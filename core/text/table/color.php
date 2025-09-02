<?php

namespace system\core\text\table;

use system\core\text\consoleColor;

class color
{
    use consoleColor;

    public static function text($color)
    {
        return isset(self::textColor[$color]) ? self::textColor[$color] : null;
    }

    public static function textBold(int|null $color)
    {
        return isset(self::textColorB[$color]) ? self::textColorB[$color] : null;
    }

    public static function textLine(int|null $color)
    {
        return isset(self::textColorU[$color]) ? self::textColorU[$color] : null;
    }

    public static function textHigh(int|null $color)
    {
        return isset(self::textColorH[$color]) ? self::textColorH[$color] : null;
    }

    public static function textHighBold(int|null $color)
    {
        return isset(self::textColorBH[$color]) ? self::textColorBH[$color] : null;
    } 
    
    public static function bg($color)
    {
        return isset(self::bgColor[$color]) ? self::bgColor[$color] : null;
    }

    public static function bgHigh(int|null $color)
    {
        return isset(self::bgColorH[$color]) ? self::bgColorH[$color] : null;
    }    
}