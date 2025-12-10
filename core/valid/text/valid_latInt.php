<?php 

namespace system\core\valid\text;

use system\core\valid\item;

class valid_latInt extends item
{
    private string $regex = "/^[\s a-zA-Z0-9\-_]+$/u";
    protected string $textError = 'Значение может содержать только латинские символы и цифры';

    public function control()
    {
        if ($this->original && !preg_match($this->regex, $this->original)) {
            $this->setError($this->textError);
            $this->setControl(preg_match($this->regex, $this->original));
        }
    }

    public function getResult():mixed
    {
        return ($this->control ? (string) $this->original : null);
    }
}