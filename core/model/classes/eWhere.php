<?php

namespace system\core\model\classes;

use system\core\model\classes\eBind;
use system\core\model\traits\wrap;

class eWhere
{
    private $whereSeparator;
    private $where;
    public eBind $bind;
    private $level;

    use wrap;

    public function __construct(int $level = 0)
    {
        $this->bind = new eBind;
        $this->level = $level;
    }

    private function separatorWhere()
    {
        if ($this->whereSeparator) {
            $wsep = $this->whereSeparator;
            $this->whereSeparator = null;
        } else {
            $wsep = ' AND';
        }
        if ($this->level == 0) {
            return empty($this->where) ? ' WHERE' : $wsep;
        } else {
            return empty($this->where) ? '' : $wsep;
        }
    }

    public function or(): void
    {
        $this->whereSeparator = ' OR';
    }

    public function where($p1, $p2, $p3, $or): void
    {
        if ($or) {
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
            $arr[] = ':' . $pp1;
        }
        $str = implode(',', $arr);
        $this->where .= $sep . ' ' . $this->wrap($p1) . ' IN (' . $str . ')';
    }

    public function whereLike(string $col, string $str): void
    {
        $sep = $this->separatorWhere();
        $this->where .= $sep . ' ' . $this->wrap($col) . ' LIKE "%' . $str . '%" ';
    }

    public function whereLikeStart(string $col, string $str): void
    {
        $sep = $this->separatorWhere();
        $this->where .= $sep . ' ' . $this->wrap($col) . ' LIKE "%' . $str . '" ';
    }

    public function whereLikeEnd(string $col, string $str): void
    {
        $sep = $this->separatorWhere();
        $this->where .= $sep . ' ' . $this->wrap($col) . ' LIKE "' . $str . '%" ';
    }

    public function whereStr(string $str, array $bind): void
    {
        $sep = $this->separatorWhere();
        $this->where .=  ' ' . $sep . ' ' . $str . ' ';
        foreach ($bind as $key => $i) {
            $this->bind->add($key, $i);
        }
    }

    public function active(bool|int $active, string|null $table = null): void
    {
        $sep = $this->separatorWhere();
        $p1 = $table ? $table . '.active' : 'active';
        $pp1 = str_replace('.', '_', $p1) . '_' . $this->bind->getNumber();
        $this->where .= $sep . ' ' . $this->wrap($p1) . ' = :' . $pp1 . ' ';
        $this->bind->add($pp1, ($active ? 1 : 0));
    }

    public function slug(string $slug, string|null $table = null): void
    {
        $sep = $this->separatorWhere();
        $p1 = $table ? $table . '.slug' : 'slug';
        $pp1 = str_replace('.', '_', $p1) . '_' . $this->bind->getNumber();
        $this->where .= $sep . ' ' . $this->wrap($p1) . ' = :' . $pp1 . ' ';
        $this->bind->add($pp1, $slug);
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
