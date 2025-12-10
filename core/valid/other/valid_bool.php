<?php 

namespace system\core\valid\other;

use system\core\valid\item;

class valid_bool extends item
{
    public function getResult():mixed
    {
        return (bool) $this->original;
    }
}