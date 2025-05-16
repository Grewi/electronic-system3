<?php
namespace system\inst\classes;
use system\core\database\maryadb;
use system\core\database\sqlite;
use system\core\database\postgre;

class connectDb 
{

    static public function c($configs)
    {
        return match($configs['type']){
            'mysql' => new maryadb($configs['host'], $configs['name'], $configs['user'], $configs['pass']),
            'sqlite' => new sqlite(ROOT . '/sqlite/' . $configs['type'] . '.db'),
            'postgre' => new postgre($configs['host'], $configs['name'], $configs['user'], $configs['pass']),
            default => throw new \PDOException('Не указан подходящий тип базы данных')
        };
    }

}