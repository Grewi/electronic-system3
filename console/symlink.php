<?php
namespace system\console;


class symlink
{
    public $dir = APP . '/system/symlink';

    public function index()
    {
        if(!file_exists($this->dir . '/symlink.php')){
            $this->createSysyemSymlink();
            echo 'Был создан файл ' . $this->dir . '/symlink.php' . '  Необходимо его заполнить' . PHP_EOL;
            exit();
        }
        // $class = '\\electronic\\symlink\\symlink';
        // $symlink = new $class();

        $symlink = new \electronic\symlink\symlink();

        if(count($symlink->list) <= 0){
            echo 'В файле ' . $this->dir . '/symlink.php нет данных для создания ссылок' . PHP_EOL;
        }else{
            $symlink->list();
            echo 'Процесс создания ссылок завершён'. PHP_EOL;
        }
    }

    private function createSysyemSymlink()
    {
        createDir($this->dir);

        $data = '<?php 
        namespace electronic\symlink;
        
        class symlink extends \system\core\symlink\symlink
        {
                /**
                 * Пример заполнения
                 * /
                public array $list = [
                [
                    \'target\' => \'/composer/vendor/twbs/bootstrap\',
                    \'link\' => \'/public/adm/bootstrap\',
                ],
        
        }';
        file_put_contents($this->dir . '/symlink.php', $data);
    }
}