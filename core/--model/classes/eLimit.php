<?php
namespace system\core\model\classes;

class eLimit 
{
    private string $limit;
    public function add(int $limit): void
    {
        $this->limit = ' LIMIT ' . $limit . ' ';
    }

    public function get(): string
    {
        return $this->limit ?? '';
    }

    public function __toString(): string
    {
        return $this->get();
    }
}