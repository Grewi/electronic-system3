<?php
namespace system\core\model\classes;
use system\core\model\traits\wrap;

class eGroup
{
    use wrap;
    private string $group;
    public function add(string $group): void
    {
        $this->group = ' GROUP BY ' . $this->wrap($group) . ' ';
    }

    public function get(): string
    {
        return $this->group ?? '';
    }

    public function __toString(): string
    {
        return $this->get();
    }
}