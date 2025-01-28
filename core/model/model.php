<?php

namespace system\core\model;

use system\core\model\traits\insert;
use system\core\model\traits\update;
use system\core\model\traits\delete;
use system\core\model\traits\save;

use system\core\model\traits\pagination;
use system\core\model\traits\group;
use system\core\model\traits\sortGet;
use system\core\model\traits\filter;

use system\core\model\classes\{
    select, 
    from,
    sort,
    bind,
    where,
    limit,
    join,
};

#[\AllowDynamicProperties]
abstract class model
{
    use insert;
    use update;
    use delete;
    use save;
    use pagination;
    use group;
    use sortGet;
    use filter;

    protected $_databaseName = 'database';
    protected $_table = '';
    protected $_idNumber = 0;
    protected $_id = 'id';
    protected $_paginCount = 20;
    private select $_select;
    private from $_from;
    private where $_where;
    private bind $_bind;
    private sort $_sort;
    private join $_join;
    private limit $_limit;
    protected $_limitDirection = 20;
    protected $_offset = '';
    
    protected $_group = '';

    protected $paginationLine = [];
    protected $paginationPriv = 0;
    protected $paginationNext = 0;
    protected $paginationActive = 0;

    protected $_data = [];

    public function __construct()
    {
        if (empty($this->_table)) {
            $c = explode('\\', get_called_class());
            $this->_table = array_pop($c);
        }
        $this->_select = new select();
        $this->_from = new from();
        $this->_bind = new bind();
        $this->_sort = new sort();
        $this->_limit = new limit();
        $this->_where = new where($this->_bind);
        $this->_join = new join();
        $this->_from->add($this->_table);
    }
        
    private function select(string $select)
    {
        $this->_select->add($select);
        return $this;
    }    

    private function from(string $from): static
    {
        $this->_from->add($from);
        return $this;
    }

    private function whereL(string $col, string $operator, string|int|float $value, bool $or = false): static
    {
        $this->_where->where($col, $operator, $value, $or);
        return $this;
    }

    private function where(string $col, string|int|float $value, bool $or = false): static
    {
        $this->_where->where($col, '=', $value, $or);
        return $this;
    }

    private function whereNull(string $col)
    {
        $this->_where->whereNull($col);
        return $this;
    }

    private function whereNotNull(string $col)
    {
        $this->_where->whereNotNull($col);
        return $this;
    }

    private function whereIn(string $col, array|object $arg)
    {
        $this->_where->whereIn( $col,  $arg);
        return $this;
    }

    private function whereStr(string $str, array $bind = [])
    {
        $this->_where->whereStr( $str,  $bind);
        return $this;
    } 

    private function limit(int $limit): static
    {
        $this->_limit->add($limit);
        return $this;
    }

    private function sort(string $name, string $type = 'asc'): static
    {
        $this->_sort->add($name, $type);
        return $this;
    }

    private function innerJoin(string $tableName, string $firstTable, string $secondaryTable):static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 0);
        return $this;
    }    

    private function leftJoin(string $tableName, string $firstTable, string $secondaryTable):static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 1);
        return $this;
    }

    private function rightJoin(string $tableName, string $firstTable, string $secondaryTable):static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 2);
        return $this;
    }

    private function fullJoin(string $tableName, string $firstTable, string $secondaryTable):static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 3);
        return $this;
    }

    private function crossJoin(string $tableName, string $firstTable, string $secondaryTable):static
    {
        $this->join($tableName, $firstTable, $secondaryTable, 4);
        return $this;
    }    

    private function count(): string
    {
        $str = 'SELECT COUNT(*) as count FROM ' .
            $this->_from . ' ' .
            $this->_leftJoin . ' ' .
            $this->_where . ' ' .
            $this->_group;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return db($this->_databaseName)->fetch($str, $this->_bind->get(), get_class($this))->count;
    }

    private function summ($name): float
    {
        $str = 'SELECT SUM(`' . $name . '`) as `summ` FROM ' .
            $this->_from . ' ' .
            $this->_leftJoin . ' ' .
            $this->_where . ' ' .
            $this->_group;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return (int) db($this->_databaseName)->fetch($str, $this->_bind->get(), get_class($this))->summ;
    }

    private function all(): array
    {
        $str = 'SELECT ' . $this->_select . ' ' . ' FROM ' .
            $this->_from . ' ' .
            $this->_leftJoin . ' ' .
            $this->_where . ' ' .
            $this->_group . ' ' .
            $this->_sort . ' ' .
            $this->_limit . ' ' .
            $this->_offset;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return db($this->_databaseName)->fetchAll($str, $this->_bind->get(), get_class($this));
    }

    private function get()
    {
        $str = 'SELECT ' . $this->_select . ' ' . ' FROM ' .
            $this->_from . ' ' .
            $this->_leftJoin . ' ' .
            $this->_where . ' ' .
            $this->_group . ' ' .
            $this->_sort . ' ' .
            $this->_limit . ' ' .
            $this->_offset;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return db($this->_databaseName)->fetch($str, $this->_bind->get(), get_class($this));
    }

    private function sql($format = true, $exit = false): void
    {
        $str = 'SELECT ' . $this->_select . ' ' . ' FROM ' .
            $this->_from . ' ' .
            $this->_leftJoin . ' ' .
            $this->_where . ' ' .
            $this->_group . ' ' .
            $this->_sort . ' ' .
            $this->_limit . ' ' .
            $this->_offset;
        $str = preg_replace('/\s{2,}/', ' ', $str);
        if($format){
            dump($this->_bind->get());
            dump($str);                 
        }else{
            print_r($this->_bind->get()) . PHP_EOL;
            print('<br><br>') . PHP_EOL;
            print_r($str) . PHP_EOL;       
        }
        if($exit){
            exit();
        }
    }

    private function find($id = null)
    {
        if (!$id) {
            return null;
        }
        $result = db($this->_databaseName)->fetch('SELECT * FROM ' . $this->_table . ' WHERE `' . $this->_id . '` = :' . $this->_id . ' ', [$this->_id => $id], get_class($this));
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
