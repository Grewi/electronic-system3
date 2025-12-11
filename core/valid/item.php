<?php

namespace system\core\valid;

use system\core\valid\validInterface;
abstract class item implements validInterface
{
    protected bool $control = true;
    protected array $errors = [];
    protected mixed $original = null;
    protected mixed $result = null;
    protected string $textError = '';

    public bool $getResult = true;

    public function setData($data)
    {
        $this->setControl($data['control']);
        array_merge($this->errors, $data['errors']);

    }
    public function setControl(bool $control):static
    {
        $this->control = ($this->control == true ? $control : false);
        if(!$this->control){
            // $this->setError($this->textError);
        }
        
        return $this;
    }
    public function getControl():bool
    {
        return $this->control;
    }
    public function setError(string $error):static
    {
        $this->errors[0] = $error;
        return $this;
    }
    public function addError(string $error):static
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

    public function setOriginal(mixed $original):static
    {
        $this->original = $original;
        return $this;
    }

    public function getOriginal():mixed
    {
        return $this->original;
    }

    public function setResulr(mixed $original):static
    {
        $this->original = $original;
        return $this;
    }

    public function getResult():mixed
    {
        return $this->original;
    }

    public function setErrorText(string $text):static
    {
        $this->textError = $text;
        return $this;
    }
    
    public function control()
    {

    }
}