<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class welcomecontroller extends ninpcontrooler {

    var $DB;
    var $redis;

    function __construct() {

        parent::__construct();

        $this->DB = self::db_init();
        
       // $this->redis = self::redis_init();
    }
    

    public function index() {

        
        $get= self::s_get();
        
        print_r($get);
        
        
//        $sql = "select * from food";
//
//        $info = $this->DB->db_all($sql);
//
//        print_r($info);
    }
    
    
    
    
    public function test($param) {
        
        
        
    }

}
