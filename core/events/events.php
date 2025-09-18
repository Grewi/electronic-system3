<?php

namespace system\core\events;

use system\core\logs\textLogs;

abstract class events
{
    /**
     * Список директорий внутри директории $this->dir по которым 
     * будет осуществлятся поиск классов 
     */
    protected array  $list = [];
    protected string $fileName;
    protected bool   $logs = false;
    protected string $dir = 'events';

    /**
     * @var fileName  имя файла/класса созданного в категории $this->dir и указанного в списке $this->list
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Поиск файла в разделах и метода в них
     */
    protected function eventSearchClassAndMethod($name, $arguments)
    {
        foreach ($this->list as $el) {
            $p = [$this->dir, $el, $this->fileName];
            $c = implode('\\', $p);
            $filePath =  implode('/', $p);
            if (file_exists(ROOT . '/' . $filePath . '.php')) {
                $class = new $c;
                if (!method_exists($class, $name)) {
                    $this->eventSearchLogErrors($c, $name);
                    continue;
                }
                $class->{$name}(...$arguments);
            } else {
                $this->eventSearchLogErrors($c, $name);
            }
        }
    }

    protected function eventSearchLogErrors($class, $method = null)
    {
        if ($this->logs) {
            $log = new textLogs(APP . '/cache/events', date('Y-m-d'));
            if ($method) {
                $log->error('class ' . $class . ' error method' . $method);
            } else {
                $log->error('error class ' . $class . ' method ' . $method);
            }
            $log->save();
        }
    }

    public function __call($name, $arguments)
    {
        $this->eventSearchClassAndMethod($name, $arguments);
    }
}
