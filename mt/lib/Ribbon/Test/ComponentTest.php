<?php 

require_once __DIR__ . "/../StaticBehavior.php";
require_once __DIR__ . "/../Component.php";

class ChildComponent extends \Ribbon\Component {
	
}

class Behavior1 extends \Ribbon\StaticBehavior {
	public static function hello() {
		return "Hello";
	}
}

class Behavior2 extends \Ribbon\StaticBehavior {
	public static function goodbye() {
		return "Good-Bye!";
	}
}

class Behavior3 extends \Ribbon\StaticBehavior {
	public static function message() {
		return "Message!";
	}
}


class ComponentTest extends PHPUnit_Framework_testCase {
	
	public function testExtends() {
		\Ribbon\Component::addBehavior("Behavior1");
		\Ribbon\Component::addBehavior("Behavior2");

		$data = \Ribbon\Component::getBehaviors();
		// var_dump( $data );
		$this->assertEquals( count($data) , 2 );
				
		ChildComponent::addBehavior("Behavior3");
		
		$data = \Ribbon\Component::getBehaviors();
		//	var_dump( $data );
		$this->assertEquals( count($data) , 2 );
		
		$data = ChildComponent::getBehaviors();
		// var_dump( $data );
		$this->assertEquals( count($data) , 1 );
		
		$this->assertTrue(true);
		
		$message = ChildComponent::message();
		$this->assertEquals( $message , "Message!" );
	}
}

?>
