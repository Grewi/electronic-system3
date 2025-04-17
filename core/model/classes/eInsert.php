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
        unset($data['EMD']);
        if(isset($data[$this->id])){
            unset($data[$this->id]);
        }
        $this->data = $data;
    }

    public function save(): ?int
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
        return $db->insert($sql, $data);
    }
}