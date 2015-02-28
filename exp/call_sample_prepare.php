<?php 

namespace XX;

class A {
	
	public function myclass() {
		print "this is " . get_called_class() . "\n";
	}
	
	static public function hoge() {
		print "HOGE\n";
	}

  static public function install() {
  	echo "--- ".get_called_class()."---\n";
  	$trigger = array(get_called_class(),"hoge" );
  	call_user_func_array( $trigger , array() );
  }
}

class B extends A {
	
}

class C extends B {
	static public function hoge() {
		print "FOO\n";
	}
}

?>
