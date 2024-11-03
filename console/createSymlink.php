<?php

namespace system\console;
use system\core\symlink\symlink;

class createSymlink
{
    public function index()
    {
        $target = ARGV[2];
        $link = ARGV[3];

        if(empty($target) || empty($link)){
            echo 'Отсутствуют необходимые значения. 
            Первым значением необходимо указать существующий источник (директория или файл), 
            а вторым место, где будет создана ссылка' . PHP_EOL;
        }

        if( (new symlink())->create($target, $link) ){
            echo 'Выполненно успешно.' . PHP_EOL;
        }else{
            echo 'Ошибка выполнения.' . PHP_EOL;
        }
    }


}
