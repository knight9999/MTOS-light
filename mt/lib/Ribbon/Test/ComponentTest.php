<?php 

// require_once __DIR__ . "/../StaticBehavior.php";
// require_once __DIR__ . "/../Component.php";

require_once __DIR__ . "/../ClassLoader.php";

spl_autoload_register( array('\Ribbon\ClassLoader','loadClass'));

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

class TriggerSample extends \Ribbon\Component {
	public function setTrigger() {
		$this->add_trigger( "hoge" , array(__NAMESPACE__."\TriggerSample" , "hoge") );
		$this->add_trigger( "hoge2" , array( get_called_class() , "hoge2") );
		$this->add_trigger( "hoge3" , function( $name ) {  return "My name is " . $name . "\n"; } );
		
	}
	
	public static function hoge($title,$num) {
		return $title . " is hoge " . $num;
	}

	public static function hoge2($title,$num) {
		return $title . " is hoge2 " . $num;
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
		
		$message = ChildComponent::message();
		$this->assertEquals( $message , "Message!" );
	
		
		$flag1 = ChildComponent::hasBehavior( "Behavior3");
		$this->assertTrue( $flag1 );
		
		$flag2 = ChildComponent::hasBehavior( "Behavior2");
		$this->assertFalse( $flag2 );		
	}
	
	public function testTrigger() {
		$sample = new TriggerSample();
		$sample->setTrigger();
		$retval = $sample->call_trigger( "hoge" , array("foo",4));
		$this->assertEquals( "foo is hoge 4",$retval);
		
		$str = "TriggerSample";
		$retval = $str::call_trigger( "hoge" , array("munyu",5));
		$this->assertEquals( "munyu is hoge 5",$retval);

		$str = "TriggerSample";
		$retval = $str::call_trigger( "hoge2" , array("munyu",5));
		$this->assertEquals( "munyu is hoge2 5",$retval);
		
		$retval = $str::call_trigger( "hoge3" , array("hoge3" ) );
		$this->assertEquals( "My name is hoge3\n",$retval);
		
	}
}

?>
