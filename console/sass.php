<?php
namespace system\console;

use system\core\text\text;

class sass
{
    private function start()
    {
        $fileSass = APP . '/system/sass/sass.php';
        $classSass = '\\' . APP_NAME . '\system\\sass\\sass';
        if(!file_exists($fileSass)){
            $this->createSysyemSass();
            text::warn('Создан файл ' . $fileSass); 
            text::warn('Для компиляции стилей необходимо его заполнить');
            text::info('http://grewi.ru/blogs/27-kompilyaciya-css-faylov', true);
        }
        return new $classSass();
    }
    public function compile()
    {
        $sass = $this->start();
        if(isset(ARGV[2])){
            $name = ARGV[2];
            $sass->name($name)->compile();
        }else{
            $sass->list()->compile();
        }
        exit();
    }

    public function info()
    {
        $sass = $this->start();
        $sass->info();
    }

    private function createSysyemSass()
    {
        $dir = APP . '/system/sass';
        createDir($dir);

        $data = '<?php 
        namespace electronic\sass;
        
        class sass extends \system\core\sass\sass
        {
            public $mini = false;
        
            // Путь к scss файлам \'имя\' => \'путь\'
            public $input = [
                \'style\'     => \'/public/style/style.scss\',
                \'bootstrap\' => \'/public/adm/bootstrap.scss\',
            ];
        
            // Путь к css файлам \'имя\' => \'путь\'
            public $output = [
                \'style\'     => \'/public/style/test.css\',
                \'bootstrap\' => \'/public/adm/bootstrap.css\',
            ];
        
            // Список имён, которые будут компилироваться в втоматическом режиме php e style
            public $list = [
                \'style\', \'bootstrap\',
            ];
        }';
        file_put_contents($dir . '/sass.php', $data);
    }
}



