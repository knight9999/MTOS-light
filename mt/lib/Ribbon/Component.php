<?php 

namespace Ribbon;

class Component {
	public static $_m = null; // TODO いずれmapにする

	public static function addBehavior( $name , $behavior ) {
		$class = get_called_class();
		if ( !isset(static::$_m)) {
			$m = new Map();
		}
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Map(); // TODO いずれmapにする
		}
		static::$_m[$class][$name] = $behavior;
	}
	
	public static function getBehaviors() {
		$class = get_called_class();
		if ( !isset(static::$_m)) {
			$m = new Map();
		}
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Map(); // TODO いずれmapにする
		}
		return static::$_m[$class];
	} 
	

	public static function add_trigger( $key , $method ) {
	
	}
	
	public static function call_trigger( $key ) {
	
	}
	
	
	
}

?>