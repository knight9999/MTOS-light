<?php 

require_once __DIR__ . "/MT/Base/Component.php";

class MT extends \MT\Base\Component {
	
	public static function registry( $name , $type_id ) {
		# TODO
		return array();
	}
	
	public static function __merge_hash(&$h1,&$h2,$replace) {
		foreach ( $h2->getKeys() as $k ) {
			if ( $h1->contains($k) && (! $replace )) {
				if ( $h1[$k] instanceof Map ) {
					$replace2 = $replace;
					if ($replace2 == null) {
						$replace2 = 0;
					}
					__merge_hash( $h1[$k] , $h2[$k] , $replace2 + 1 );
				} elseif ( $h1[$k] instanceof Vector ) {
					if ( $h2[$k] instanceof Vector) {
						$h1[$k] = array_merge( $h1[$k] , $h2[$k] );
					} else {
						$h1[$k]->add( $h2[k] );
					}
				} else {
					$h1[$k] = new Vector( array( $h1[$k] , $h2[$k] ) );
				}
			} else {
				$h1[$k] = $h2[$k];
			}
		}
	}
}

?>