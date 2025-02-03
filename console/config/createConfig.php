<?php

namespace system\console\config;
use system\core\text\text;
class createConfig
{
    private $className = '';
    private $path = '';
    private $pathDir = '';
    private $namespace = '';

    public function index(): void
    {
        $ARGV = ARGV;
        if (is_array($ARGV)) {
            if (!isset(ARGV[2])) {
                text::danger('Не указан обязательный параметр');
                text::warn('Необходимо указать наименование файла конфигурации.');
                text::warn('Например: "text" создаст "app/configs/test.php"', true);
            }
            $parametr = $ARGV[2];
        } else {
            text::danger('Не удалось получить необходимые параметры', true);
        }

        $ArrParam = explode('/', $parametr);
        $this->className = array_pop($ArrParam);
        $this->path = APP . '/configs/' . $parametr . '.php';
        $this->pathDir = APP . '/configs/' . implode('/', $ArrParam);
        $ArrParam = array_merge([APP_NAME, 'configs'], $ArrParam);
        $this->namespace = implode('\\', $ArrParam);
        $this->save();
        text::primary('Операция завершена', true);
    }

    private function save(): void
    {
        if (!file_exists($this->path)) {

            if (!file_exists($this->pathDir)) {
                mkdir($this->pathDir, 0755, true);
            }
            $data = [
                'namespace' => $this->namespace,
                'className' => $this->className,
            ];
            $this->view(__DIR__ . '/view', $data, $this->path);
            text::success('Файл конфигурации создан.');
        }else{
            text::warn('Файл конфигурации уже существует.');
        }
    }

    /**
     * Summary of view
     * @param string $view Путь к шаблону 
     * @param array $data Массив с данными
     * @param string $file Путь к новому файлу
     * @return void
     */
    private function view(string $view, array $data, string $file)
    {
        $layout = file_get_contents($view);
        foreach ($data as $a => $i) {
            $layout = str_replace('{{' . $a . '}}', $i, $layout);
        }
        file_put_contents($file, $layout);
    }
}