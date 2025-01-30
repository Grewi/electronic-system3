<?php 
namespace system\core\model\classes;

use system\core\config\config;
use system\core\database\database;
use system\core\model\classes\bind;
use system\core\model\model;

class eInsert
{
    private string|null $databaseName = null;
    private model $model;

    public function databaseName(string $name): void
    {
        $this->databaseName = $name;
    }

    public function model(model $class): void
    {
        $this->model = $class;
    }

    public function save(array $data, string $id, string $table, bind &$bind): int
    {
        $db = database::connect($this->databaseName);
        $count = count($data);
        $strKey = '';
        $strData = '';
        $c = 0;
        foreach($data as $key => $i){
            $c++;
            $strKey .= ' `' . $key . '`' . ($c == $count ? ' ' : ', ');
            $strData .= ' :' . $key . '' . ($c == $count ? ' ' : ', ');
        }

        $data = array_merge($data, $bind->get());
        $sql = 'INSERT INTO ' . $table . ' (' . $strKey .') VALUES (' . $strData . ')';
        $db->query($sql, $data);

        if (config::database('type') == 'sqlite') {
            $dbId = $db->fetch('SELECT Last_insert_rowid() as ' . $id, []);
        }else{
            $dbId = $db->fetch('SELECT * FROM ' . $table . ' where ' . $id .' = LAST_INSERT_ID()', []);
        }

        return $dbId->{$id};
    }
}