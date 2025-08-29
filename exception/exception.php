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
            if (!headers_sent()) {
                http_response_code(503);
            }

            if (getConfig('globals', 'dev')) {
                require SYSTEM . '/exception/tempDew.php';
            } else {
                if (file_exists(APP . '/views/error/exception.html')) {
                    $f = file_get_contents(APP . '/views/error/exception.html');
                    echo $f;
                } else {
                    require SYSTEM . '/exception/tempProd.php';
                }
            }
        } else {
            require SYSTEM . '/exception/tempConsole.php';
        }
        self::cache($exeption, $message, $code);
        exit();
    }

    public static function cache($exeption, $message, $code)
    {
        $dir = APP . '/cache/exception/' . ENTRANSE . '/';
        createDir($dir);
        foreach(scandir($dir) as $file){
            if($file == '.' || $file == '..' || !file_exists($dir . $file)){
                continue;
            }
            if(filectime($dir . $file) < (time() - (60 * 60 * 24 * 30))){
                unlink($dir . $file);
            }
        }

        $str = '';
        $fileLen = strlen('File');
        $lineLen = strlen('Line');
        $clssLen = strlen('Class');
        $funcLen = strlen('Function');

        foreach ($exeption->getTrace() as $e) {
            $fileLen = strlen(localPathFile($e['file'])) > $fileLen ? strlen(localPathFile($e['file'])) : $fileLen;
            $lineLen = isset($e['line']) && strlen($e['line']) > $lineLen ? strlen($e['line']) : $lineLen;
            $clssLen = isset($e['class']) && strlen($e['class']) > $clssLen ? strlen($e['class']) : $clssLen;
            $funcLen = isset($e['function']) && strlen($e['function']) > $funcLen ? strlen($e['function']) : $funcLen;
        }
        $str .=  PHP_EOL;
        $s = $fileLen + $lineLen + $clssLen + $funcLen;
        $errorStr = str_pad('', (int)($s / 2), '-') . 'ERROR';
        $str .= str_pad($errorStr, $s + 3, '-') . PHP_EOL;

        $str .= $message  . PHP_EOL;
        $str .= localPathFile($exeption->getFile()) . ' - (' .  $exeption->getLine() . ')' . PHP_EOL;
        $str .= str_pad('File', $fileLen, '-') . ' '
            . str_pad('Line', $lineLen, '-') . ' '
            . str_pad('Class', $clssLen, '-') . ' '
            . str_pad('Function', $funcLen, '-') . PHP_EOL;

        foreach ($exeption->getTrace() as $e) {

            $file = localPathFile($e['file']);
            $line = isset($e['line']) ? $e['line'] : '';
            $class = isset($e['class']) ? $e['class'] : '';
            $function = isset($e['function']) ? $e['function'] : '';
        $str .= str_pad($file, $fileLen, ' ') . ' '
                . str_pad($line, $lineLen, ' ') . ' '
                . str_pad($class, $clssLen, ' ') . ' '
                . str_pad($function, $funcLen, ' ') . PHP_EOL;
        }
        $errorStr = str_pad('', (int)($s / 2 + 2), '-') . '^';
        $str .= str_pad($errorStr, $s + 3, '-') . PHP_EOL;

        file_put_contents($dir . date('Y-m-d'). '.log', $str, FILE_APPEND);
    }
}

function myShutdown()
{
    exit();
}
register_shutdown_function('myShutdown');
