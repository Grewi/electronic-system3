<?php

namespace system\core\valid;

use system\core\valid\validInterface;
abstract class item implements validInterface
{
    protected bool $control = true;
    protected array $errors = [];
    public function setData($data)
    {
        $this->setControl($data['control']);
        array_merge($this->errors, $data['errors']);

    }
    public function setControl(bool $control):static
    {
        $this->control = ($this->control == true ? $control : false);
        return $this;
    }
    public function getControl():bool
    {
        return $this->control;
    }
    public function setError(string $error):static
    {
        $this->errors[] = $error;
        return $this;
    }
    public function getErrors():array
    {
        return $this->errors;
    }
    public function getError():string
    {
        return implode(', ', $this->errors);
    }
}