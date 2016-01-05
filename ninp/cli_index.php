<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'config/config.php';

require_once 'library/urlCliClass.php';

$NINP = new urlCliClass;


if (isset($argv[1]) && isset($argv[2])) {


    $Controller = $argv[1];

    $Method = $argv[2];
   
    (isset($argv[3]))?$parameter = $argv[3]:$parameter = null;


} else {

    exit("request no parameter");
}








$NINP->run($Controller,$Method,$parameter);
