<?php

namespace system\core\database;

use system\core\config\config;
use system\core\database\cacheQuery;

#[\AllowDynamicProperties]
class database
{

    /** @var \PDO */
    protected $pdo;
    protected static $db;
    protected static $connect = [];

    protected $type = '';
    protected $file = '';
    protected $host = '';
    protected $name = '';
    protected $user = '';
    protected $pass = '';

    static public function connect($configName = null)
    {
        if (!$configName) {
            $configName = 'database';
        }
        if (!isset(self::$connect[$configName]) || self::$connect[$configName] === null) {
            self::$connect[$configName] = new self($configName);
        }
        return self::$connect[$configName];
    }

    protected function __construct($configName)
    {
        $this->config($configName);
        $this->dpo();
    }

    /** @var Подключение к базе */
    protected function dpo()
    {
        try {
            if ($this->type == 'sqlite') {
                $options = [
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ];
                if (file_exists(ROOT . '/sqlite/' . $this->file . '.db')) {
                    $this->pdo = new \PDO('sqlite:' . ROOT . '/sqlite/' . $this->file . '.db', '', '', $options);
                } else {
                    throw new \PDOException('Ошибка подключения к БД');
                }
            } else if (in_array($this->type, ['mysql', 'pgsql'])) {
                $this->pdo = new \PDO(
                    $this->type . ':host=' . $this->host . ';dbname=' . $this->name  . '; charset=utf8mb4',
                    $this->user,
                    $this->pass
                );
                //$this->pdo->exec('SET NAMES UTF8');
                if (config::globals('dev')) {
                    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                }
            } else {
                throw new \PDOException('Неизвестный тип БД');
            }
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
            // dd('Ошибка подключения к БД: ' . $e->getMessage());
        }        
    }

    protected function config($name)
    {
        $config = config::$name();
        if (!$config) {
            exit('Не установленны настройки для подключения к базе данных');
        }
        $this->file = $config->file;
        $this->type = $config->type;
        $this->name = $config->name;
        $this->host = $config->host;
        $this->user = $config->user;
        $this->pass = $config->pass;
    }

    protected function query(string $sql, array $params = [], string $className = 'stdClass')
    {
        $sth = $this->pdo->prepare($sql);
        foreach ($params as $param => &$value) {
            $sth->bindParam(':' . $param, $value);
        }
        $sth->setFetchMode($this->pdo::FETCH_CLASS, $className);
        $sth->execute();
        return $sth;
    }

    protected function fetchAll(string $sql, array $params = [], string $className = 'stdClass')
    {
        cacheQuery::addKey($sql, $params);
        if (!cacheQuery::control()) {
            $r = $this->query($sql, $params, $className)->fetchAll();
            cacheQuery::addQuery($r);
            return $r;
        } else {
            return cacheQuery::returnQuery();
        }
    }

    private function fetch(string $sql, array $params = [], string $className = 'stdClass')
    {
        cacheQuery::addKey($sql, $params);
        if (!cacheQuery::control()) {
            $r = $this->query($sql, $params, $className)->fetch();
            $r = $r === false ? null : $r;
            cacheQuery::addQuery($r);
            return $r;
        } else {
            return cacheQuery::returnQuery();
        }
    }

    private function transaction()
    {
        $this->pdo->beginTransaction();
    }

    private function commit()
    {
        $this->pdo->commit();
    }

    private function rollBack()
    {
        $this->pdo->rollBack();
    }

    private function errorCode()
    {
        return $this->pdo->errorCode();
    }

    private function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    public static function __callStatic($method, $parameters)
    {
        $m = '_' . $method;
        if (method_exists(self::connect(), $method)) {
            return self::connect()->$method(...$parameters);
        } elseif (method_exists(self::connect(), $m)) {
            return self::connect()->$m(...$parameters);
        }
    }

    public function __call($method, $param)
    {
        $m = '_' . $method;
        if (method_exists($this, $method)) {
            return $this->$method(...$param);
        } elseif (method_exists($this, $m)) {
            return $this->$m(...$param);
        }
    }
}
