<?php 

class A {
	
	public static function properties() {
		print "prop! \n";
	}

}

class B extends A {
	
	public static function check($mname) {
		$parents = class_parents( get_called_class() );
		if (count($parents)>0) {
			$parent = array_pop( $parents );
			$flag = is_callable( array( $parent , $mname ) );
			print "The " . $mname . " is " . $flag . "\n";
		}
		
	}
	
	
}

B::check("hoge");

B::check("properties");

?>