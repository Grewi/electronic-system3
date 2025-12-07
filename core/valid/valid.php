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
    private array $data = [];

    /**
     * Массив изначальных значений key => value
     */
    private array $original = [];

    public function add(string $name, item $item, callable|null $function)
    {
        if(isset($this->data[$name])){
            // $item->setData($this->data[$name]);
        }
        
        if(isset($this->original[$name])){
            $item->setOriginal($this->original[$name]);
        }
        $function($item);
        $item->control();
        $this->data[$name]['control'] = $item->getControl();
        $this->data[$name]['errors'] = $item->getErrors();
        $this->data[$name]['original'] = $item->getOriginal();
        $this->data[$name]['result'] = $item->getResult();
        $this->setControl($item->getControl());
    }

    public function errors(string $name):array
    {
        return isset($this->data[$name]) ? $this->data[$name]['errors'] : [];
    }

    public function control(): bool
    {
        return $this->control;
    }

    public function setOriginalArray(array $data){
        $this->original = $data;
    }

    private function setControl(bool $control)
    {
        if($this->control){
            $this->control = $control;
        }
    }
}