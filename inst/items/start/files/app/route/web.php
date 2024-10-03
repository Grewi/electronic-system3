<?php 
use electronic\core\route\route;
use {namespace}\controllers\error\error;

if(!defined('ADMIN')){
    define('ADMIN', 'admin');
}

$route  = new route();

// $route->filter('bruteforce');

$route->autoloadWeb();

$error = (new error())->error404();