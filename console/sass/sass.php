<?php
namespace system\console\sass;

use system\core\text\text;

class sass
{
    private function start()
    {
        $fileSass = APP . '/system/sass/sass.php';
        $classSass = '\\' . APP_NAME . '\system\\sass\\sass';
        if (!file_exists($fileSass)) {
            $dir = APP . '/system/sass';
            createDir($dir);
            $data = [
                'namespace' => APP_NAME . '\\system\\sass'
            ];
            $this->view(__DIR__ . '/view', $data, $dir . '/sass.php');
            text::warn('Создан файл ' . $fileSass);
            text::warn('Для компиляции стилей необходимо его заполнить');
            text::info('http://grewi.ru/blogs/27-kompilyaciya-css-faylov', true);
        }
        return new $classSass();
    }
    public function compile()
    {
        $ARGV = ARGV;
        $sass = $this->start();
        if (is_array($ARGV) && isset($ARGV[2])) {
            $name = $ARGV[2];
            $sass->name($name)->compile();
        } else {
            $sass->list()->compile();
        }
        exit();
    }

    public function info()
    {
        $sass = $this->start();
        $sass->info();
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



