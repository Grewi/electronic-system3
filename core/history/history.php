<?php
namespace system\core\history;

use system\core\app\app;
use system\core\validate\validate;

class history
{
    private static $singleton = null;
    private $tameSave = 60 * 60 * 24;
    private $cache = APP . '/cache/history.db';
    public $actualHash = null;
    public $referal = null;

    private function __construct()
    {
        $this->create();
        $this->referal();
        $this->clear();
        $this->actualHash = md5((string)time().rand(0,9999));
    }

    public function save()
    {
        if(app::app()->bootstrap->ajax == 1){
            return;
        }
        if (!empty(session_id())) {      
            $sql = 'INSERT INTO `history` (`hash`, `uri`, `method`, `session`, `datetime`) VALUES (
            "' . $this->actualHash . '",
            "' . ($_SERVER['REQUEST_URI'] ?? '') . '",
            "' . ($_SERVER['REQUEST_METHOD'] ?? '') . '",
            "' . session_id() . '",
            "' . time() . '")';
            $this->query($sql, []);
        }
    }

    public function reset()
    {
        if(app::app()->bootstrap->ajax == 1){
            return;
        }
        $sql = 'DELETE FROM `history` WHERE `hash` = "' . $this->actualHash . '";';
        $this->query($sql, []);
    }

    /**
     * Возвращает хеш текущего запроса 
     * @return string
     */
    public function actualHash()
    {
        return $this->actualHash;
    }

    /**
     * Если в запросе есть параметр referal ищет uri
     * @return void
     */
    private function referal()
    {
        $valid = new  validate();
        $valid->name('referal')->free("/^[0-9a-f]+$/u");
        if($valid->control()){
            $a = $this->fetch('SELECT * FROM `history` WHERE `hash` = "' . $valid->return('referal') . '"');
            $this->referal = $a ? $a->uri : null;
        }
    }

    /**
     * Возвращает uri для редиректа
     */
    public function referalUrl()
    {
        if($this->referal){
            return $this->referal;
        }else{
            $a = $this->fetch('SELECT * FROM `history` 
                WHERE `session` = "' . session_id()  . '" 
                AND `method` = "GET" 
                AND `hash` != "' . $this->actualHash . '"
                AND `uri` NOT LIKE "%referal=%"
                ORDER BY `id` DESC LIMIT 1', []);
            if($a){
                $this->fetch('DELETE FROM `history` WHERE `id` = ' . $a->id);
                return $a->uri;
            }else{
                return '/';
            }
        }
    }

    /**
     * Создаёт файл базы данных для хранения
     */
    private function create(): void
    {
        if (!file_exists($this->cache)) {
            file_put_contents($this->cache, '');
            $sql = file_get_contents(__DIR__ . '/sql/createHistory.sql');
            $this->query($sql, []);
        }
    }

    public function clear()
    {
        $this->query('DELETE FROM `history` WHERE `datetime` < "' . time() - $this->tameSave . '"');
    }

    public function delete()
    {
        if(app::app()->bootstrap->ajax == 1){
            return;
        }
        $app = app::app();
        $this->query('DELETE FROM `history` WHERE `uri` = "' . $app->bootstrap->uri . '" AND `method` = "GET" AND `session` = "' . session_id()  . '" ');
    }

    private function db(): \PDO
    {
        $options = [
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        if (file_exists($this->cache)) {
            return new \PDO('sqlite:' . $this->cache, '', '', $options);
        } else {
            throw new \PDOException('Ошибка создания файла истории');
        }
    }

    protected function query(string $sql, array $params = [], string $className = 'stdClass'): bool|\PDOStatement
    {
        $pdo = $this->db();
        $sth = $pdo->prepare($sql);
        foreach ($params as $param => &$value) {
            $sth->bindParam(':' . $param, $value);
        }
        $sth->setFetchMode($pdo::FETCH_CLASS, $className);
        $sth->execute();
        return $sth;
    }

    protected function fetchAll(string $sql, array $params = [], string $className = 'stdClass'): array
    {
        return $this->query($sql, $params, $className)->fetchAll();
    }

    private function fetch(string $sql, array $params = [], string $className = 'stdClass'): mixed
    {
        $r = $this->query($sql, $params, $className)->fetch();
        return $r === false ? null : $r;
    }

    public static function __callStatic($method, $args)
    {
        if(!self::$singleton){
            self::$singleton = new self;
        }
        self::$singleton->$method(...$args);
    }

    public static function start()
    {
        if(!self::$singleton){
            self::$singleton = new self;
        }
        return self::$singleton;
    }
}