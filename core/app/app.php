<?php
namespace system\core\app;
use system\core\collection\collection;

#[\AllowDynamicProperties]
class app{
    private static $connect = null;
    private $collections = [];

    static public function app()
    {
		if(self::$connect === null){ 
			self::$connect = new self();
		}
		return self::$connect;
	}

    public function __get($name)
    {
        if(!isset($this->collections[$name])){
            $this->collections[$name] = new collection();
        }
        return $this->collections[$name];
    }

    public function toController()
    {
        return self::app();
    }
    
    public function clean() : void
    {
        $this->collections = [];
    }
}