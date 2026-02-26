<?php

use system\core\database\database;
use system\core\request\request;
use system\core\lang\lang;
use system\core\user\auth;
use system\core\app\app;
use system\core\files\files;
use system\core\system\header;
use system\core\history\history;
use system\core\config\iConfig;
use system\core\config\globals;

if (!function_exists('db')) {
    function db($configName = 'database')
    {
        return database::connect($configName);
    }
}

if (!function_exists('user_id')) {
    function user_id()
    {
        return auth::status();
    }
}

if (!function_exists('includeFile')) {
    function includeFile($path)
    {
        files::includeFile($path);
    }
}

if (!function_exists('createDir')) {
    function createDir($path)
    {
        files::createDir($path);
    }
}

if (!function_exists('deleteDir')) {
    function deleteDir($path)
    {
        return files::deleteDir($path);
    }
}

if (!function_exists('copyDir')) {
    function copyDir($from, $to, $rewrite = true)
    {
        files::copyDir($from, $to, $rewrite);
    }
}

if (!function_exists('alert')) {
    function alert($text, $type = 'primary', $header = '')
    {
        $_SESSION['alert'][] = [
            'header' => $header,
            'text' => $text,
            'type' => $type,
        ];
    }
}

if (!function_exists('referal_url')) {
    function referal_url()
    {
        if(isset($_SERVER['HTTP_REFERER'])){
            if(strpos($_SERVER['HTTP_REFERER'], app::app()->bootstrap->url) === 0){
                return $_SERVER['HTTP_REFERER'];
            }
        }
        return history::start()->referalUrl();
    }
}

if (!function_exists('referal_hash')) {
    function referal_hash()
    {
        return history::start()->actualHash;
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $data = null, $error = null)
    {
        (new header())->data($data)->error($error)->location($url);
    }
}

if (!function_exists('csrf')) {
    function csrf($name)
    {
        if (!isset($_SESSION['csrf'][$name])) {
            $token = bin2hex(random_bytes(35));
            $_SESSION['csrf'][$name] = $token;
            return  $token;
        } else {
            return $_SESSION['csrf'][$name];
        }
    }
}

// if (!function_exists('historyid')) {
//     function historyid()
//     {
//         return history::currentId();
//     }
// }

if (!function_exists('returnModal')) {
    function returnModal($i)
    {
        $_SESSION['returnModal'] = $i;
    }
}

if (!function_exists('getConfig')) {
    function getConfig($file, $param)
    {
        if(!file_exists(APP . '/configs/' . $file . '.php')){
            return (new globals($file))->get($param);
        }else{
            $class = new ('\\' . APP_NAME . '\\configs\\' . $file);
        }
        
        if($class instanceof iConfig){
            return (new $class)?->get($param);
        }else{
            throw new \Exception('Класс конфигурации ' . $file . ' не реализует интерфейс iConfig');
        }
    }
}

if (!function_exists('allConfig')) {
    function allConfig($file)
    {
        if(!file_exists(APP . '/configs/' . $file . '.php')){
            return (new globals($file))->all();
        }
        $class = new ('\\' . APP_NAME . '\\configs\\' . $file);
        if($class instanceof iConfig){
            return (new $class)?->all();
        }else{
            throw new \Exception('Класс конфигурации ' . $file . ' не реализует интерфейс iConfig');
        }
    }
}

if (!function_exists('dump')) {
    function dump(...$a)
    {
        if (getConfig('globals','dev')) {
            if(!headers_sent()){
                $code = getConfig('globals','dumpcode') ?? 423;
                http_response_code($code);
            }
            if (getConfig('globals','dumpline')) {
                $backtrace = debug_backtrace();
                echo '<div style="font-size: 12px; padding:3px; background: #fff; font-family: monospace; white-space:nowrap;">
            <span style="color:#900;">' . $backtrace[0]['file'] . '</span>
            <span style="color:#090;">' . $backtrace[0]['line'] . '</span>
            </div>';
            }
            foreach ($a as $b) {
                var_dump($b);
            }
        }
    }
}

if (!function_exists('dd')) {
    function dd(...$a)
    {
        if (getConfig('globals','dev')) {
            if(!headers_sent()){
                $code = getConfig('globals','dumpcode') ?? 423;
                http_response_code($code);
            }            
            if (getConfig('globals','dumpline')) {
                $backtrace = debug_backtrace();
                if (ENTRANSE == 'web') {
                    echo '<div style="font-size: 12px; padding:3px; background: #fff; font-family: monospace; white-space:nowrap;">
                    Вызов в: 
                <span style="color:#900;">' . localPathFile($backtrace[0]['file']) . '</span>
                <span style="color:#090;">' . $backtrace[0]['line'] . '</span>
                </div>';
                } else {
                    echo 'Вызов в: ' . localPathFile($backtrace[0]['file']) . ' (' . $backtrace[0]['line'] . ')' . PHP_EOL;
                }
            }
            foreach ($a as $b) {
                var_dump($b);
            }
            exit();
        }
    }
}

if (!function_exists('localPathFile')) {
    function localPathFile($path)
    {
        return str_replace(ROOT, '', str_replace('\\', '/', $path));
    }
}

if(!function_exists('microtime_system')){
    function time_system(string $name){
        $app = app::app();
        $app->time->{$name} = (round(microtime(true) - $app->time->start, 3));

        $b = (memory_get_usage() - $app->memory->start);
        $i = 0;
        while (floor($b / 1024) > 0) {
            $i++;
            $b /= 1024;
        }
         
        $n = array('байт', 'КБ', 'МБ');
        $app->memory->{$name} =  round($b, 2) . ' ' . $n[$i];
        
    }
}

if(!function_exists('eEmpty')){
    function eEmpty(mixed $a): bool {
        return empty($a);
    }
}