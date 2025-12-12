<?php 

namespace system\core\valid\other;

use system\core\valid\item;

class valid_email extends item 
{
    private string $regex = "/^[a-z0-9+._-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/u";
    protected string $textError = 'Данные не похожи на электронную почту';

    public function control()
    {
        if ($this->original && !preg_match($this->regex, $this->original)) {
            $this->setError($this->textError);
            $this->setControl(false);
        }
    }
}