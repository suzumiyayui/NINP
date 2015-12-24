<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class urlClass {

    function __construct() {
        
    }

    function run() {

        require_once 'system/ninpController.php';


        $controller = self::request_controller();

        $method = self::request_method();

        $parameter = self::request_parameter();

        $controller->$method($parameter);
    }

    function request_controller() {

        if (isset($_SERVER['PATH_INFO']))
            $url = explode("/", $_SERVER['PATH_INFO']);

        if (!isset($url[1]) || $url[1] == NULL)
            $url[1] = DEFINE_CONTROLLER;

        $controllerName = $url[1] . "controller";

        $controllerPath = 'controller/' . $url[1] . "Controller.php";

        if (!self::check_request_file($controllerPath))
            exit("Controller Error");

        require_once $controllerPath;

        $controller = new $controllerName;

        return $controller;
    }

    function request_method() {

        if (isset($_SERVER['PATH_INFO']))
            $url = explode("/", $_SERVER['PATH_INFO']);

        if (isset($url[2])) {

            $url[2] != NULL ? $method = $url[2] : $method = DEFINE_METHOD;
        } else {

            $method = DEFINE_METHOD;
        }



        return $method;
    }

    function request_parameter() {

        $parameter = '';

        if (isset($_SERVER['PATH_INFO']))
            $url = explode("/", $_SERVER['PATH_INFO']);

        for ($num = 3; $num < 100; $num++) {

            if (isset($url[$num])) {

                $parameter ? $parameter = $parameter . "," . $url[$num] : $parameter = $parameter . $url[$num];
                
            } else {

                break;
            }
        }

        

        return $parameter;
    }

    function check_request_file($file_name) {

        return file_exists($file_name);
    }

}
