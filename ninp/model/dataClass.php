<?php

class dataclass extends ninpcontrooler {
    
   
    private $DB;
    private $redis;


    function __construct() {

        parent::__construct();

         if(!$this->DB)$this->DB = self::db_init();

         if(!$this->redis)$this->redis = self::redis_init();
       
         header('Content-type: application/json');
        
    }

    
    public function s_data(){
        
              
        $sql = "select * from net_log order by id DESC limit 50";

        $info = $this->DB->db_all($sql);

        print_r($info);
        
        
        self::set_redis();
        
        
    }
    
    
    public function set_redis($key,$str){
        
    	$redis_key  = REDIS_PREFIX.$key;
        
        $this->redis->setex($redis_key,20,$str);
        
        
    }
    
    
    public function get_redis($key){
    	    	
    	$redis_key  = REDIS_PREFIX.$key;
    	
    	echo $this->redis->get($redis_key);
   
    	
    }
    
    
    
    
    
    
    
    
}
