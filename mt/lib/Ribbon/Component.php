<?php 

namespace Ribbon;

require_once __DIR__ . "/Map.php";
require_once __DIR__ . "/Vector.php";

class Component {
	public static $_m = null;

	public static function staticInit() {
		if ( ! isset(static::$_m) ) {
			static::$_m = new Map();
		}
	}
	
	public static function __callStatic($name, array $arguments ) {
		$result = null;
		$flag_run = false;
		$class = get_called_class();
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Vector();
		}
		foreach( static::$_m[$class] as $k) {
			if (method_exists( $k , $name )) {
				$result = forward_static_call_array( array( $k , $name ) , $arguments );
				$flag_run = true;
				break;
			}
		}
		if (! $flag_run) {
			throw "No Static Behavior Error"; // TODO
		}
		return $result;
	}
	
	public static function addBehavior( $name ) {
		$class = get_called_class();
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Vector();
		}
		static::$_m[$class][] = $name;
	}
	
	public static function getBehaviors() {
		$class = get_called_class();
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Vector(); 
		}
		return static::$_m[$class];
	} 
	

	public static function add_trigger( $key , $method ) {
	
	}
	
	public static function call_trigger( $key ) {
	
	}
	
	
	
}

Component::staticInit();

?>