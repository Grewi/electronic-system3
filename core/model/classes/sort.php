<?php
namespace system\core\model\classes;

use system\core\model\traits\wrap;

class sort
{
    private $sort = [];

    use wrap;

    /**
     * 
     * Добавить сортировку
     * @param string $name наименование поля в таблице
     * @param string $type направление сортировки
     * @return void
     */
    public function add(string $name, string $type = 'asc')
    {
        $type = strtolower($type);
        $this->sort[] = $this->wrap($name) . ($type == 'asc' ? ' ASC' : ' DESC');
    }

    /**
     * @return string 
     */
    public function get(): string
    {
        if(empty($this->sort)){
            return '';
        }
        return ' ORDER BY ' . implode(',', $this->sort) . ' ';        
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