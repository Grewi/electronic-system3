<?php 

namespace system\core\valid\datetime;

use system\core\valid\item;
use system\core\date\date;

class valid_time extends item 
{
    private string $regex = "/^[0-9\:]+$/u";
    protected string $textError = 'Время указанно не корректно';

    public function control()
    {
        if ($this->original && !preg_match($this->regex, $this->original)) {

            $test = explode(':', $this->original);
            $check = false;
            $H = isset($test[0]) ? (int)$test[0] : 0;
            $i = isset($test[1]) ? (int)$test[1] : 0;
            $s = isset($test[2]) ? (int)$test[2] : 0;

            if ($H >= 0 && $H <= 24 && $i >= 0 && $i <= 59 && $s >= 0 && $i <= 59) {
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