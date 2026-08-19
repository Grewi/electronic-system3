<?php

namespace system\core\user;

class register
{
    /**
     * @param string $pass 
     * @return string 
     */
    public static function password(string $pass): string
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }
}

