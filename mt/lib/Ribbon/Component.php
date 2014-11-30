<?php 

namespace Ribbon;

require_once __DIR__ . "/Map.php";
require_once __DIR__ . "/Vector.php";

class Component {
	public static $_m = null;
	public static $_t = null;
	
	public static function staticInit() {
		if ( ! isset(static::$_m) && class_exists("Ribbon\Map") ) {
			static::$_m = new Map();
			static::$_t = new Map();
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
		if ( !isset(static::$_m)) {
			$m = new Map();
		}
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Vector();
		}
		static::$_m[$class][] = $name;
	}
	
	public static function getBehaviors() {
		$class = get_called_class();
		if ( !isset(static::$_m)) {
			$m = new Map();
		}
		if ( ! isset(static::$_m[$class]) ) {
			static::$_m[$class] = new Vector(); 
		}
		return static::$_m[$class];
	} 
	
	public static function hasBehavior( $name ) {
		foreach (static::getBehaviors() as $behavior) {
			if ($behavior == $name) {
				return true;
			}
		}
		return false;
	}
	

	public static function add_trigger( $key , $method ) {
		$class = get_called_class();
		if ( !isset(static::$_t)) {
			$t = new Map();
		}
		if ( !isset(static::$_t[$class])) {
			static::$_t[$class] = new Map();
		}
		static::$_t[$class][$key] = $method; 
	}
	
	public static function call_trigger( $key , $param_array ) {
		$class = get_called_class();
		if ( !isset(static::$_t)) {
			$t = new Map();
		}
		return call_user_func_array( static::$_t[$class][$key] , $param_array );
	}
	
//	namespace Foobar;
//	
//	class Foo {
//		static public function test() {
//			print "Hello world!\n";
//		}
//	}
//	
//	call_user_func(__NAMESPACE__ .'\Foo::test'); // PHP 5.3.0 以降
//	call_user_func(array(__NAMESPACE__ .'\Foo', 'test')); // PHP 5.3.0 以降
	
}

Component::staticInit();

?>