<?php
use \system\core\config\config;

class FileException extends Exception
{
    public $message = '';
    public function __construct(string $message = '', int $code = 1)
    {
        exeptionVar::dump($this, $message, $code);
    }
}

class GlobalException extends Exception
{
    public function __construct(string $message = '', int $code = 1)
    {
        exeptionVar::dump($this, $message, $code);
    }
}

class MaxCountIncludeTemp extends Exception
{
    public $message = '';
    public function __construct(string $message = '', int $code = 0)
    {
        exeptionVar::dump($this, $message, $code);
    }
}

class TempException extends Exception
{
    public $message = '';
    public function __construct(string $message = '', int $code = 0)
    {
        exeptionVar::dump($this, $message, $code);
    }
}

class exeptionVar
{
    public static function dump($exeption, $message, $code)
    {
        ob_end_clean();
        if (ENTRANSE == 'web') {
            if(!headers_sent()){
                http_response_code(503);
            }
            
            if (getConfig('globals', 'dev')) {
                require SYSTEM . '/exception/tempDew.php';
            } else {
                if(file_exists(APP . '/views/error/exception.html')){
                    $f = file_get_contents(APP . '/views/error/exception.html');
                    echo $f;
                }else{
                    require SYSTEM . '/exception/tempProd.php';
                }
            }
        } else {
            require SYSTEM . '/exception/tempConsole.php';
        }
        exit();
    }
}

function myShutdown()
{
    exit();
}
register_shutdown_function('myShutdown');