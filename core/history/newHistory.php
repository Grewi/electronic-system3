<?php 
namespace system\core\history;

class newHistory
{
    public static function start()
    {
        $hash = md5(time() + rand(0, 9999999));
        header('History: ' . $hash);
    }
}