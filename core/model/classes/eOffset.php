<?php 
namespace system\core\model\classes;

class eOffset
{
    private string $offset;

    public function add(int $offset): void
    {
        $this->offset = ' OFFSET ' . $offset . ' ';
    }

    public function get(): string
    {
        return $this->offset ?? '';
    }

    public function toString(): string
    {
        return $this->get();
    }
}