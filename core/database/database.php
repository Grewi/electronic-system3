<?php

namespace system\core\database;

use system\core\database\maryadb;
use system\core\database\sqlite;
use system\core\config\config;

class database
{

    public static function connect($configName = 'database')
    {
        $config = (new ('\\' . APP_NAME . '\\configs\\' . $configName)())->all();
        if (!$config) {
            throw new \PDOException('Не установленны настройки для подключения к базе данных');
        }
        return match($config['type']){
            'mysql' => new maryadb($config['host'], $config['name'], $config['user'], $config['pass']),
            'sqlite' => new sqlite(ROOT . '/sqlite/' . $config['type'] . '.db'),
            'postgre' => new postgre($config['host'], $config['name'], $config['user'], $config['pass']),
            default => throw new \PDOException('Не указан подходящий тип базы данных')
        };
    }
}