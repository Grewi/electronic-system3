<?php 

namespace system\core\valid\text;

use system\core\valid\item;

class valid_text extends item
{
    protected string $textError = 'Значение может содержать только латинские, кириллические символы и цифры';

    public function control()
    {

    }

    public function getResult():mixed
    {
        return ($this->control ? htmlspecialchars($this->original??'') : null);
    }
}