<?php

declare(strict_types=1);
!INDEX ? exit('exit') : true;

spl_autoload_register(function (string $className) {
    new autoloader($className);
});

class autoloader
{
    private $namespace;
    private $path;
    private $arrayPath = [];
    private $appSystem = APP . '/' . SYSTEM_NAME;
    private $p;

    public function __construct(string $a)
    {
        $this->namespace = $this->namespace($a);
        $this->path = $this->path($a);
        $this->arrayPath = explode('/', $this->path);

        if (file_exists(ROOT . '/' . $this->path . '.php')) {
            $this->includeFile(ROOT . '/' . $this->path . '.php');
        }else{
            // var_dump($this->lastItemPath($this->path));
            $this->includeFile(ROOT . '/' . $this->lastItemPath($this->path) . '.php');
        }

    }

    private function includeFile($path)
    {
        try {
            if (file_exists($path)) {
                require $path;
            } else {
                throw new \FileException('Файл ' . $path . ' не найден!');
            }
        } catch (\FileException $e) {
            var_dump($e);
            exit($e->message);
        }
    }

    private function namespace(string $i): string
    {
        return str_replace('/', '\\', $i);
    }

    private function path(string $i): string
    {
        return str_replace('\\', '/', $i);
    }

    public function lastItemPath(string $namespace): string
    {
        $a = explode('/', APP_NAME);
        $b = array_pop($a);
        if(str_starts_with($namespace, $b)){
            return str_replace($b, APP_NAME, $namespace);
        }else{
            return $namespace;
        }
    }

}
