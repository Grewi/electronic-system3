<?php
namespace system\core\symlink;

class symlink
{
    /**
     * Summary of list
     * @var array ['target' => '/...', 'link' => '/...']
     */
    public array $list = [];

    public function list():void
    {
        foreach($this->list as $i){
            $this->create( $i['target'], $i['link']);
        }
    }

    /**
     * Summary of create
     * @param string $target
     * @param string $link
     * @return bool
     */
    public function create(string $target, string $link):bool
    {
        $_linkDir = pathinfo(getcwd() . DIRECTORY_SEPARATOR . $link);
        $linkDir = $_linkDir['dirname'];
        createDir($linkDir);

        if(!file_exists(getcwd() . DIRECTORY_SEPARATOR . $target)){
            echo 'Файл не найден!' . PHP_EOL;
            return false;
        }

        if(file_exists(getcwd() . DIRECTORY_SEPARATOR . $link)){
            echo 'Файл уже существует' . PHP_EOL;
            return false;            
        }

        $a1 = getcwd() . DIRECTORY_SEPARATOR . $target;
        $a2 = getcwd() . DIRECTORY_SEPARATOR . $link;

        $this->addGitIgnore($link);

        return symlink($this->linkSlash($a1), $this->linkSlash($a2));
    }

    private function linkSlash(string $a) :string
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $a);
    }

    private function addGitIgnore(string $link)
    {
        $file = ROOT . '/.gitignore';
        if(!file_exists($file)){
            echo 'Файл .gitignore не найден' .PHP_EOL;
            return;
        }
        $f = file_get_contents($file);
        if(!strpos($f,$link)){
            echo 'Ссылка добавлена в файл .gitignore' . PHP_EOL;
            file_put_contents($file, PHP_EOL . $link, FILE_APPEND);
        }else{
            echo 'В файле .gitignore уже есть запись о ссылке' . PHP_EOL;
        }
        // dd(0);
    }
}