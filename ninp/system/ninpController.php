<?php

require_once 'library/loginClass.php';


class ninpcontrooler extends loginclass {
    
    
    //Load函数

    protected function load($folder = NULL, $file = NULL) {

        if ($folder == NULL || $file == NULL)
            exit("null loader");

        $filePath = $folder . "/" . $file . "Class.php";

        $fileName = $file . "class";

        if (!self::check_request_file($filePath))
            exit("null loader");

        require_once $filePath;

        return new $fileName;
    }
    
    //DB 加载
    protected function db_init() {

        $DB = self::load('library', 'DB');
        $DB->db_con(DB_HOST,DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        
        return $DB;
    }
    
    
    protected function redis_init(){
        

       if(class_exists ('Redis')){
        
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        
        return $redis;
       }  else {
       
        echo "No Redis Class";   
       }
        
    }
    
   
    
    

    
    //检查是否有文件
    protected function check_request_file($file_name) {

        return file_exists($file_name);
    }

    

}
