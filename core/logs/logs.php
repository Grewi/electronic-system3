<?php

namespace system\core\logs;

use system\core\files\files;
use system\core\logs\iLogs;

abstract class logs implements iLogs
{
    protected string $path;

    /**
     * @var dir Абсолютный путь к директории
     * @var fileNane Имя файла без расширения
     */
    public function __construct(
        protected string $dir,
        protected string $fileName,
    )
    {
        files::createDir($this->dir);
        $this->path = '/' . files::path($this->dir) . '/' . files::path($this->fileName) . '.' . $this->extention();
    }

    /**
     * Удаляет все файлы из директории старше указанного времени
     */
    public function clean(int $time = 60 * 60 * 24 * 30)
    {
        foreach(scandir($this->dir) as $file){
            if($file == '.' || $file == '..' || !file_exists($this->dir . $file)){
                continue;
            }
            if(filectime($this->dir . $file) < (time() - ($time))){
                unlink($this->dir . $file);
            }
        }  
    }
}

