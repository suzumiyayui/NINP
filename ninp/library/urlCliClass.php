<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class urlCliClass {

    public function __construct() {
        
    }

    public function run($ControllerName, $MethodName, $ParameterName) {

        require_once 'system/ninpController.php';

        $controller = self::request_controller($ControllerName);
        
        $method = $MethodName;

        $parameter = $ParameterName;

        if (method_exists($controller, $method)) {

            $controller->$method($parameter);
        } else {

            exit("Ninp: function '$method'  No found");
        }
    }
    
    
    
    
    

    function request_controller($ControllerName_base) {

        $controllerName = $ControllerName_base . "controller";

        $controllerPath = 'controller/' . $ControllerName_base . "Controller.php";

        if (!self::check_request_file($controllerPath))
            exit("Ninp: Controller Error");

        require_once $controllerPath;

        $controller = new $controllerName;

        return $controller;
    }

    function check_request_file($file_name) {

        return file_exists($file_name);
    }

}
