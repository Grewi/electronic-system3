<?php

namespace system\core\model\classes;
use system\core\model\classes\eBind;
use system\core\model\traits\wrap;

class eWhere
{
    private $whereSeparator;
    private $where;
    public eBind $bind;

    use wrap;

    public function __construct()
    {
        $this->bind = new eBind;
    }

    private function separatorWhere()
    {
        if ($this->whereSeparator) {
            $wsep = $this->whereSeparator;
            $this->whereSeparator = null;
        } else {
            $wsep = ' AND';
        }
        return empty($this->where) ? ' WHERE' : $wsep;
    }

    public function where($p1, $p2, $p3, $or): void
    {
        if($or){
            $this->whereSeparator = ' OR';
        }
        $sep = $this->separatorWhere();
        $pp1 = str_replace('.', '_', $p1) . '_' . $this->bind->getNumber();
        $this->where .= $sep . ' ' . $this->wrap($p1) . ' ' . $p2 . ' :' . $pp1 . ' ';
        $this->bind->add($pp1, $p3);
    }

    public function whereNull(string $col): void
    {
        $sep = $this->separatorWhere();
        $this->where .= $sep . ' ' . $this->wrap($col) . ' IS NULL ';
    }

    public function whereNotNull(string $col): void
    {
        $sep = $this->separatorWhere();
        $this->where .= $sep . ' ' . $this->wrap($col) . ' IS NOT NULL ';
    }

    public function whereIn(string $p1, array|object $arg): void
    {
        $sep = $this->separatorWhere();
        $arr = [];
        foreach ($arg as $i) {
            $pp1 = str_replace('.', '_', $p1) . '_' . $this->bind->getNumber();
            $this->bind->add($pp1, $i);
            $arr[] = ':'.$pp1;
        }
        $str = implode(',', $arr);
        $this->where .= $sep . ' ' . $this->wrap($p1) . ' IN (' . $str . ')';
    }

    public function whereStr(string $str, array $bind): void
    {
        $sep = $this->separatorWhere();
        $this->where .=  ' ' . $sep . ' ' . $str . ' ';
        foreach ($bind as $key => $i) {
            $this->bind->add($key, $i);
        }
    }

    public function get(): string
    {
        return $this->where ?? '';
    }

    public function __toString(): string
    {
        return $this->get();
    }

}