<?php 

namespace system\core\valid\number;

use system\core\valid\item;

class vFloat extends item
{
    private string $regex = "/^[0-9\.\,-]+$/u";
    protected string $textError = 'Значение должно быть числом';

    public function control()
    {
        if ($this->original && !preg_match($this->regex, $this->original)) {
            $this->setError($this->textError);
            $this->setControl(preg_match($this->regex, $this->original));
        }
    }

    public function getResult():mixed
    {
        return ($this->control ? (float) $this->original : null);
    }
}