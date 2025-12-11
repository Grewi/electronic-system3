<?php 

namespace system\core\valid\datetime;

use system\core\valid\item;
use system\core\date\date;

class valid_datetime extends item 
{
    protected string $textError = 'Дата указанна не корректно';

    public function control()
    {
        if ($this->original) {
            try{
                date::create($this->original);
                $check = true;
            }catch(\Exception $e){
                $check = false;
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