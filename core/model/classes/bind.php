<?php

namespace system\core\model\classes;

class bind
{
    private $bind = [];

    public function add(string $key, string $value): void
    {
        $this->bind[$key] = $value;
    }

    public function getNumber():int
    {
        return count($this->bind);
    }

    public function get(): array
    {
        return $this->bind;
    }
}