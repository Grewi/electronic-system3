<?php

namespace system\core\text\table;

class params
{
    private $w = null; // ширина в символах
    private $c = null; // цвет текста
    private $f = null; // цвет фона
    private $r = null; // равнение l / c / r
    private $z = null; // символ заполнения

    public function width(array $width)
    {
        $this->w = $width;
        return $this;
    }

    public function color(string $color)
    {
        $this->c = $color;
        return $this;
    }

    public function bg(string $bg)
    {
        $this->f = $bg;
        return $this;
    }

    public function r(string $r)
    {
        $this->r = $r;
        return $this;
    }

    public function z(string $z)
    {
        $this->z = $z;
        return $this;
    }

    public function get() : array
    {
        $r = [];
        foreach($this as $a => $i){
            if(!is_null($i)){
                $r[$a] = $i;
            }
        }
        return $r;
    }
}