<?php 

namespace system\core\events;

abstract class events
{
    public function __call($name, $arguments)
    {
        $this->search($name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        (new static)->search($name, $arguments);
    }

    private function search($name, $arguments)
    {
        if (!property_exists($this, $name)) {
            return false;
        }
        foreach ($this->{$name} as $i) {
            if (!class_exists($i)) {
                continue;
            }
            $class = new $i;
            if(!method_exists($class, $name)){
                continue;
            }
            $class->{$name}(...$arguments);
        }
    }

    
}