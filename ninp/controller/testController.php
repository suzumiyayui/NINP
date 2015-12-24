<?php



class testcontroller extends ninpcontrooler{
    
    public function index(){
        
        self::setCookieKey('sssss');
        
        self::setServerSession('uid','5');
        
        echo $name = self::getServerSession('uid');
        
        self::load();
        
    }
    
}