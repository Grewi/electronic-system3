<?php
namespace system\core\history;
use system\core\app\app;

class history2
{
    public static $tameSave = 60 * 60 * 24 * 365;
    public static $cache = APP . '/cache/history.db';


    public static function start()
    {
        if(!self::startControl()){
            self::clear();
            define('CONTROL_START_HISTORY', true);
        }
    }

    public static function startControl()
    {
        return defined('CONTROL_START_HISTORY') ? true : false;
    }

    public static function save()
    {
        self::create();
        $h = md5((string)time());
        $app = app::app();
        $tab = $_COOKIE['tabID'] ?? 0;
        $sess = session_id() ?? 0;
        $ajax = $app->bootstrap->ajax ? 1 : 0;
        $uri = $app->bootstrap->uri;
        $method = $app->bootstrap->method;
        $control = self::fetch('SELECT * FROM `history` WHERE `uri` = "' . $uri . '" AND `method` = "' . $method . '" AND `tab` = "' . $tab . '" AND `session` = "' . $sess . '"', []);
        if (!$control && $sess != 0) {
            self::start();
            $sql = 'INSERT INTO `history` (`uri`, `method`, `tab`, `ajax`, `session`, `datetime`, `status`) VALUES (
            "' . $uri . '","' . $method . '","' . $tab . '","' . $ajax . '","' . $sess . '","' . time() . '", "200")';
            self::query($sql, []);
            $dbId = self::fetch('SELECT * FROM `history` ORDER BY `id` DESC LIMIT 1', []);
            dump($dbId->id);
            
        }
        header('History: ' . $h);
    }

    /**
     *  Устанавливается текущий статус (код) http  запроса
     */
    public static function setStatus()
    {
        if (!self::startControl()) {
            return;
        }
        $tab = $_COOKIE['tabID'] ?? 0;
        $sess = session_id() ?? 0;
        self::query('UPDATE `history` SET `status` = "' . http_response_code() . '" WHERE `session` = "' . $sess . '" AND `tab` = "' . $tab . '" ORDER BY `id` DESC LIMIT 1', []);
    }

    /**
     * Создаёт файл базы данных для хранения
     */
    public static function create(): void
    {
        if (!file_exists(self::$cache)) {
            file_put_contents(self::$cache, '');
            $sql = file_get_contents(__DIR__ . '/sql/createHistory.sql');
            self::query($sql, []);
        }
    }

    public static function clear()
    {
        if (!self::startControl()) {
            return;
        }
        self::query('DELETE FROM `history` WHERE `datetime` < "' . time() - self::$tameSave . '"');
    }

    public static function referal()
    {
        $tab = $_COOKIE['tabID'] ?? 0;
        $sess = session_id() ?? 0;
        $r = self::fetchAll('
        SELECT * FROM `history` 
        WHERE `session` = "' . $sess . '" 
        AND `tab` = "' . $tab . '"
        AND `status` = "200" 
        AND `ajax` = 0
        ORDER BY `id` DESC LIMIT 2', []);
        dump($r);
    }

    public static function db(): \PDO
    {
        $options = [
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        if (file_exists(self::$cache)) {
            return new \PDO('sqlite:' . self::$cache, '', '', $options);
        } else {
            throw new \PDOException('Ошибка создания файла истории');
        }
    }

    protected static function query(string $sql, array $params = [], string $className = 'stdClass'): bool|\PDOStatement
    {
        $pdo = self::db();
        $sth = $pdo->prepare($sql);
        foreach ($params as $param => &$value) {
            $sth->bindParam(':' . $param, $value);
        }
        $sth->setFetchMode($pdo::FETCH_CLASS, $className);
        $sth->execute();
        return $sth;
    }

    protected static function fetchAll(string $sql, array $params = [], string $className = 'stdClass'): array
    {
        return self::query($sql, $params, $className)->fetchAll();
    }

    private static function fetch(string $sql, array $params = [], string $className = 'stdClass'): mixed
    {
        $r = self::query($sql, $params, $className)->fetch();
        return $r === false ? null : $r;
    }
}