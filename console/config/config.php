<?php

namespace system\console\config;


class config
{
    public function control()
    {
        (new \system\core\config\config())->control();
    }
}
