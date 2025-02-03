<?php

namespace system\console\model;
use system\core\text\text;
class createModel
{
    private $className = '';
    private $path = '';
    private $pathDir = '';
    private $namespace = '';

    public function index()
    {
        $ARGV = ARGV;
        if (is_array($ARGV)) {
            if (!isset(ARGV[2])) {
                text::danger('Не указан обязательный параметр');
                text::warn('Необходимо указать имя модели.');
                text::warn('Например: "users" создаст "' . MODELS . '/users.php"', true);
            }
            $parametr = $ARGV[2];
        } else {
            text::danger('Не удалось получить необходимые параметры', true);
        }

        $ArrParam = explode('/', $parametr);
        $this->className = array_pop($ArrParam);
        $this->path = MODELS . '/' . $parametr . '.php';
        $this->pathDir = MODELS . '/' . implode('/', $ArrParam);
        $modelPath = str_replace(ROOT . '/', '', MODELS);
        $modelPath = str_replace('/', '\\', $modelPath);
        $ArrParam = array_merge([$modelPath], $ArrParam);
        $this->namespace = implode('\\', $ArrParam);
        $this->save();
        text::primary('Операция завершена', true);
    }

    private function save()
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
            text::success('Создан файл модели "' . $this->className . '"');
        }else{
            text::warn('Файл модели уже существует.');
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