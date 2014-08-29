<?php

namespace MT;

require_once __DIR__ . "/Base/Component.php";

class Object extends Base\Component {
	
	private static $pre_init_props = null; // TODO
	public static $properties = null;
	public static function install_properties($props) {
		$class = get_called_class();
		// TODO plugins_installedチェック＆処理
		
		$super_props = null;
		if ( get_parent_class($class) != "MT\\Base\\Component" ) {
			$super_props = parent::$properties;
		}
		if ($super_props) {
			foreach ( array("primary_key","class_column","datasource","driver","audit") as $key) {
				if ($super_props[$key] && !$props[$key]) {
					$props[$key] = $super_props[$key];
				}
			}
			foreach ( array("column_defs","defaults","indexes") as $p) {
				if ($super_props[$p]) {
					foreach( array_keys( $super_props[$p] ) as $k ) {
						if (! $props[$p][$k] ) {
							$props[$p][$k] = $super_props[$p][$k];
						}
					}
					if ($p == 'column_defs') {
						static::__parse_defs( $props['column_defs'] );
					}
				}
			}
			if ( $super_props["class_type"]) {
				$props["__class_to_type"] = $super_props["__class_to_type"];
				$props["__type_to_class"] = $super_props["__type_to_class"];
			}
		}		
		# TODO Legacy MT::Object proc
		$props["columns"] = array_keys( $props["column_defs"] );
		
		if ($props["audit"]) {
			if ( ! isset( $props["column_defs"]["created_on"]) ) {
				$props["column_defs"]["created_on"] = "datetime";
				$props["column_defs"]["created_by"] = "integer";
				$props["column_defs"]["modified_on"] = "datetime";
				$props["column_defs"]["modified_by"] = "integer";
				array_push( $props["columns"] , "created_on" , "created_by" , "modified_on" , "modified_by" );
			}
		}
		
		# Classed object types
		if ( isset( $props["class_type"] ) ) {
			$props["class_column"] = isset( $props["class_column"] ) ? $props["class_column"] : "class";
		}
		if ( $col = $props["class_column"] ) {
			if ( ! isset( $props["column_defs"][$col] ) ) {
				$props["column_defs"][$col] = "string(255)";
				array_push( $props["columns"] , $col );
				$props["indexes"][$col] = 1;
			}
			# TODO add_triggers
			if ( $type = $props["class_type"] ) {
				$props["defaults"][$col] = $type;
				$props["__class_to_type"][$class] = $type;
				$props["__type_to_class"][$type] = $class;
			}
		}
		
		if ($type_id = $props["class_type"]) {
			if ( $type_id != $props["datasource"] ) {
				$type_id = $props["datasource"] . "." . $type_id;
			}
 		} else {
 			$type_id = $props->{datasource};
 		}
 		
// 		var_dump( $props ); # debug
	}
	
	public static function __parse_defs($defs) {
		foreach( array_keys( $defs ) as $col ) {
			if (is_array($defs[$col])) {
				continue;
			}
			$defs[$col] = static::__parse_def( $col , $defs[$col] );
		}
	}
	
	public static function __parse_def($col,$def) {
		if (! isset($def)) {
			return null;
		}
		$props = static::properties;
		$hdef = array();
		$pattern = "/^([^( ]+)\\s*/";
		if (preg_match($pattern,$def,$matches)) {
			$hdef["type"] = $matches[1];
			$def = preg_replace($pattern,"",$def);
		}
		$pattern = "s/^\((\\d+)\\)\\s*//";
		if (preg_match($pattern,$def,$matches)) {
			$hdef["size"] = $matches[1];
			$def = preg_replace($pattern,"",$def);
		}
		if (preg_match("/\\bnot null\\b/i",$def)) {
			$hdef["not_null"] = 1;
		}
		if (preg_match("/\\bprimary key\\b/i",$def)) {
			$hdef["key"] = 1;
		}
		if ($props["primary_key"] && $props["primary_key"] == $col) {
			$hdef["key"] = 1;
		}
		if (preg_match("/\\bauto[_ ]increment\\b/i",$def)) {
			$hdef["auto"] = 1;
		}
		if (preg_match("/\\brevisioned\\b/i",$def)) {
			$hdef["revisioned"] = 1;
		}
		if (isset( $props["defaults"][$col] )) {
			$hdef["default"] = $props["defaults"][$col];
		}
		return $hdef;
	}
	
}


?>
