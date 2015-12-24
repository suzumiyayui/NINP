<?php

class hicontroller extends ninpcontrooler {
	

	
	
	function you(){
		
		
		$tem = json_encode(array('uuuu'=>'xxxxx'));		
  
		$o = self::json_to_array($tem);
		
		print_r($o);
	
		
		
	}
	
	
	
	
	
	
	
	
}


