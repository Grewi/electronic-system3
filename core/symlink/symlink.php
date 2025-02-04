<?php
namespace system\core\symlink;
use system\core\text\text;

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
            if($this->create( $i['target'], $i['link'])){
                text::success('ссылка "' . $i['link'] . '" создана');
            }else{
                text::danger('Ссылку "' . $i['link'] . '" создать не удалось');
            }
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
            text::danger('Файл "' . $target . '" не найден!');
            return false;
        }

        if(file_exists(getcwd() . DIRECTORY_SEPARATOR . $link)){
            text::danger('Файл "' . $link . '" уже существует');
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
            text::danger('Файл .gitignore не найден');
            return;
        }
        $f = file_get_contents($file);
        if(!strpos($f,$link)){
            text::success('Ссылка добавлена в файл .gitignore');
            file_put_contents($file, PHP_EOL . $link, FILE_APPEND);
        }else{
            text::warn('В файле .gitignore уже есть запись о ссылке');
        }
    }
}