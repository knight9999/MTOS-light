<?php 

namespace YY;

require "call_sample_prepare.php";
// class A {
	
// 	static public function hoge() {
// 		print "HOGE\n";
// 	}

//   static public function install() {
//   	echo "--- ".get_called_class()."---\n";
//   	$trigger = array(get_called_class(),"hoge" );
//   	call_user_func_array( $trigger , array() );
//   }
// }

// class B extends A {
	
// }

// class C extends B {
// 	static public function hoge() {
// 		print "FOO\n";
// 	}
// }


\XX\A::install();

\XX\B::install();

\XX\C::install();

$a = new \XX\A();
$a->myclass();


?>