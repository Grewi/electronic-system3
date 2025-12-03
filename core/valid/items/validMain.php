<?php

namespace system\sore\salid\items;
use system\core\valid\item;

class validMain
{
    public static function int(mixed $item): item
    {
        $item = new item();

        return $item;
    }

    private static function regex()
    {
        // preg_match("/^[0-9-]+$/u", (string)$data)
    }
}