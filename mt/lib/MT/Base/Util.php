<?php 

namespace MT\Base;

class Util {
	
	public static function is_hash(&$array) {
		if (! is_array($array)) {
			return false;
		}
		
		$i = 0;
		foreach($array as $k => $dummy) {
			if ( $k !== $i++ ) return true;
		}
		return false;
	}
	
	public static function is_array(&$array) {
		if (! is_array($array)) {
			return false;
		}
		return ! self::is_hash($array);
	}
	
}


?>