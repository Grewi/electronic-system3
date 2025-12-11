<?php 
 namespace system\core\valid\to;

 use system\core\valid\item;

class valid_toInt extends item
{
    public function control()
    {

    }

    public function getResult():mixed
    {
        return (int) $this->original;
    }
}