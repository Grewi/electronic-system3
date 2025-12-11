<?php 

namespace system\core\valid\bisness;

use system\core\valid\item;

class valid_kpp extends item
{
    protected string $textError = 'КПП указан не корректно';

    public function control()
    {
        if ($this->original && mb_strlen($this->original) != 9 && !is_numeric($this->original)) {
            $this->setError($this->textError);
            $this->setControl(false);
        }
    }

    public function getResult():mixed
    {
        return ($this->control ? (int) $this->original : null);
    }
}