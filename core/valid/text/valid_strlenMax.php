<?php 

namespace system\core\valid\text;

use system\core\valid\item;

class valid_strlenMin extends item
{
    protected string $textError = 'Значение не должно быть менее ' . $this->len . ' символов';
    private int $len;

    public function __construct(int $len)
    {
        $this->len = $len;
    }
    public function control()
    {
        if ($this->original && mb_strlen((string)$this->original) < $this->len) {
            $this->setError($this->textError);
            $this->setControl(false);
        }        
    }
}