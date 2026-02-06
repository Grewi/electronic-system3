<?php

namespace system\core\config;
use system\core\config\iConfig;

abstract class config implements iConfig
{
    private static $path = APP . '/configs/';
    private $globals = ROOT . '/config.ini';
    private $globalData = [];
    protected $file = '';

    public function __construct(string|null $file = null)
    {
        $a = explode('\\', static::class);
        $this->file = ($file ? $file : array_pop($a) );
        if (file_exists($this->globals)) {
            $this->globalData = parse_ini_file($this->globals, true);
        }       
    }

    /** 
     * Возвращает значение параметра
     */
    public function get(string $param): ?string
    {
        if(isset($this->globalData[$this->file][$param])){
            return $this->globalData[$this->file][$param];
        }
        $ini = self::$path . '.' . $this->file . '.ini';

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
        $ini = self::$path . '.' . $this->file . '.ini';
        if (!file_exists($ini)) {
            $this->update();
        }
        if(isset($this->globalData[$this->file])){
            return array_merge(parse_ini_file($ini), $this->globalData[$this->file]);
        }        
        return parse_ini_file($ini);
    }

    /**
     * Обновляет значения в ini файле
     */
    public function update(): void
    {
        $ini = self::$path . '.' . $this->file . '.ini';
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

}
