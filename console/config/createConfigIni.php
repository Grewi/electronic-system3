<?php
namespace system\console\config;
use system\core\config\config;
use system\core\text\text;

class createConfigIni
{
    public function index() : void
    {
        $path = APP . '/configs';
        if(file_exists($path)){
            $allFiles = scandir($path);
            foreach($allFiles as $a => $i){
                if($i == '.' || $i == '..'){
                    continue;
                }
                $info = pathinfo($i);
                if($info['extension'] != 'php'){
                    continue;
                }
                $filename = $info['filename'];
                (new ('\\' . APP_NAME . '\\configs\\' . $filename))->update();
            }
            text::primary('Операция завершена', true);
        }
    }
}