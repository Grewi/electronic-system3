<?php 

namespace system\core\valid\datetime;

use system\core\valid\item;
use system\core\date\date;

class valid_date extends item 
{
    private string $regex = "/^[0-9\-]+$/u";
    protected string $textError = 'Указана не существующая дата';

    public function control()
    {
        if ($this->original && !preg_match($this->regex, $this->original)) {

            $test = explode('-', trim($this->original));
            $check = false;
            if (@checkdate((int)$test[1], (int)$test[2], (int)$test[0])) {
                $check = true;
            }

            if(!$check){
                $this->setError($this->textError);
                $this->setControl(false);                 
            }
        }
    }

    public function getResult():mixed
    {
        return ($this->control ? date::create($this->original) : null);
    }
}