<?php 



namespace Ribbon;

// http://qiita.com/misogi@github/items/8d02f2eac9a91b4e6215

class ClassLoader {
	
	public static function loadClass($class) {
		if (preg_match( "/Ribbon\\\\(.+)$/" , $class, $matches ) ) {
			$namespace = $matches[1];
			$list = explode( "\\" , $namespace );
			$path = __DIR__ . "/" .  implode( "/" , $list ) .  ".php";
			if (is_file($path)) {
				require $path;
				return true;
			}
		} 
		return false;
	}
	
}

?>