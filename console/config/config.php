<?php
namespace system\console\config;
use system\core\text\text;
class config
{
    private $dir = [];
    private $path = APP . '/configs/';

    public function actual(): void
    {
        $this->scan();
        $this->comparison();
        text::primary('Файлы конфигураций обновленны! Операция завершена', true);
    }

    private function scan(): void
    {
        $dir = scandir($this->path);
        foreach ($dir as $i) {
            if ($i == '.' || $i == '..') {
                continue;
            }

            $file = pathinfo($this->path . $i);
            if ($file['extension'] == 'php') {
                $this->dir[] = $file['filename'];
            }
        }
    }

    private function comparison(): void
    {
        foreach ($this->dir as $i) {
            if (file_exists($this->path . '.' . $i . '.ini')) {
                $ini = parse_ini_file($this->path . '.' . $i . '.ini');
                text::warn('Файл конфигурации ".' . $i . '.ini" обновлён.');
            } else {
                $ini = [];
                text::success('Файл конфигурации ".' . $i . '.ini" создан.');
            }

            $class = '\\' . APP_NAME . '\\configs\\' . $i;
            $configs = new $class();
            $php = $configs->set();
            $result = array_merge($php, $ini);
            $ini = '';
            foreach ($result as $key => $ii) {

                $ini .= $key . ' = ' . $ii . PHP_EOL;
            }
            
            file_put_contents($this->path . '.' . $i . '.ini', $ini);
        }
    }
}