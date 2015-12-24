<?php

class welcomecontroller extends ninpcontrooler {

    private $Model;

    function __construct() {

        parent::__construct();
        
        self::setCookieKey("ADBCDE");
        
        if(!$this->Model)$this->Model = self::load('model','data'); //加载模板
        
    }
    

    public function index() {
              
        
        $post = self::s_post();
        
        
        $get  = self::s_get('uid');
        

                 
        $this->Model->s_data();
       
        
        
       
    }

    
    
    
    public function set_serCookie($param) {
        
   
        $array = array("uid"=>"25");
        
        self::setServerSession('login',$array);
         
        
    }
    
    
    public function get_serCookie($param){
        
        
        $code = self::getServerSession('login');
        
        print_r($code);
        
        
    }

}
