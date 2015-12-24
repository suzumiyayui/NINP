<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(0);

require_once 'class/class_login.php';

$key = "ADCCCCCCCCCCCB";
$LG = new class_login($key);
$LG->setServerSession('UID',"25"); 
$code=$LG->getServerSession('UID');


print_r($code);