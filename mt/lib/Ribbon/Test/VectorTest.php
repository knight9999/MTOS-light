<?php 

require_once __DIR__ . "/../Component.php";
require_once __DIR__ . "/../Vector.php";

class VectorTest extends PHPUnit_Framework_testCase {
	
	public function testBasic() {
		$v = new \Ribbon\Vector( array( 1,2,3,4,5 ) );
		
		$this->assertEquals( count( $v ) , 5 );
		
		$this->assertEquals( $v[2] , 3 );
		
		$v->removeAt(3);
		
		$this->assertEquals( count( $v ) , 4);
		
		$this->assertEquals( $v[3] , 5 );
		
		$v->insertAt(2,10);
		
		$this->assertEquals( count( $v) , 5);

		$this->assertEquals( $v[3] , 3 );
		
		$this->assertEquals( $v[2] , 10 );
		
		$a = array( 11,12,13 );
		
		$v->mergeWith( $a );
		
		$this->assertEquals( count($v) , 8 );
		
		$this->assertEquals( $v[6] , 12 );
		
		$v->clear();
		
		$this->assertEquals( count($v) , 0 );
		
	}
}

?>