<?php 

namespace system\core\valid\password;

use system\core\valid\item;
use system\core\valid\password\valid_pass;
use system\core\database\database;

class valid_passConfirm extends item 
{
    protected string $textError = 'Пароли не совпадают';
    public valid_pass $pass;

    public function __construct(valid_pass $pass)
    {
        $this->pass = $pass;
    }

    public function control()
    {
        if ($this->original) {
            if($this->pass->getOriginal() != $this->getOriginal()){
                $this->setError($this->textError);
                $this->setControl(false);                 
            }
        }
    } 

        public function getResult():mixed
    {
        return null;
    }
}