<?php

namespace system\core\valid;

use system\core\valid\item;
use system\inst\classes\functions;

class valid
{
    private bool $control = true;

    public function add(string $name, item $item)
    {

    }

    public function control(): bool
    {
        return $this->control;
    }
}