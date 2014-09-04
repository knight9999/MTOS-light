<?php 

require_once __DIR__ . "/../Component.php";

class ChildComponent extends \Ribbon\Component {
	
}



class ComponentTest extends PHPUnit_Framework_testCase {
	
	public function testExtends() {
		\Ribbon\Component::addBehavior("key1", "val1");
		\Ribbon\Component::addBehavior("key2", "val2");

		$data = \Ribbon\Component::getBehaviors();
		// var_dump( $data );
		$this->assertEquals( count($data) , 2 );
		
		
		ChildComponent::addBehavior("ckey1", "cval1");
		
		$data = \Ribbon\Component::getBehaviors();
		//	var_dump( $data );
		$this->assertEquals( count($data) , 2 );
		
		$data = ChildComponent::getBehaviors();
		// var_dump( $data );
		$this->assertEquals( count($data) , 1 );
		
		$this->assertTrue(true);
	}
}

?>
