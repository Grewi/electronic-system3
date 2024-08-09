<?php declare(strict_types=1);

namespace system\core\lang;
use system\core\config\config;

class lang 
{
    private $default = 'ru';

    public function __construct()
    {
        $l = config::globals('lang');
        if($l){
           $this->default = $l;
        }
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static)->action($name, $arguments);
    }

    public function __call($name, $arguments)
    {
        return (new static)->action($name, $arguments);
    } 

    private function action($name, $arguments)
    {
        $lex = array_shift($arguments);
        $file = APP . '/lang/' . (new static)->default . '/' . $name . '.php';
        $str = '';
        if(file_exists($file)){
            $langs = require $file;
            if(isset($langs[$lex])){
                $str = $langs[$lex];
                try{
                    $str = sprintf($str, ...$arguments);
                }catch(\Throwable $e){
                    dd($e);
                }
            }
        }

        if(empty($str)){
            return $lex;
        }else{
            return $str;
        }        
    }

}