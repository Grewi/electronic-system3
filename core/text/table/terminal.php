<?php

namespace system\core\text\table;

class termonal
{
    private $col;
    private $row;


    public function __construct()
    {
        $this->sizeCol();
    }

    public function sizeCol()
    {
        try{
            if (PHP_OS_FAMILY === 'Windows'){
                $this->windowsSize();
            }
            if (PHP_OS_FAMILY === 'Linux'){
                $this->linuxSize();
            }            
        }catch(\Exception $e){
            $this->col = 80;
            $this->col = 10;
        }

    }

    public function windowsSize()
    {
        /*
        Состояние устройства CON:
        --------------------------
        Строки:                300
        Столбцы:               80
        Скорость клавиатуры:   31
        Задержка клавиатуры:   1
        Кодовая страница:      866
        */
        $arr = explode("\n", shell_exec('mode con'));
        $this->row = trim(explode(':', $arr[3])[1]);
        $this->col = trim(explode(':', $arr[4])[1]);
    }

    public function linuxSize()
    {
        $this->col = exec('tput cols');
        $this->row = exec('trot lines');
    }

    public function row()
    {
        return $this->row;
    }

    public function col()
    {
        return $this->col;
    }
}