<?php

namespace system\core\text;

use system\core\text\consoleColor;

class text
{
    use consoleColor;

    public static function color(string $text, string $color)
    {
        return self::color[$color] . $text . self::reset;
    }

    public static function purp(string $text): string
    {
        return self::color($text, 'Purple');
    }

    public static function yellow(string $text): string
    {
        return self::color($text, 'Yellow');
    }

    public static function red(string $text): string
    {
        return self::color($text, 'Red');
    }

    public static function green(string $text): string
    {
        return self::color($text, 'Green');
    }

    public static function cyan(string $text): string
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

    public static function warn(string $text, bool $exit = false): void
    {
        echo self::pre() . self::yellow($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function danger(string $text, bool $exit = false): void
    {
        echo self::pre() . self::red($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function success(string $text, bool $exit = false): void
    {
        echo self::pre() . self::green($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function primary(string $text, bool $exit = false): void
    {
        echo self::pre() . self::cyan($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function info(string $text, bool $exit = false): void
    {
        echo self::pre() . self::purp($text) . PHP_EOL;
        if ($exit) {
            exit();
        }
    }

    public static function i(string $text): void
    {
        if (time() % 2 == 0) {
            echo self::pre() . self::color($text, 'On_Black') . " " . time() . " \r";
        } else {
            echo self::pre() . self::color($text, 'On_Yellow') . " " . time() . " \r";
        }
    }

    public static function pre(): string
    {
        return self::color(" ▶ ", 'Green');
    }
}

