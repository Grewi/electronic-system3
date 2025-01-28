<?php

namespace system\core\model\classes;

use system\core\model\traits\wrap;

class from 
{
    private string $from;

    use wrap;

    public function add(string $table): void
    {
        $this->from = $this->wrap($table);
    }

    public function get(): string
    {
        return $this->from ?? '';
    }

    public function toString(): string
    {
        return $this->get();
    }
}