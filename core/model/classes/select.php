<?php

namespace system\core\model\classes;

class select 
{
    private string $select;
    private string $default = '*';

    public function add(string $select)
    {
        $this->select = $select;
    }

    public function get(): string
    {
        return $this->select ?? $this->default;
    }

    public function __toString(): string
    {
        return $this->get();
    }
}