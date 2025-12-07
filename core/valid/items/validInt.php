<?php 

namespace system\core\valid\items;

use system\core\valid\item;

class validInt extends item
{
    private string $regex = "/^[0-9-]+$/u";
    protected string $textError = 'Значение должно быть целым числом';

    public function control()
    {
        if (!preg_match("/^[0-9-]+$/u", $this->original)) {
            $this->setError($this->textError);
            $this->setControl(preg_match("/^[0-9-]+$/u", $this->original));
        }
    }
}