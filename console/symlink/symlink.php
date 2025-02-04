<?php
namespace system\console\symlink;
use system\core\text\text;


class symlink
{
    public $dir = APP . '/system/symlink';

    public function index()
    {
        if (!file_exists($this->dir . '/symlink.php')) {
            createDir($this->dir);
            $data = [
                'namespace' => APP_NAME . '\\system\\symlink'
            ];
            $this->view(__DIR__ . '/view', $data, $this->dir . '/symlink.php');
            text::success('Был создан файл ' . $this->dir . '/symlink.php' . '  Необходимо его заполнить');
            text::primary('Операция завершена', true);
        }

        $c = '\\' . APP_NAME . '\\system\\symlink\\symlink';
        $symlink = new $c();

        if (count($symlink->list) <= 0) {
            text::warn('В файле ' . $this->dir . '/symlink.php нет данных для создания ссылок');
        } else {
            $symlink->list();
        }
        text::primary('Операция завершена', true);
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