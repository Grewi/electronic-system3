<?php

namespace system\core\config;

use system\core\text\text;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');
class config
{
    private array $config;

    public function __construct()
    {
        $appConfig = APP . '/configs/config.ini';
        $globalConfig = ROOT . '/config.ini';
        $cache = APP . '/cache/config.json';

        //Время последнего изменения в конфигах и файлах в кеше
        $timeCacheFile = file_exists($cache) ? filemtime($cache) : 0;
        $timeApp = file_exists($appConfig) ? filemtime($appConfig) : 0;
        $timeGlobal = file_exists($globalConfig) ? filemtime($globalConfig) : 0;
        //Файл изменился
        if (file_exists($appConfig) && $timeApp > $timeCacheFile) {
            $this->config = $this->updateConfig();
            file_put_contents($cache, json_encode($this->config));
        } elseif (file_exists($globalConfig) && $timeGlobal > $timeCacheFile) {
            $this->config = $this->updateConfig();
            file_put_contents($cache, json_encode($this->config));
        } elseif (file_exists($cache)) {
            $this->config = json_decode(file_get_contents($cache), true);
        } else {
            $this->config = $this->updateConfig();
            file_put_contents($cache, json_encode($this->config));
        }
    }

    public function getConfig(string $file, string $param)
    {
        if (!isset($this->config[$file][$param])) {
            return null;
        }
        return $this->config[$file][$param];
    }

    public function allConfig(string $file)
    {
        if (!isset($this->config[$file])) {
            return null;
        }
        return $this->config[$file];
    }

    public function allConfigs()
    {
        return $this->config ?? [];
    }

    public function control()
    {
        $appConfig = APP . '/configs/config.ini';
        $globalConfig = ROOT . '/config.ini';
        if (file_exists($globalConfig)) {
            $this->createExamle($globalConfig);
        }
        if (file_exists($appConfig)) {
            $this->createExamle($appConfig);
        }
    }

    public function testing()
    {
        $appConfig = APP . '/configs/config.ini';
        $globalConfig = ROOT . '/config.ini';
        if (file_exists($globalConfig)) {
            $this->test($globalConfig);
        }
        if (file_exists($appConfig)) {
            $this->test($appConfig);
        }
    }


    private function createExamle(string $path)
    {
        $arr = parse_ini_file($path, true);
        $exPath = str_replace('config.ini', 'examle.config.ini', $path);
        $ex = null;
        if (file_exists($exPath)) {
            $ex = parse_ini_file($exPath, true);
        }
        if (!$ex) {
            $ex = [];
        }
        $str = '';
        $add = [];

        foreach (array_replace_recursive($arr, $ex) as $file => $param) {
            $str .= '[' . $file . ']' . PHP_EOL;
            foreach ($param as $key => $value) {
                if (isset($ex[$file][$key])) {
                    $str .= $key . ' = ' . $ex[$file][$key] . PHP_EOL;
                } else {
                    $str .= $key . ' = ' . PHP_EOL;
                    $add[] = $file . ' => ' . $key;
                }
            }
            $str .= PHP_EOL;
        }
        if (!empty($add)) {
            text::success('В файл examle.config.ini были добавлены новые параметры ' . PHP_EOL . implode(', ' . PHP_EOL, $add));
        }
        file_put_contents($exPath, $str);
    }


    private function test(string $path)
    {
        $arr = parse_ini_file($path, true);
        $exPath = str_replace('config.ini', 'examle.config.ini', $path);
        $ex = null;
        if (file_exists($exPath)) {
            $ex = parse_ini_file($exPath, true);
        }
        if (!$ex) {
            $ex = [];
        }
        text::primary('Проверка параметров конфигурации ' . $path);
        foreach ($ex as $file => $param) {
            if (!isset($arr[$file])) {
                text::warn('В файле ' . $path . ' отсутствует секция параметров ' . $file);
                continue;
            }
            foreach ($param as $key => $value) {
                if (!isset($arr[$file][$key])) {
                    text::danger('В файле '  . $path . ' в секции ' . $file . ' отсутствует параметр ' . $key);
                }
            }
        }
    }

    private function updateConfig()
    {
        $appConfig = APP . '/configs/config.ini';
        $globalConfig = ROOT . '/config.ini';
        $a = $g = [];
        if (!file_exists($appConfig) && !file_exists($globalConfig)) {
            throw new \Exception('Отсутствуют файлы конфигурации');
        }
        if (file_exists($appConfig)) {
            $a = parse_ini_file($appConfig, true);
            if (!$a) {
                throw new \Exception('Ошибка чтения файла конфигурации ' . $appConfig);
            }
        }
        if (file_exists($globalConfig)) {
            $g = parse_ini_file($globalConfig, true);
            if (!$g) {
                throw new \Exception('Ошибка чтения файла конфигурации ' . $globalConfig);
            }
        }
        return array_replace_recursive($a, $g);
    }
}
