<?php

namespace system\core\valid;

use Closure;
use system\core\valid\item;
use system\core\valid\other\valid_bool;
use system\core\valid\other\valid_csrf;
use system\core\valid\other\valid_email;
use system\core\valid\other\valid_empty;
use system\core\valid\number\valid_float;
use system\core\valid\number\valid_int;
use system\core\valid\number\valid_min;
use system\core\valid\number\valid_max;
use system\core\valid\text\valid_latInt;
use system\core\valid\text\valid_latRuInt;
use system\core\valid\text\valid_ru;
use system\core\valid\text\valid_text;

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

    /**
     * 
     * @param string $name
     * @param item|string|array $item
     * @param callable|null $function
     * @return void
     */
    public function add(string $name, item|string|array $item, callable|null $function = null)
    {
        if (!isset($this->data[$name])) {
            // return;
        }
        if(is_string($item) || is_array($item)){
            $item = $this->searchItemClass($item);
        }

        if (isset($this->original[$name])) {  
            $item->setOriginal($this->original[$name]);
        }
        if ($function) {
            $function($item);
        }
        $item->control();
        $this->data[$name]['control'] = $item->getControl();
        $this->data[$name]['errors'] = $item->getErrors();
        $this->data[$name]['original'] = $item->getOriginal();
        if ($item->getResult || !isset($this->data[$name])) {
            $this->data[$name]['result'] = $item->getResult();
        }

        $this->setControl($item->getControl());
    }

    private function searchItemClass(string|array $item): item
    {
        $name = is_array($item) ? array_shift($item) : $item;
        $object = match($name){
            'bool'     => new valid_bool(),
            'csrf'     => new valid_csrf(...$item),
            'email'    => new valid_email(),
            'empty'    => new valid_empty(),
            'float'    => new valid_float(),
            'int'      => new valid_int(),
            'min'      => new valid_min(...$item),
            'max'      => new valid_max(...$item),
            'latInt'   => new valid_latInt(),
            'latRuInt' => new valid_latRuInt(),
            'valid_ru' => new valid_ru(),
            'text'     => new valid_text(),
            default    => null,
        };
        if(!$object){
            throw new \Exception('Error type valid item');
        }
        return $object;
    }

    public function valid_int(string $name, callable|null $function = null)
    {
        $this->add($name, new valid_int(), $function);
    }

    public function error(string $name): array
    {
        return isset($this->data[$name]) ? $this->data[$name]['errors'] : [];
    }

    public function errors(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors'])) {
                $errors[$a] = $i['errors'];
            }
        }
        return $errors;
    }

    public function errorList(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors']) && count($i['errors']) > 0) {
                foreach ($i['errors'] as $er) {
                    $errors[] = $er;
                }
            }
        }
        return $errors;
    }

    public function original(string $name):mixed
    {
        return (isset($this->data[$name]['original']) ? $this->data[$name]['original'] : null);
    }

    public function originals(): array
    {
        $original = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['original'])) {
                $original[$a] = $i['original'];
            }
        }
        return $original;
    }

    public function results(): array
    {
        $original = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['result'])) {
                $original[$a] = $i['result'];
            }
        }
        return $original;
    }

    public function result(string $name):mixed
    {
        return (isset($this->data[$name]['result']) ? $this->data[$name]['result'] : null);
    }    

    public function control(): bool
    {
        return $this->control;
    }

    public function setOriginalArray(array $data)
    {
        $this->original = $data;
    }

    public function setRequest(): void
    {
        $this->original = $_REQUEST;
    }

    private function setControl(bool $control)
    {
        if ($this->control) {
            $this->control = $control;
        }
    }
}