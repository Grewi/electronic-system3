<?php

namespace system\core\logs;

use system\core\logs\logs;

class textLogs extends logs
{
    private string $text = '';
    
    public function fatal(string $text):static
    {
        $this->text('FATAL', $text);
        return $this;
    }

    public function error(string $text)
    {
        $this->text('ERROR', $text);
    }

    public function warn(string $text):static
    {
        $this->text('WARN', $text);
        return $this;
    }

    public function info(string $text)
    {
        $this->text('INFO', $text);
    } 
    
    public function trace(string $text):static
    {
        $this->text('TRACE', $text);
        return $this;
    } 
    
    public function debug(string $text):static
    {
        $this->text('DEBUG', $text);
        return $this;
    }

    private function text($type, $text):static
    {
        $this->text = date('Y-m-d H:i:s') . ' ' . $type . ' ' . $text;
        return $this;
    }

    public function add(string $text): static
    {
        $this->text = $this->text . ' ' . $text;
        return $this;
    }    

    public function save()
    {
        file_put_contents($this->path, $this->text . PHP_EOL, FILE_APPEND);
    }

    public function saveAndClean()
    {
        $this->clean();
        $this->save();
    }

    public function extention(): string
    {
        return 'log';
    }
}