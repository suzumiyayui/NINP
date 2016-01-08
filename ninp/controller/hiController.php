<?php

class hicontroller extends ninpcontrooler {
	

    var $HtmlCache;
            
	
	function you(){
		
		
		$tem = json_encode(array('uuuu'=>'xxxxx'));		
  
		$o = self::json_to_array($tem);
		
		print_r($o);
	
		
		
	}
        
        
        function htmlcache(){
            
            
            $this->HtmlCache = self::load('library','htmlcache');
            
            
            $this->HtmlCache->init();
            

        }
	
	
	
	
	
	
	
	
}


