<?php

namespace system\core\valid;

use system\inst\classes\functions;
class item
{
    private bool $control = true;

    public function setControl(bool $control): static
    {
        $this->control = $control;
        return $this;
    }

    public function getControl(): bool
    {
        return $this->control;
    }
}