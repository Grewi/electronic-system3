<?php

namespace system\core\collection;

#[\AllowDynamicProperties]
class collection 
{

    private $collections;

    public function __get($name)
    {
        if(!isset($this->collections[$name])){
            $this->collections[$name] = new collection();
        }
        return $this->collections[$name];
    }

    public function __toString()
    {
        return '';
    }
} 