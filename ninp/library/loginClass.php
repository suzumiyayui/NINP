<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'config/config.php';;
require_once 'library/baseClass.php';



class loginClass extends baseClass {

    private $CookieKey;

    function __construct() {

    }

    public function setCookieKey($CookieKey) {

        $this->CookieKey = $CookieKey;
    }

    public function setServerSession($sessionName, $content) { //      
        if ($this->CookieKey == NULL)
            exit("error1 No_Key");


        self::set_cookie($sessionName, $content);
    }

    public function getServerSession($sessionName) {

        if ($this->CookieKey == NULL)
            exit("error1 No_Key");



        return self::get_cookie($sessionName);
    }

    private function set_cookie($cookieName, $content) {

        is_array($content) ? $cookie_content = json_encode($content) : $cookie_content = $content;



        $code = self::encrypt($cookie_content, $this->CookieKey);

        setcookie($cookieName, $code);
    }

    public function get_cookie($cookieName) {

        $content = self::decrypt($_COOKIE[$cookieName], $this->CookieKey);

        self::is_json($content) ? $content = self::json_to_array($content) : $content = $content;

        return $content;
    }

}
