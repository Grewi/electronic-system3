<?php

namespace system\core\logs;

use system\core\logs\logs;

class textLogs extends logs
{
    private string $text = '';
    
    public function fatal(string $text)
    {
        $this->text('FATAL', $text);
    }

    public function error(string $text)
    {
        $this->text('ERROR', $text);
    }

    public function warn(string $text)
    {
        $this->text('WARN', $text);
    }

    public function info(string $text)
    {
        $this->text('INFO', $text);
    } 
    
    public function trace(string $text)
    {
        $this->text('TRACE', $text);
    } 
    
    public function debug(string $text)
    {
        $this->text('DEBUG', $text);
    }

    private function text($type, $text)
    {
        $this->text = date('Y-m-d H:i:s') . ' ' . $type . ' ' . $text;
    }

    public function add(string $text): void
    {
        $this->text = $this->text . ' ' . $text;
    }    

    public function save()
    {
        file_put_contents($this->path, $this->text . PHP_EOL, FILE_APPEND);
    }

    public function extention(): string
    {
        return 'log';
    }
}