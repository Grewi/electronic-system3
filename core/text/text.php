<?php
namespace system\core\text;

use my\controllers\stat\stat;
use system\core\text\consoleColor;

class text
{
    use consoleColor;

    public static function color(string $text, string $color)
    {
        return self::color[$color] . $text . self::reset;
    }

    public static function purp($text)
    {
        return self::color($text, 'Purple');
    }

    public static function yellow($text)
    {
        return self::color($text, 'Yellow');
    }

    public static function red($text)
    {
        return self::color($text, 'Red');
    }

    public static function green($text)
    {
        return self::color($text, 'Green');
    }

    public static function cyan($text)
    {
        return self::color($text, 'Cyan');
    }    

    public static function print(string $text, bool $exit = false): void
    {
        echo self::pre() . $text . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function warn($text, $exit = false)
    {
        echo self::pre() . self::yellow($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function danger($text, $exit = false)
    {
        echo self::pre() . self::red($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function success($text, $exit = false)
    {
        echo self::pre() . self::green($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function primary($text, $exit = false)
    {
        echo self::pre() . self::cyan($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }    

    public static function info($text, $exit = false)
    {
        echo self::pre() . self::purp($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function i($text)
    {
        if(time() % 2 == 0){
            echo self::pre() . self::color($text, 'On_Black') . " " . time() . " \r";
        }else{
            echo self::pre() . self::color($text, 'On_Yellow') . " " . time() . " \r";
        }
        
    }
    
    public static function pre()
    {
        return self::color(" ▶ ", 'Green');
    }

}