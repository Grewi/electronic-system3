<?php

namespace system\core\model\classes;

use system\core\model\traits\wrap;

class eJoin 
{
    private array $join = [];
    private $type = ['INNER', 'LEFT', 'RIGHT', 'FULL', 'CROSS'];

    use wrap;

    public function join(string $tableName, string $firstTable, string $secondaryTable, int $type): void
    {
        $this->join[] = ' ' . $this->type[$type]. ' JOIN ' . $this->wrap($tableName) . ' ON ' . $this->wrap($firstTable) . ' = ' . $this->wrap($secondaryTable) . ' ';
        
        // return $this;
    }

        /**
     * @return string 
     */
    public function get(): string
    {
        if(empty($this->join)){
            return '';
        }
        return ' ' . implode(' ', $this->join) . ' ';        
    }

    /**
     * Summary of __toString
     * @return string
     */
    public function __toString(): string
    {
        return $this->get();
    }
}