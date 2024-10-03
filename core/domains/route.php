<?php
namespace system\core\domains;

class route 
{
    private static $connect = null;
    private $default;
    private $accord = [];

    static public function connect()
    {
		if(self::$connect === null){ 
			self::$connect = new self();
		}
		return self::$connect;
	}

    private function __construct()
    {
        
    }

    /**
     *  Ищем по HTTP_HOST и возвращаем приложение
     */
    public function app($function = null){
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if(isset($this->accord[$host])){
            return $this->accord[$host];
        }else{
            if($this->default){
                return $this->default;
            }
            if($function){
                $function();
            }
            exit();
        }
    }
    
    public function addApp(string $domain, string $app) : void
    {
        $this->accord[$domain] = $app;
    }

    public function addDefault( string $app) : void
    {
        $this->default = $app;
    }
}