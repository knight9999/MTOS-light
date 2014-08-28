<?php

namespace MT;

class Object {
	
	private static $pre_init_props = null; // TODO
	public static $properties = null;
	public static function install_properties($props) {
		$super_props = parent::$properties;
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
		}		
	}
}


?>
