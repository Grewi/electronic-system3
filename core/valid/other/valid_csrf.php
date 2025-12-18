<?php

namespace system\core\valid\other;

use system\core\valid\item;

class valid_csrf extends item
{

    private string $param;
    protected string $textError = 'Ошибка csrf токена';
    public static string $deaultName = 'csrf';

    public function __construct(string $name)
    {
        $this->param = $name;
    }

    public function control()
    {
        $a = null;  
        if (isset($_SESSION['csrf'][$this->param])) {
            $a = $_SESSION['csrf'][$this->param];
        }     

        if (!$a || $a != $this->original) {
            $this->setError($this->textError);
            $this->setControl(false);
        }
        $this->getResult = false;
    } 
}