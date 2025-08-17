<?php

namespace system\core\text\table;

class color
{
    // Цвет
    const array textColor = [
        "\033[0;30m", # Black
        "\033[0;31m", # Red
        "\033[0;32m", # Green
        "\033[0;33m", # Yellow
        "\033[0;34m", # Blue
        "\033[0;35m", # Purple
        "\033[0;36m", # Cyan
        "\033[0;37m", # White
    ]; 

    // Цвет + жирное начертание
    const array textColorB = [
        "\033[1;30m", # Black
        "\033[1;31m", # Red
        "\033[1;32m", # Green
        "\033[1;33m", # Yellow
        "\033[1;34m", # Blue
        "\033[1;35m", # Purple
        "\033[1;36m", # Cyan
        "\033[1;37m", # White        
    ];

    // Подчёркивание
    const array textColorU = [
        "\033[4;30m", # Black
        "\033[4;31m", # Red
        "\033[4;32m", # Green
        "\033[4;33m", # Yellow
        "\033[4;34m", # Blue
        "\033[4;35m", # Purple
        "\033[4;36m", # Cyan
        "\033[4;37m", # White
    ];

    // Яркий
    const array textColorH = [
        "\033[0;90m", # Black
        "\033[0;91m", # Red
        "\033[0;92m", # Green
        "\033[0;93m", # Yellow
        "\033[0;94m", # Blue
        "\033[0;95m", # Purple
        "\033[0;96m", # Cyan
        "\033[0;97m", # White        
    ];

    // Жирный яркий
    const array textColorBH = [
        "\033[1;90m", # Black
        "\033[1;91m", # Red
        "\033[1;92m", # Green
        "\033[1;93m", # Yellow
        "\033[1;94m", # Blue
        "\033[1;95m", # Purple
        "\033[1;96m", # Cyan
        "\033[1;97m", # White        
    ];

    // Фон
    const array bgColor = [
        "\033[40m", # Black
        "\033[41m", # Red
        "\033[42m", # Green
        "\033[43m", # Yellow
        "\033[44m", # Blue
        "\033[45m", # Purple
        "\033[46m", # Cyan
        "\033[47m", # White        
    ];

    //Яркий фон
    const array bgColorH = [
        "\033[0;100m", # Black
        "\033[0;101m", # Red
        "\033[0;102m", # Green
        "\033[0;103m", # Yellow
        "\033[0;104m", # Blue
        "\033[0;105m", # Purple
        "\033[0;106m", # Cyan
        "\033[0;107m", # White
    ];

    const array colorsList = [
        0 => 'black', 
        1 => 'red', 
        2 => 'green', 
        3 => 'yellow', 
        4 => 'blue', 
        5 => 'purple', 
        6 => 'cyan', 
        7 => 'white'
    ];

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