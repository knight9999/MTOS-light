<?php 

require_once __DIR__ . "/MT/Base/Component.php";

class MT extends \MT\Base\Component {
	
	public static function registry( $name , $type_id ) {
		# TODO
		return array();
	}
	
	public static function __merge_hash(&$h1,&$h2,$replace) {
		foreach ( array_keys($h2) as $k ) {
			if ( isset($h1[$k]) && (! $replace )) {
				if ( is_array($h1[$k]) ) {
					__merge_hash( $h1[$k] , $h2[$k] , 1 );
				} elseif ( $h1[$k] ) {
					
				} else {
					$h1[$k] = array( $h1[$k] , $h2[$k] );
				}
			} else {
				$h1[$k] = $h2[$k];
			}
		}
	}
}

?>