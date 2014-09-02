<?php

namespace MT;

require_once __DIR__ . "/../MT.php";
require_once __DIR__ . "/Base/Util.php";
require_once __DIR__ . "/Base/Component.php";

class Object extends Base\Component {
	
	private static $pre_init_props = null; // TODO
	public static $properties = null;
	public static function install_properties($props) {
		$class = get_called_class();
		// TODO plugins_installedチェック＆処理
		$meta = array();
		$summary = array();
		
		$super_props = null;
		if ( isset( parent::$properties ) ) { 
			$super_props = parent::$properties;
		}
		
		foreach ( array("meta","summary") as $which) {
			if ($super_props && $super_props[$which]) {
				$props[$which] = 1 ;
			}
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
 		
 		if ( isset( $props["summary"] ) ) {
 			$type_summaries = \MT::registry( 'summaries' , $type_id );
			$summary = array(); 			
 			foreach (array_keys( $type_summaries ) as $_) {
 				$summary[$_] = preg_match( "/(string|integer)/" , $type_summaries[$_]["type"] , $matches) ? 
 					$matches[1] . " indexed meta" : $type_summaries[$_]["type"] . " meta";
 			}
 		}
 		$props["get_driver"] = isset( $props["get_driver"]) ? 
 			$props["get_driver"] : function() { return \MT\ObjectDriverFactory::instance(); };
 		
		if ( method_exists( get_parent_class( get_called_class() ) , "install_properties") ) {
			parent::install_properties( $props );
		}
	
		# check for any supplemental columns from other components
		$more_props = \MT::registry( 'object_types' , $type_id );
		if ( isset( $more_props ) && \MT\Base\Util::is_array( $more_props ) ) {
			$cols = array();
			foreach ($more_props as $prop) {
				if ( ! \MT\Base\Util::is_hash($prop) ) {
					continue;
				}
				\MT::__merge_hash( $cols , $prop , 1 );				
			}
			$classes = array();
			foreach ($more_props as $_) {
				if (is_string($_)) {
					array_push( $classes , $_ );
				}
			}
			foreach ($classes as $isa_class) { 
				# $classが、$isa_classを継承したクラスの場合はスキップ。そうでない場合は、読み込む。
				# そうでない場合は、"$class::ISA"に、$isa_classを付け足す。
				# PHPの場合は、親子関係はクラスファイルに記載されているので、この処理はしないでよい？
				# requireも、autoloadの方で対応するので、しない。
			} 
			if ($cols) {
				if ($cols["plugin"]) {
					unset( $cols["plugin"] );
				}
				foreach( array_keys( $cols ) as $name ) {
					if (isset( $props["column_defs"][$name] )) {
						continue;
					}
					if ( preg_match("/\\bmeta\\b/", $cols[$name]) ) {
						$meta[$name] = $cols[$name];
						continue;
					}
					$class->install_column( $name, $cols[$name] );
					if ( preg_match("/\\bindexed\\b/",$cols[$name]) ) {
						$props["indexes"][$name] = 1;
					} 
					if ( preg_match("/\\bdefault (?:'([^']+?)'|(\\d+))\\b/",$matches) ) {
						$props["defaults"][$name] = isset( $matches[1] ) ? $matches[1] : $matches[2];
					}
					
				}
			}
		}
	
		$pk = isset( $props["primary_key"] ) ? $props["primary_key"] : "";
		usort( $props["columns"] , function($a,$b) use ($pk) { return $a == $pk ? -1 : ( $b == $pk ? 1 : - ( $a < $b ) ); } );

		# Child classes are declared as an array;
		# convert them to a hashref for easier lookup.
		if ( \MT\Base\Util::is_array( $props["child_classes"] ) ) {
			$classes = $props["child_classes"];
			$props["child_classes"] = array();
			foreach ($classes as $c) {
				$props["child_classes"][$c] = array();
			}
		}
		# We're declared as a child of some other class; associate ourselves
		# with that package (the invoking class should have already use'd it.)
		if ( isset( $props["child_of"]) ) {
			$parent_classes = $props["child_of"];
			if (! is_array( $parent_classes )) {
				$parent_classes = array( $parent_classes );
			}
			foreach ($parent_classes as $pc) {
				$pp = $pc->properties;
				$pp["child_classes"] = isset( $pp["child_classes"] ) ? $pp["child_classes"] : array();
				$pp["child_classes"][$class] = array();
			}
		}
    	# Special handling for 'Taggable' objects; automatic saving
    	# and removal of tags.
		
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
