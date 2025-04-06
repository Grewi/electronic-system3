<?php 
namespace system\core\model\classes;
use system\core\database\database;

class eDelete
{
    private string $databaseName;
    private string $id;
    private string $table;
    private string $where;
    private array $data;

    public function databaseName(string $name): void
    {
        $this->databaseName = $name;
    }

    public function id(string $id): void
    {
        $this->id = $id;
    }

    public function data(array $data): void
    {
        unset($data['EMD']);
        if(isset($data[$this->id])){
            unset($data[$this->id]);
        }
        $this->data = $data;
    }
    public function table(string $table): void
    {
        $this->table = $table;
    }

    public function where(string $where): void
    {
        $this->where = $where;
    } 

    public function save(): int
    {
        $db = database::connect($this->databaseName);
        $sql = 'DELETE FROM ' . $this->table . ' ' . $this->where;
        return $db->delete($sql, $this->data);
    }
}