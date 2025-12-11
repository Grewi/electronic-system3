<?php 

namespace system\core\valid\bisness;

use system\core\valid\item;


//https://github.com/ybelenko/ogrn/tree/master
class valid_ogrn extends item
{
    protected string $textError = 'ОГРН указан не корректно';

    public function control()
    {

        if ($this->original) {
            if(mb_strlen($this->original) == 13 && $this->ogrn($this->original)){
                return;
            }
            if(mb_strlen($this->original) == 15 && $this->ogrnip($this->original)){
                return;
            }            
            $this->setError($this->textError);
            $this->setControl(false);
        }

    }

    public function getResult():mixed
    {
        return ($this->control ? (int) $this->original : null);
    }

    private function ogrn(string $identifier):bool
    {
        $id = strval($identifier);
        if (preg_match('/^\d{13}$/', $id) !== 1 || intval($identifier) <= 0) {
            return false;
        }
        // remainder after division
        $rem = gmp_intval(
            gmp_mod(
                substr($id, 0, -1),
                11
            )
        );
        if (gmp_cmp($rem, 10) === 0) {
            $rem -= 10;
        }
        // control number
        $con = substr($id, -1);
        return (bool)(strval($rem) === $con);
    }    

    private function ogrnip(string $identifier):bool
    {
        $id = strval($identifier);
        if (preg_match('/^\d{15}$/', $id) !== 1 || intval($identifier) <= 0) {
            return false;
        }
        // remainder after division
        $rem = gmp_intval(
            gmp_mod(
                substr($id, 0, -1),
                13
            )
        );
        if (gmp_cmp($rem, 9) === 1) {
            $rem -= 10;
        }
        // control number
        $con = substr($id, -1);
        return (bool)(strval($rem) === $con);
    }    
}