<?php

namespace system\core\bootstrap;

use system\core\app\app;

class bootstrap
{
    public static function load()
    {
        $app = app::app();
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $url = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'];
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

        $app->bootstrap->uri = $uri;
        $app->bootstrap->url = $url;
        $app->bootstrap->host = $host;
        $app->bootstrap->method = $method;      

        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = @$_SERVER['HTTP_CLIENT_IP'];
        } elseif (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = @$_SERVER['REMOTE_ADDR'];
        }
        $app->bootstrap->ip = $ip;


        if (isset($_SERVER['HTTP_USER_AGENT']) and $_SERVER['HTTP_USER_AGENT'] != '-') {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $app->bootstrap->user_agent = $user_agent;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $app->bootstrap->ajax = 1;
        } else {
            $app->bootstrap->ajax = 0;
        }
    }
}
