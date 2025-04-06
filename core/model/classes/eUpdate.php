<?php
namespace system\core\model\classes;
use system\core\model\classes\where;
use system\core\model\classes\bind;
use system\core\database\database;

class eUpdate
{
    private string $databaseName;
    private string $id;
    private string $table;
    private array $bind;
    private array $data;
    private string $where;

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

    public function where(string $where): void
    {
        $this->where = $where;
    }    

    public function save(): int
    {
        $db = database::connect($this->databaseName);
        $count = count($this->data);
        $str = '';
        $c = 0;
        foreach ($this->data as $key => $i) {
            $c++;
            $str .= ' `' . $key . '` = :' . $key . ($c == $count ? '': ',') . ' ';
        }
        $data = array_merge($this->data, $this->bind);
        $sql = 'UPDATE ' . $this->table . ' SET ' . $str . ' ' . $this->where . ';';
        return $db->update($sql, $data);
    }
}
