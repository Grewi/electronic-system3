<?php

namespace system\core\valid;

use Closure;
use system\core\valid\item;
use system\inst\classes\functions;

class valid
{
    private bool $control = true;
    /**
     * control
     * errors
     * original
     * result
     */
    private $data = [];

    public function add(string $name, item $item, callable $function)
    {
        if(isset($this->data[$name])){
            $item->setData($this->data[$name]);
        }
        $function($item);
        $this->data[$name]['control'] = $item->getControl();
        $this->data[$name]['errors'] = $item->getErrors();
        dump($item);
    }

    public function errors(string $name):array
    {
        return isset($this->data[$name]) ? $this->data[$name]['errors'] : [];
    }

    public function control(): bool
    {
        return $this->control;
    }
}