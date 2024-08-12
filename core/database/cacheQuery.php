<?php
namespace system\core\database;

#[\AllowDynamicProperties]
class cacheQuery
{
    private static $connect = null;

    static public function connect()
    {
		if(self::$connect === null){ 
			self::$connect = new self();
		}
		return self::$connect;
	}

    private $data = [];
    public $key = '';


    private function _addKey($sql, $params)
    {
        $this->key = $this->sanitizer($sql);
        foreach($params as $i){
            $this->key .= $this->sanitizer($i);
        }
        return $this;
    }

    private function _control()
    {
        return false;
        if(isset($this->data[$this->key])){
            return true;
        }else{
            return false;
        }
    }

    private function _addQuery($data)
    {
        $this->data[$this->key] = $data;
    }

    private function _returnQuery()
    {
        if(isset($this->data[$this->key])){
            return $this->data[$this->key];
        }else{
            return null;
        }
    }

    private function sanitizer($str){
        return preg_replace('/[^a-zA-Z0-9\<\>\=]/ui', '', $str ?? '');
    }

    public static function __callStatic($method, $parameters)
    {
        $m = '_' . $method;
        if(method_exists(self::connect(), $method)){
            return self::connect()->$method(...$parameters);
        }elseif(method_exists(self::connect(), $m)){
            return self::connect()->$m(...$parameters);
        }
    }
}