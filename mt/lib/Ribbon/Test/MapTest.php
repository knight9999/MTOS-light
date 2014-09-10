<?php 

require_once __DIR__ . "/../Component.php";
require_once __DIR__ . "/../Map.php";

class MapTest extends PHPUnit_Framework_testCase {

	public function testBasic() {

		$m = new \Ribbon\Map( array( "name" => "Ribbon" , "lang" => "PHP" , "date" => "2014/09/07") );
		
		$this->assertEquals( count ($m ) , 3 );
		$this->assertEquals( $m["name"] , "Ribbon" );

		$v = $m->remove("lang");
		$this->assertEquals( $v , "PHP" );
		
		$this->assertEquals( count ($m) , 2 );
		
		$m["type"] = "Framework";
		
		$this->assertEquals( count($m) , 3 );
	}
	
}

?>