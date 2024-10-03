<?php declare(strict_types=1);
!INDEX ? exit('exit') : true;

spl_autoload_register(function(string $className){
    new autoloader($className);
});

class autoloader
{
    private $namespace  = '';
    private $classArray = [];
    private $pathSystem = '';
    private $pathApp = '';

    public function __construct(string $namespace)
    {
        $this->namespace = str_replace('\\', '/', $namespace);
        $this->classArray = explode('/', $this->namespace);
        
        if($this->classArray[0] == 'electronic'){
            $this->system();
        }else{
            $this->includeFile(ROOT . '/' . $this->namespace . '.php');
        }        
    }

    private function system() : void
    {
        $path = $this->classArray;
        unset($path[0]);
        $this->pathApp =  APP . '/system/' . implode('/', $path);
        $this->pathSystem = '/system/' . implode('/', $path);

        if(file_exists(ROOT . $this->pathSystem . '.php')){
            if(!file_exists($this->pathApp . '.php')){
                $this->createFile($this->pathApp);
            }
            $this->includeFile($this->pathApp . '.php');
        }

        if(!file_exists(ROOT . $this->pathSystem . '.php') && file_exists($this->pathApp . '.php')){
            $this->includeFile($this->pathApp . '.php');
        }
    }

    private function createFile() : void
    {
        $path = $this->classArray;
        $className = $path[count($path) - 1];
        unset($path[0]);
        unset($path[count($path)]);
        $p = APP . '/system/' . implode('/', $path);
        createDir($p);

        $namespace = $this->classArray;
        unset($namespace[0]);
        unset($namespace[count($namespace)]);
        $n = 'electronic/' . implode('/', $namespace);

        $class = '<?php
namespace ' . str_replace('/', '\\', $n) . ';
class ' . $className .' extends ' . str_replace('/', '\\', $this->pathSystem) . '
{

}
';
        file_put_contents($this->pathApp . '.php', $class);
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
}