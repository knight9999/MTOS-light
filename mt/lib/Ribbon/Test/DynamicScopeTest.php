<?php 

require_once __DIR__ . "/../ClassLoader.php";
spl_autoload_register( array('\Ribbon\ClassLoader','loadClass'));

class DynamicScopeTest extends PHPUnit_Framework_testCase {
	
	public function testScope() {
		Ribbon\DynamicScope::beginLocal();
		Ribbon\DynamicScope::set("var1","ok");
		$val = Ribbon\DynamicScope::get("var1");
		$this->assertEquals( "ok" , $val );
		
		Ribbon\DynamicScope::beginLocal();
		Ribbon\DynamicScope::set("var1","hello");
		$val = Ribbon\DynamicScope::get("var1");
		$this->assertEquals( "hello" , $val );
		Ribbon\DynamicScope::endLocal();
		
		$val = Ribbon\DynamicScope::get("var1");
		$this->assertEquals( "ok" , $val );
		
		Ribbon\DynamicScope::endLocal();
	}
}

?>