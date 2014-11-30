<?php 

namespace Ribbon;

class DynamicScope { // シングルトンで実装する。
	
	public static $variables = null;
	
	public static function beginLocal() {
		if (! isset(static::$variables)) {
			static::$variables = new Vector();
		}
		static::$variables->add( new Map() );
	}
	
	public static function endLocal() {
		if (! isset(static::$variables)) {
			die("Error at DynamcalScope");
		}
		static::$variables->removeAt( static::$variables->count() - 1 );
	}
	
	public static function set($key,$val) {
		static::$variables[ static::$variables->count() - 1 ]->add( $key , $val );
	}
	
	public static function get($key) {
		for ($i=static::$variables->count()-1 ; $i>=0 ; $i-- ) {
			if ( static::$variables[ $i ]->contains( $key ) ) {
				return static::$variables[ $i ][$key];
			}
		}		
		return null;
	}
}

?>