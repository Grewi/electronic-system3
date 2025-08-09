<?php
namespace system\core\app;

class app implements \JsonSerializable
{
    private static $connect = null;
    private $collections = [];

    static public function app()
    {
		if(static::$connect === null){ 
			static::$connect = new static();
		}
		return static::$connect;
	}

    private function __construct()
    {
        
    }

    public function __get($name)
    {
        if(!isset($this->collections[$name])){
            return null;
        }
        return $this->collections[$name];
    }

    public function __set($a, $b)
    {
        $this->collections[$a] = $b;
    }
    
    public function clean() : void
    {
        $this->collections = [];
    }

    public function set(string $name): void
    {
        $this->collections[$name] = new app();
    }

    public function add($a): void
    {
        $this->collections[] = $a;
    }

    public function getArray(): array
    {
        $r = [];
        foreach($this->collections as $a => $i){
            $r[$a] = $i instanceof app ? $i->getArray() : $i;
        }
        return $r;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->collections);
    }    

    public function jsonSerialize() : mixed
    {
        $array = [];
        foreach($this->collections as $a => $i){
            $array[$a] = json_encode($i);
        }
        return $array;
    }

    public function __debugInfo()
    {
        return $this->collections ?? null;
    }


}