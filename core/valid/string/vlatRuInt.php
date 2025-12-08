<?php 

namespace system\core\valid\string;

use system\core\valid\item;

class vlatRuInt extends item
{
    private string $regex = "/^[\s a-zA-Z0-9а-яА-ЯёЁ\-_]+$/u";
    protected string $textError = 'Значение может содержать только латинские, кириллические символы и цифры';

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