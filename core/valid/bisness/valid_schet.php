<?php 

namespace system\core\valid\bisness;

use system\core\valid\item;

class valid_schet extends item
{
    protected string $textError = 'Номер счёта может содержать 20 символов';

    public function control()
    {
        if ($this->original && mb_strlen($this->original) != 20 && !is_numeric($this->original)) {
            $this->setError($this->textError);
            $this->setControl(false);
        }
    }

    public function getResult():mixed
    {
        return ($this->control ? (int) $this->original : null);
    }
}