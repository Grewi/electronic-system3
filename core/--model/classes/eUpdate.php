<?php
namespace system\core\model\classes;
use system\core\model\classes\where;
use system\core\model\classes\bind;
use system\core\database\database;

class eUpdate
{
    private string|null $databaseName = null;

    public function databaseName(string $name): void
    {
        $this->databaseName = $name;
    }

    public function save(array $data, string $id, string $table, bind &$bind, where &$where): int
    {
        $db = database::connect($this->databaseName);
        $count = count($data);
        $str = '';
        $c = 0;
        foreach ($data as $key => $i) {
            $c++;
            $str .= ' `' . $key . '` = :' . $key . ($c == $count ? '': ',') . ' ';
        }
        $data = array_merge($data, $bind->get());
        $sql = 'UPDATE ' . $table . ' SET ' . $str . ' ' . $where->get(). ';';
        $db->query($sql, $data);
        dd($id, $data);
        $dbId = $db->fetch('SELECT * FROM ' . $table . ' WHERE `' . $id . '` = ' . $data[$id] . ';', []);
        return $dbId;
    }
}