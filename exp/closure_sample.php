<?php 

function makeClosure( $a , $b ) {
	
	$retval = function( $name ) use ($a,$b) {
		return $name . " " . $a . " " . $b;
		
	};
	return $retval;
}

$x = makeClosure( "ok" , "or not" );
print $x("book is ");


?>