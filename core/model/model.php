<?php
namespace system\core\model;
use system\core\collection\collection;
use system\core\database\database;
use system\core\model\classes\{
    eSelect, 
    eFrom, 
    eSort, 
    eBind, 
    eWhere, 
    eLimit, 
    eJoin, 
    eInsert, 
    eUpdate, 
    eDelete,
    eOffset,
    eGroup,
};

#[\AllowDynamicProperties]
class model
{
    private collection $EMD;

    public function __construct()
    {
        $this->EMD = new collection;
        $this->EMD->databaseName = 'database';
        $this->EMD->select = new eSelect;
        $this->EMD->from   = new eFrom;
        $this->EMD->sort   = new eSort;
        $this->EMD->bind   = new eBind;
        $this->EMD->where  = new eWhere;
        $this->EMD->limit  = new eLimit;
        $this->EMD->join   = new eJoin;
        $this->EMD->insert = new eInsert;
        $this->EMD->update = new eUpdate;
        $this->EMD->delete = new eDelete;
        $this->EMD->offset = new eOffset;
        $this->EMD->group  = new eGroup;
        $c = explode('\\', get_called_class());
        $this->EMD->from->add(array_pop($c));
        $this->EMD->id = 'id';
        $this->EMD->paginCount = 20;
        $this->EMD->limitDirection = 20;
        $this->EMD->paginationLine = [];
        $this->EMD->paginationPriv = 0;
        $this->EMD->paginationNext = 0;
        $this->EMD->paginationActive = 0;
    }

    public function select(string $select)
    {
        $this->EMD->select->add($select);
        return $this;
    }

    public function from(string $from): static
    {
        $this->EMD->from->add($from);
        return $this;
    }

    public function whereL(string $col, string $operator, string|int|float $value, bool $or = false): static
    {
        $this->EMD->where->where($col, $operator, $value, $or);
        return $this;
    }

    public function where(string $col, string|int|float $value, bool $or = false): static
    {
        $this->EMD->where->where($col, '=', $value, $or);
        return $this;
    }

    public function whereNull(string $col)
    {
        $this->EMD->where->whereNull($col);
        return $this;
    }

    public function whereNotNull(string $col)
    {
        $this->EMD->where->whereNotNull($col);
        return $this;
    }

    public function whereIn(string $col, array|object $arg)
    {
        $this->EMD->where->whereIn($col, $arg);
        return $this;
    }

    public function whereStr(string $str, array $bind = [])
    {
        $this->EMD->where->whereStr($str, $bind);
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->EMD->limit->add($limit);
        return $this;
    }

    public function sort(string $name, string $type = 'asc'): static
    {
        $this->EMD->sort->add($name, $type);
        return $this;
    }

    public function innerJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 0);
        return $this;
    }

    public function leftJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 1);
        return $this;
    }

    public function rightJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 2);
        return $this;
    }

    public function fullJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 3);
        return $this;
    }

    public function crossJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 4);
        return $this;
    }

    public function insert(array $data): static
    {
        $this->EMD->insert->databaseName($this->EMD->databaseName);
        $this->EMD->insert->table($this->EMD->from->get());
        $this->EMD->insert->bind($this->bind());
        $this->EMD->insert->id($this->EMD->id);
        $this->EMD->insert->data($data);
        $id = $this->EMD->insert->save();
        $cl = $this::class;
        return (new $cl)->find($id);
    }

    public function update(array $data = []): static
    {
        $d = array_merge(get_object_vars($this), $data);
        $this->where($this->EMD->id, $d[$this->EMD->id]);
        $this->EMD->update->where($this->EMD->where->get());  
        $this->EMD->update->databaseName($this->EMD->databaseName);
        $this->EMD->update->table($this->EMD->from->get());
        $this->EMD->update->bind($this->bind());
        $this->EMD->update->id($this->EMD->id);
        $this->EMD->update->data($d);
        $this->EMD->update->save();
        $cl = $this::class;
        return (new $cl)->find($this->id);
    }

    public function delete(array $data = []): void
    {
        $d = array_merge(get_object_vars($this), $data);
        if(isset($this->id)){
            $this->where($this->EMD->id, $d[$this->EMD->id]);
        }
        $this->EMD->delete->where($this->EMD->where->get());  
        $this->EMD->delete->databaseName($this->EMD->databaseName);
        $this->EMD->delete->table($this->EMD->from->get());
        $this->EMD->delete->id($this->EMD->id);
        $this->EMD->delete->data($this->bind());
        $this->EMD->delete->save();
    }

    public function all(): array
    {
        return db($this->EMD->databaseName)->fetchAll($this->slectSql(), $this->bind(), get_class($this));
    }

    public function get()
    {
        return db($this->EMD->databaseName)->fetch($this->slectSql(), $this->bind(), get_class($this));
    }

    public function find(int $id): static
    {
        $db = database::connect($this->EMD->databaseName);
        return $db->fetch('SELECT * FROM ' . $this->EMD->from->get() . ' WHERE `' . $this->EMD->id . '` = :' . $this->EMD->id . ' ', [$this->EMD->id => $id], get_class($this));
    }

    public function sqlPrint($format = true, $exit = false): void
    {
        if ($format) {
            dump($this->slectSql());
        } else {
            print_r($this->slectSql()) . PHP_EOL;
        }
        if ($exit) {
            exit();
        }
    }

    public function bindPrint($format = true, $exit = false): void
    {
        if ($format) {
            dump($this->bind());
        } else {
            print_r($this->bind()) . PHP_EOL;
        }
        if ($exit) {
            exit();
        }
    }

    private function slectSql(): string
    {
        $a = 'SELECT ' . $this->EMD->select->get() . ' ' . ' FROM ' .
        $this->EMD->from->get() . ' ' .
        $this->EMD->join->get() . ' ' .
        $this->EMD->where->get() . ' ' .
        $this->EMD->group->get() . ' ' .
        $this->EMD->sort->get() . ' ' .
        $this->EMD->limit->get() . ' ' .
        $this->EMD->offset->get();
        return preg_replace('/\s{2,}/', ' ', $a);
    }

    private function bind(): array
    {
        return array_merge($this->EMD->where->bind->get());
    }

    public function __debugInfo(): array
    {
        $methods = [];
        $reflect = new \ReflectionObject($this);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $methods[$prop->getName()] = $this->{$prop->getName()};
        }
        return $methods;
    }
}
