<?php 

function check_able($str) {  
	
	
	return preg_match('/able$/',$str);
}

print check_able("hogehoeg") . "\n";

print check_able("hogeable") . "\n";


?>