<?php

namespace system\core\model;
use system\core\model\classes\{EMD, eSelect, eFrom, eSort, eBind, eWhere, eLimit, eJoin, eInsert, eUpdate,};
// use system\core\model\traits\update;
// use system\core\model\traits\delete;
// use system\core\model\traits\save;

// use system\core\model\traits\pagination;
// use system\core\model\traits\group;
// use system\core\model\traits\sortGet;
// use system\core\model\traits\filter;


#[\AllowDynamicProperties]
abstract class model
{
    // use update;
    // use delete;
    // use save;
    // use pagination;
    // use group;
    // use sortGet;
    // use filter;

    protected EMD $EMD;

    public function __construct()
    {
        $this->EMD = new EMD();
        if (empty($this->EMD->table)) {
            $c = explode('\\', get_called_class());
            $this->EMD->table = array_pop($c);
        }
        $this->EMD->from()->add($this->EMD->table);
    }

    private function select(string $select)
    {
        $this->EMD->select();
        $this->EMD->select->add($select);
        return $this;
    }

    private function from(string $from): static
    {
        $this->EMD->from()->add($from);
        return $this;
    }

    private function whereL(string $col, string $operator, string|int|float $value, bool $or = false): static
    {
        $this->EMD->where()->bind($this->EMD->bind());
        $this->EMD->where()->where($col, $operator, $value, $or);
        return $this;
    }

    private function where(string $col, string|int|float $value, bool $or = false): static
    {
        $this->EMD->where()->bind($this->EMD->bind());
        dd($this->EMD->where());
        $this->EMD->where()->where($col, '=', $value, $or);
        return $this;
    }

    private function whereNull(string $col)
    {
        $this->EMD->where()->bind($this->EMD->bind());
        $this->EMD->where()->whereNull($col);
        return $this;
    }

    private function whereNotNull(string $col)
    {
        $this->EMD->where()->bind($this->EMD->bind());
        $this->EMD->where()->whereNotNull($col);
        return $this;
    }

    private function whereIn(string $col, array|object $arg)
    {
        $this->EMD->where()->bind($this->EMD->bind());
        $this->EMD->where()->whereIn($col, $arg);
        return $this;
    }

    private function whereStr(string $str, array $bind = [])
    {
        $this->EMD->where()->bind($this->EMD->bind());
        $this->EMD->where()->whereStr($str, $bind);
        return $this;
    }

    private function limit(int $limit): static
    {
        $this->EMD->limit()->add($limit);
        return $this;
    }

    private function sort(string $name, string $type = 'asc'): static
    {
        $this->EMD->sort()->add($name, $type);
        return $this;
    }

    private function innerJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 0);
        return $this;
    }

    private function leftJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 1);
        return $this;
    }

    private function rightJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 2);
        return $this;
    }

    private function fullJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 3);
        return $this;
    }

    private function crossJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 4);
        return $this;
    }

    private function insert(array $data): static
    {
        $this->EMD->insert()->databaseName($this->EMD->databaseName);
        $this->EMD->insert->model($this);
        $id = $this->EMD->insert->save($data, $this->EMD->id, $this->EMD->table, $this->EMD->bind());
        return ($this::class)::find($id);
    }

    private function update(array $data): static
    {
        $this->EMD->update()->databaseName($this->EMD->databaseName);
        $d = get_object_vars($this);
        unset($d['EMD']);
        $d = array_merge($d, $data);
        $this->where($this->EMD->id, $d[$this->EMD->id]);
        $id = $this->EMD->update()->save($data, $this->EMD->id, $this->EMD->table, $this->EMD->bind(), $this->EMD->where());
        return ($this::class)::find($id);
    }

    private function count(): string
    {
        $str = 'SELECT COUNT(*) as count FROM ' .
            $this->EMD->from() . ' ' .
            $this->EMD->join() . ' ' .
            $this->EMD->where() . ' ' .
            $this->EMD->group();
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return db($this->EMD->databaseName)->fetch($str, $this->EMD->bind()->get(), get_class($this))->count;
    }

    private function summ($name): float
    {
        $str = 'SELECT SUM(`' . $name . '`) as `summ` FROM ' .
            $this->EMD->from()->get() . ' ' .
            $this->EMD->join()->get() . ' ' .
            $this->EMD->where()->get() . ' ' .
            $this->EMD->group;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return (int) db($this->EMD->databaseName)->fetch($str, $this->EMD->bind()->get(), get_class($this))->summ;
    }

    private function all(): array
    {
        $str = 'SELECT ' . $this->EMD->select()->get() . ' ' . ' FROM ' .
            $this->EMD->from->get() . ' ' .
            $this->EMD->join->get() . ' ' .
            $this->EMD->where->get() . ' ' .
            $this->EMD->group . ' ' .
            $this->EMD->sort->get() . ' ' .
            $this->EMD->limit . ' ' .
            $this->EMD->offset;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return db($this->EMD->databaseName)->fetchAll($str, $this->EMD->bind->get(), get_class($this));
    }

    private function get()
    {
        $str = 'SELECT ' . $this->EMD->select()->get() . ' ' . ' FROM ' .
            $this->EMD->from()->get() . ' ' .
            $this->EMD->join()->get() . ' ' .
            $this->EMD->where()->get() . ' ' .
            $this->EMD->group . ' ' .
            $this->EMD->sort()->get() . ' ' .
            $this->EMD->limit . ' ' .
            $this->EMD->offset;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return db($this->EMD->databaseName)->fetch($str, $this->EMD->bind()->get(), get_class($this));
    }

    private function sql($format = true, $exit = false): void
    {
        $str = 'SELECT ' . $this->EMD->select()->get() . ' ' . ' FROM ' .
            $this->EMD->from()->get() . ' ' .
            $this->EMD->join()->get() . ' ' .
            $this->EMD->where()->get() . ' ' .
            $this->EMD->group . ' ' .
            $this->EMD->sort()->get() . ' ' .
            $this->EMD->limit . ' ' .
            $this->EMD->offset;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        if ($format) {
            dump($this->EMD->bind()->get());
            dump($str);
        } else {
            print_r($this->EMD->bind()->get()) . PHP_EOL;
            print ('<br><br>') . PHP_EOL;
            print_r($str) . PHP_EOL;
        }
        if ($exit) {
            exit();
        }
    }

    private function find($id = null)
    {
        if (!$id) {
            return null;
        }
        $result = db($this->EMD->databaseName)->fetch('SELECT * FROM ' . $this->EMD->table . ' WHERE `' . $this->EMD->id . '` = :' . $this->EMD->id . ' ', [$this->EMD->id => $id], get_class($this));
        return $result ? $result : null;
    }

    public static function __callStatic(string $method, array $parameters)
    {
        if (method_exists((new static), $method)) {
            return (new static)->$method(...$parameters);
        }
    }

    public function __call(string $method, array $param)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$param);
        }
    }

    public function __get($property)
    {
        return null;
    }
}
