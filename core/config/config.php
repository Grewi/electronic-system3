<?php

namespace system\core\config;

abstract class config
{
    private static $path = APP . '/configs/';
    /** 
     * Возвращает значение параметра
     */
    public function get(string $param): ?string
    {
        $ini = self::$path . '.' . $this->returnClassName() . '.ini';

        //Парсим ini файл
        if (file_exists($ini)) {
            $iniArr = parse_ini_file($ini);
        }
        //Если есть значение в ini возвращаем его
        if (isset($iniArr[$param])) {
            return $iniArr[$param];
        }

        self::update();
        $iniArr = parse_ini_file($ini);
        //Если есть значение в ini возвращаем его
        if (isset($iniArr[$param])) {
            return $iniArr[$param];
        } else {
            return null;
        }
    }

    /**
     * Возвращаетс значения всех параметров массивом
     */
    public function all(): ?array
    {
        $ini = self::$path . '.' . $this->returnClassName() . '.ini';
        if (!file_exists($ini)) {
            $this->update();
        }
        return parse_ini_file($ini);
    }

    /**
     * Обновляет значения в ini файле
     */
    public function update(): void
    {
        $ini = self::$path . '.' . $this->returnClassName() . '.ini';
        $iniArr = [];
        if (file_exists($ini)) {
            $iniArr = parse_ini_file($ini);
        }
        $config = array_merge($this->set(), $iniArr);
        $data = '';
        foreach ($config as $key => $i) {
            $data .= $key . ' = ' . $i . PHP_EOL;
        }
        file_put_contents($ini, $data);
    }

    private function returnClassName(): string
    {
        return array_reverse(explode('\\', static::class))[0];
    }
}
