<?php 

namespace system\core\valid\other;

use system\core\valid\item;

class valid_empty extends item 
{
    protected string $textError = 'Параметр не может быть пустым';
    public function control()
    {
        $orginal = $this->original;
        if (empty($orginal)) {
            $this->setError($this->textError);
            $this->setControl(false);
        }
    }
}