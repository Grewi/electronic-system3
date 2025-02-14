<?php
namespace system\core\history;
use system\core\app\app;
use system\core\validate\validate;

class history3
{
    private static $singleton = null;
    public $tameSave = 60 * 60;
    public $cache = APP . '/cache/history2.db';

    public $actualHeash = null;
    public $actualTab = null;

    private function __construct()
    {
        $this->actualHeash = md5((string)time().rand(0,9999));
    }

    public static function start()
    {
        if(!self::$singleton){
            self::$singleton = new self();
        }
        return self::$singleton;
    }
    public function save()
    {
        $this->create();
        $this->cookies();
        $sess = session_id() ?? 0;
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $tab = $this->actualTab;
        if($tab){
            $o = $this->fetch('SELECT * FROM `history` WHERE `session` = "' . $sess . '" AND `tab` = "' . $tab . '" ORDER BY `id` DESC LIMIT 1');            
        }else{
            $o = $this->fetch('SELECT * FROM `history` WHERE `session` = "' . $sess . '" ORDER BY `id` DESC LIMIT 1');            
        }

        if($o && ($o->uri == $uri && $o->method == $method) ){
            return;
        }

        $sql = 'INSERT INTO `history` (`uri`, `method`, `tab`, `session`, `datetime`, `status`, `hash`) VALUES (
        "' . $uri . '","' . $method . '", "' . $tab . '","' . $sess . '","' . time() . '", "200", "' . $this->actualHeash . '")';
        $this->query($sql, []);
        $this->fetch('SELECT * FROM `history` ');   

        header('History: ' . $this->actualHeash);
    }

    public function setTabId()
    {
        $valid = new  validate();
        $valid->name('tabid')->float();
        $valid->name('historyid')->free("/^[0-9a-f]+$/u");
        if($valid->control()){
            $tab = $valid->return('tabid'); 
            $hash = $valid->return('historyid');
            $this->query('UPDATE `history` SET `tab` = "' . $tab . '" WHERE `hash` = "' . $hash . '" ', []);
        }
    }

    /**
     * Создаёт файл базы данных для хранения
     */
    public function create(): void
    {
        if (!file_exists($this->cache)) {
            file_put_contents($this->cache, '');
            $sql = file_get_contents(__DIR__ . '/sql/createHistory2.sql');
            $this->query($sql, []);
        }
    }

    public function referal()
    {
        $sess = session_id() ?? 0;
        $tab = $this->actualTab;
        $a = $this->fetch('SELECT * FROM `history` WHERE `session` = "' . $sess . '" ORDER BY `id` DESC LIMIT 1');
        $old = $this->fetch('SELECT * FROM `history` 
            WHERE `tab` = "' . $a->tab . '" 
            AND `method` = "GET" 
            AND `hash` != "' . $a->hash . '"
            ORDER BY `id` DESC LIMIT 1');
        return $old ? $old->uri : '/';
    }

    public function db(): \PDO
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

    private function cookies()
    {
        if(isset($_COOKIE['History']) && isset($_COOKIE['tabID'])){
            $h = $_COOKIE['History'];
            $this->actualTab = $_COOKIE['tabID'];
            $this->query('UPDATE `history` SET `tab` = "' . $this->actualTab . '" WHERE `hash` = "' . $h . '"');
        }
    }
}