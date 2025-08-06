<?php

namespace system\core\app;

#[\AllowDynamicProperties]
class collection 
{

    private $collections = [];

    public function __get($name)
    {
        return $this->collections[$name];
    }

    public function __set($name, $value)
    {
        $this->collections[$name] = $value;
    }

    public function __toString()
    {
        return '';
    }

    public function clean() : void
    {
        $this->collections = [];
    }

    public function __debugInfo(): array
    {
        return $this->collections;
    }
} 