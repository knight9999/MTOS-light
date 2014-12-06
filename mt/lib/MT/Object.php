<?php

namespace MT;

use \Ribbon\Map;
use \Ribbon\Vector;

// require_once __DIR__ . "/../Ribbon/Map.php";
// require_once __DIR__ . "/../Ribbon/Vector.php";


require_once __DIR__ . "/../MT.php";
require_once __DIR__ . "/Base/Util.php";
require_once __DIR__ . "/Base/Component.php";

class Object extends Base\Component {
	
	private static $pre_init_props = null; // TODO
	public static $properties = null;
	public static function install_properties($props) {
		$class = get_called_class();
		// TODO plugins_installedチェック＆処理
		$meta = new Map();
		$summary = new Map();
		
		$super_props = null;
		if ( isset( parent::$properties ) ) { 
			$super_props = parent::$properties;
		}
		
		foreach ( array("meta","summary") as $which) {
			if ($super_props && $super_props[$which]) {
				$props[$which] = 1 ;
			}
		}
		
		if ($props['meta']) {
			# yank out any meta columns before we start working on column_defs
			foreach ( $props["column_defs"] as $key ) {
				if (preg_match("/\\bmeta\\b/",$props["column_defs"][$key])) {
					$meta[$key] = $props["column_defs"]->remove($key);
				}
			}
		}
		
		if ($super_props) {
      # subclass; merge hash
			foreach ( array("primary_key","class_column","datasource","driver","audit") as $key) {
				if ($super_props->contains($key) && !$props->contains($key) ) {
					$props[$key] = $super_props[$key];
				}
			}
			foreach ( array("column_defs","defaults","indexes") as $p) {
				if ($super_props->contains($p)) {
					foreach( array_keys( $super_props[$p] ) as $k ) {
						if (! $props[$p]->contains($k) ) {
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
		$props["columns"] = new Vector( $props["column_defs"]->getKeys() );
		
		if ($props["audit"]) {
			if ( ! $props["column_defs"]->contains("created_on") ) {
				$props["column_defs"]["created_on"] = "datetime";
				$props["column_defs"]["created_by"] = "integer";
				$props["column_defs"]["modified_on"] = "datetime";
				$props["column_defs"]["modified_by"] = "integer";
				$props["columns"]->add("created_on");
				$props["columns"]->add("created_by");
				$props["columns"]->add("modified_on");
				$props["columns"]->add("modified_by");
			}
		}
		
		# Classed object types
		if ( $props->contains("class_type") ) {
			$props["class_column"] = $props->contains("class_column")  ? $props["class_column"] : "class";
		}
		if ( $col = $props["class_column"] ) {
			if ( ! $props["column_defs"][$col] ) {
				$props["column_defs"][$col] = "string(255)";
				$props["columns"]->add( $col );
				$props["indexes"][$col] = 1;
			}
			if ( !$super_props || !$super_props["class_column"]) {
			  $class::add_trigger(
			  	"pre_search" , "_pre_search_scope_terms_to_class" 
			  );
			  $class::add_trigger(
			  	"post_load" , "_post_load_rebless_object" 
			  );
			  $class::add_trigger(
			  	"post_inflate" , "_post_load_rebless_object" 
			  );
			}
			if ( $type = $props["class_type"] ) {
				$props["defaults"][$col] = $type;
				if (! $props->contains("__class_to_type")) {
					$props["__class_to_type"] = new Map();
				}
				$props["__class_to_type"][$class] = $type;
				if (! $props->contains("__type_to_class")) {
					$props["__type_to_class"] = new Map();
				}				
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
 		
 		if ( $props->contains("summary") ) {
 			$type_summaries = \MT::registry( 'summaries' , $type_id );
			$summary = new Map(); 			
 			foreach ( $type_summaries->getKeys() as $_) {
 				$summary[$_] = preg_match( "/(string|integer)/" , $type_summaries[$_]["type"] , $matches) ? 
 					$matches[1] . " indexed meta" : $type_summaries[$_]["type"] . " meta";
 			}
 		}
 		$props["get_driver"] = $props->contains("get_driver") ? 
 			$props["get_driver"] : function() { return \MT\ObjectDriverFactory::instance(); };
 		
		if ( method_exists( get_parent_class( get_called_class() ) , "install_properties") ) {
			parent::install_properties( $props );
		}
	
		# check for any supplemental columns from other components
		$more_props = \MT::registry( 'object_types' , $type_id );
		if ( isset( $more_props ) && ($more_props instanceof Vector ) ) {
			$cols = new Map();
			foreach ($more_props as $prop) {
				if ( ! ($prop instanceof Map) ) {
					continue;
				}
				\MT::__merge_hash( $cols , $prop , 1 );				
			}
			$classes = new Vector();
			foreach ($more_props as $_) {
				if (is_string($_)) {
					array_push( $classes , $_ );
				}
			}
			foreach ($classes as $isa_class) { 
				if ($class::getBehaviors()->contains($isa_class)) {
					continue;
				}
				$class::addBehavior(  $isa_class );
				#TODO AUTOLOAD
			} 
			if ($cols) {
				if ($cols->contains("plugin")) {
					$cols->remove("plugin");
				}
				foreach( $cols->getKeys() as $name ) {
					if ( $props["column_defs"]->contains($name) ) {
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
	
		$pk = $props->contains("primary_key") ? $props["primary_key"] : "";
		usort( $props["columns"]->_d , function($a,$b) use ($pk) { return $a == $pk ? -1 : ( $b == $pk ? 1 : - ( $a < $b ) ); } );

		# Child classes are declared as an array;
		# convert them to a hashref for easier lookup.
		if ( $props["child_classes"] instanceof Vector ) {
			$classes = $props["child_classes"];
			$props["child_classes"] = new Vector();
			foreach ($classes as $c) {
				$props["child_classes"][$c] = new Vector();
			}
		}
		# We're declared as a child of some other class; associate ourselves
		# with that package (the invoking class should have already use'd it.)
		if ( $props->contains("child_of") ) {
			$parent_classes = $props["child_of"];
			if (! ($parent_classes instanceof Vector || $parent_classes instanceof Map) ) {
				$parent_classes = new Vector( array( $parent_classes ) );
			}
			foreach ($parent_classes as $pc) {
				$pp = $pc::$properties;
				$pp["child_classes"] = $pp->contains("child_classes") ? $pp["child_classes"] : new Vector();
				$pp["child_classes"][$class] = new Vector();
			}
		}
		  # line 235 @ original
    	# Special handling for 'Taggable' objects; automatic saving
    	# and removal of tags.
		
		// TODO
		// Taggableクラスを継承している場合は、それがinstall_propertiesが出来る場合は、Taggableクラスに
		// 現在のクラスをinstall_propertiesする。
		// PHPでは、単一継承なので、このままの実装は出来ないので、とりあえず割愛。StaticBehaviorで対応できる場合は、
		// それで実装する(クラスの継承関係を、整理して、考える必要あり）
		
		# line 250 @ original
		# install legacy date translation
		// 割愛
		
		# line 264 @ original
    # Treat blank string with number field
    $class::add_trigger( "pre_save" , array( __NAMESPACE__ . "\\" . get_called_class() , "_translate_numeric_fields" ) );
		
    # inherit parent's metadata setup
    if ( $props->contains("meta") ) {
    	# if ($super_props && $super_props->{meta_installed}) {
    	$class::install_meta(
  			$meta->isEmpty() ? new Map(array( "columns" => new Vector() )) : new Map(array( "column_defs" => $meta) )  	
	    	, "meta"
    	);
    	$class::add_trigger( "post_remove" , array( __NAMESPACE__ . "\\" . get_called_class() ,  "remove_meta" ) );
    }
    if ( $props->contains("summary")) {
    	$class::install_meta(
    		$summary->isEmpty() ? new Map(array( "columns" => new Vector() )) : new Map(array( "column_defs"=>$summary ))
    	);
    }
    
    # line 287 @ original
    # Because of the inheritance of MT::Entry by MT::Page, we need to do this here
    
    if ( $class::hasBehavior('MT\Revisable') ) {
    	$class::init_revisioning();
    }
    
    $enc = isset( \MT::config()->PublishCharset ) ? \MT::config()->PublishCharset : 'UTF-8';
    
    # original line 294
    # install these callbacks that is guaranteed to be called
    # at the very last in the callback list to encode everything.
    
    $class::add_trigger(
    	'__core_final_pre_save', function( $original ) {
    		$dbd = $this->driver->dbd;
    		if (! $dbd->need_encode) {
    			return;
    		}
    		$data = $obj->get_values;
    		foreach ( $data as $key => $value ) {
    			// TODO エンコード処理
    		}
    		$this->set_values( $data , new Map(array( "no_changed_flag" => 1)) );
	    }
    );
    
    return $props;
    
    
	}
	
	public static function install_meta($params,$which = null) {
		$class = get_called_class();
		if (! isset( $which) ) {
			$which = 'meta';
		}
//		if ( ( $class != 'MT\Config' ) && ( ! MT::$plugins_installed) ) { // Dinamic Variables;
			// TODO
			// push @PRE_INIT_META, [ $class, $params, $which ];
//			return;
//		}
		// TODO
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
