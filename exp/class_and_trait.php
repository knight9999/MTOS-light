<?php 

trait AT {
	
	public function print2() {
		echo "print2 \n";
	}
	
}

trait BT {
	
	
}

class A {
	use AT;
	use BT;
	
	public function print1() {
		echo "print1 \n";
	}
	
}

class B extends A {
	
}

class C extends B {
	
}

$a = new A();

$a->print1();

$a->print2();

print_r( array_keys( class_uses("A") ) );

print_r( array_keys( class_uses("B") ) ) ;

print_r( array_keys( class_parents("B") ) );

print_r( array_keys( class_parents("C") ) );



?>