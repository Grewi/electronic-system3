<?php 
namespace system\core\model\classes;

use system\core\config\config;
use system\core\database\database;
use system\core\model\model;

class eInsert
{
    private string $databaseName;
    private string $id;
    private string $table;
    private array $bind;
    private array $data;
    private bool $returnedId;

    public function databaseName(string $name): void
    {
        $this->databaseName = $name;
    }

    public function id(string $id): void
    {
        $this->id = $id;
    }

    public function table(string $table): void
    {
        $this->table = $table;
    }

    public function bind(array $bind): void
    {
        $this->bind = $bind;
    }

    public function data(array $data): void
    {
        $this->data = $data;
    }

    public function returnedId(bool $data)
    {
        $this->returnedId = $data;
    }

    public function save(): int|null
    {
        $db = database::connect($this->databaseName);
        $count = count($this->data);
        $strKey = '';
        $strData = '';
        $c = 0;
        foreach($this->data as $key => $i){
            $c++;
            $strKey .= ' `' . $key . '`' . ($c == $count ? ' ' : ', ');
            $strData .= ' :' . $key . '' . ($c == $count ? ' ' : ', ');
        }

        $data = array_merge($this->data, $this->bind);
        $sql = 'INSERT INTO ' . $this->table . ' (' . $strKey .')  VALUES (' . $strData . ')';
     
        $db->query($sql, $data);

        if(!$this->returnedId){
            return null;
        }

        if (config::database('type') == 'sqlite') {
            $dbId = $db->fetch('SELECT Last_insert_rowid() as ' . $this->id, []);
        }else{
            $dbId = $db->fetch('SELECT * FROM ' . $this->table . ' where ' . $this->id .' = LAST_INSERT_ID()', []);
        }

        return $dbId->{$this->id};
    }
}