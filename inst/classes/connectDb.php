<?php
namespace system\inst\classes;
use system\core\database\database;

class connectDb extends database
{

    static public function c($configs)
    {
        if (!isset(self::$connect['install']) || self::$connect['install'] === null) {
            self::$connect['install'] = new self($configs);
        }
        return self::$connect['install'];
    }

    protected function __construct($configs)
    {
        $this->file = $configs['file'];
        $this->type = $configs['type'];
        $this->name = $configs['name'];
        $this->host = $configs['host'];
        $this->user = $configs['user'];
        $this->pass = $configs['pass'];
        $this->dpo();
    }

}