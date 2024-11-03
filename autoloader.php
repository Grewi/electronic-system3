<?php declare(strict_types=1);
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
        
        if ($this->arrayPath[0] == 'electronic') {
            $this->system();
        } else {
            $this->includeFile(ROOT . '/' . $this->path . '.php');
        }
    }

    private function system(): void
    {

        $path = $this->arrayPath;
        unset($path[0]);
        $this->p = '/' . implode('/', $path);

        if (file_exists(SYSTEM . $this->p . '.php')) {

            if (!file_exists($this->appSystem . '/' . $this->p . '.php')) {
                $this->createFile();
            }
            $this->includeFile($this->appSystem . '/' . $this->p . '.php');
        }

        if (!file_exists(ROOT . $this->p . '.php') && file_exists($this->appSystem . '/' . $this->p . '.php')) {
            $this->includeFile($this->appSystem . '/' . $this->p . '.php');
        }
    }

    private function createFile(): void
    {
        $path = $this->arrayPath;
        $className = $path[count($path) - 1];
        unset($path[0]);
        unset($path[count($path)]);
        createDir($this->appSystem . $this->p);

        $class = '<?php
namespace electronic\\' . implode('\\', $path) . ';
class ' . $className . ' extends \\' . SYSTEM_NAME . $this->namespace($this->p) . ' {}';

        file_put_contents($this->appSystem . $this->p . '.php', $class);
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
}