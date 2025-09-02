<?php

namespace system\core\logs;

use system\core\logs\logs;
use system\core\files\files;

class jsonLogs extends logs
{

    public function read()
    {
        $this->createFile();
        return json_decode(file_get_contents($this->path), true);
    }

    public function save($data)
    {
        $this->createFile();
        file_put_contents($this->path, json_encode($data));
    }

    private function createFile()
    {
        if(!file_exists($this->path)){
            file_put_contents($this->path, '{}');
        }
    }

    public function extention(): string
    {
        return 'json';
    }
}