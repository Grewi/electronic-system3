<?php 
 namespace system\core\valid\to;

 use system\core\valid\item;

class valid_toNull extends item
{
    public function control()
    {

    }

    public function getResult():mixed
    {
        $original = $this->original;
        return empty($original) ? null : $this->original;
    }
}