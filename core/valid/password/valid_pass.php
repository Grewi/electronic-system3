<?php 

namespace system\core\valid\password;

use system\core\valid\item;
use system\core\user\register;

class valid_pass extends item 
{
    protected string $textError = '';
    public int $minLen = 6;

    public function control()
    {
        if ($this->original) {
            if(mb_strlen((string) $this->original) < $this->minLen){
                $this->setError('Минимальная длина пароля ' . $this->minLen . ' символов');
                $this->setControl(false);
            }
        }
    } 

    public function getResult():mixed
    {
        return register::password($this->original);
    }

    public function strong()
    {
            $uppercase = preg_match('/[A-ZА-Я]/', $this->original);
            $lowercase = preg_match('/[a-zа-я]/', $this->original);
            $number    = preg_match('/[0-9]/', $this->original);

            if(!$uppercase || !$lowercase || !$number ) {
                $this->setError('Пароль должен содержать символы верхнего, нижнего регистров и цифры');
                $this->setControl(false);
            }        
    }
}