<?php 
namespace system\core\route;

use system\core\app\app;

class route 
{
    protected $url = [];
    protected $namespace;
    protected $groupName;
    protected $get = true;
    protected $start = false;
    protected $groupControl = false;
    protected $autoExitGroup = false;
    protected $autoExitController = true;
    protected $param_regex = '/[^a-zA-Zа-яА-Я0-9-_]/ui';

    public function __construct()
    {
        $app = app::app();
        if (ENTRANSE == 'web') {
            //Парсинг URL
            $urls = explode('?', $app->bootstrap->uri);
            $url = explode('/', $urls[0]);
            unset($url[0]);
            $this->url = $url;
            $app->request->params = $url;
        } elseif (ENTRANSE == 'console') {
            $argv = ARGV;
            unset($argv[0]);
            $this->url = $argv;
            $app->request->params = $argv;
        }
    }

    public function autoExitGroup($status)
    {
        $this->autoExitGroup = $status;
    }

    public function autoExitController($status)
    {
        $this->autoExitController = $status;
    }

    public function autoloadWeb()
    {
        $existInclude = get_included_files();
        $appFunctionDir = APP . '/route/web';
        $generatorPage = null;
        $route = $this;
        if (file_exists($appFunctionDir)) {
            $systemFnctionFiles = scandir($appFunctionDir);
            asort($systemFnctionFiles);
            if (is_iterable($systemFnctionFiles)) {
                foreach ($systemFnctionFiles as $file) {
                    if (!file_exists($appFunctionDir . '/' . $file)) {
                        continue;
                    }
                    if (in_array($appFunctionDir . '/' . $file, $existInclude)) {
                        continue;
                    }
                    if ($file == 'generatorPage.php') {
                        $generatorPage = $appFunctionDir . '/' . $file;
                        continue;
                    }

                    $f = pathinfo($file);
                    if ($f['extension'] == 'php') {
                        require $appFunctionDir . '/' . $file;
                    }
                }
            }
            if ($generatorPage) {
                require $generatorPage;
            }
        }
    }

    public function get(string $get): route
    {
        $this->startControl();
        $get = $this->slash($get);
        if (is_null($get)) {
            return $this;
        }
        $this->parseUrl($get);
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->get = false;
        }
        return $this;
    }

    public function post(string $get): route
    {
        $this->startControl();
        $get = $this->slash($get);
        $this->parseUrl($get);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->get = false;
        }
        return $this;
    }

    public function put(string $get): route
    {
        $this->startControl();
        $get = $this->slash($get);
        $this->parseUrl($get);
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->get = false;
        }
        return $this;
    }

    public function delete(string $get): route
    {
        $this->startControl();
        $get = $this->slash($get);
        $this->parseUrl($get);
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->get = false;
        }
        return $this;
    }

    public function all(string $get): route
    {
        $this->startControl();
        $get = $this->slash($get);
        $this->parseUrl($get);
        return $this;
    }

    public function group(string $name, callable $function, string|null $prefix = null): void
    {
        $app = app::app();
        $app->route->group = $name;
        $this->groupControl = true;
        $name = $this->slash($name);
        $this->startControl();
        if ($name[0] == '/') {
            $name = substr($name, 1);
        }

        $this->groupName = $name;
        $status = true;
        $nameArr = explode('/', $name);

        foreach ($nameArr as $a => $i) {
            if (@$this->url[$a + 1] != $i) {
                $status = false;
            }
        }

        if($prefix){
            $this->prefix($prefix);
        }

        if ($status && $this->get) {
            $function($this);
            if ($this->autoExitGroup) {
                exit();
            }
        } 

        $this->get = true;
        $this->start = false;
        $this->groupControl = false;
        $this->groupName = null;
    }

    public function blockGroup(string $name, callable $function, string|null $prefix = null):route
    {
        $this->get = true;
        $this->namespace = '';
        $this->groupName = null;
        $this->group($name, $function, $prefix);
        return $this;
    }    

    /**
     * interim
     */
    public function prefix($class, $method = 'index'): route
    {
        $this->startControl();
        if ($this->get) {
            $reflection = new \ReflectionClass($class);

            $params = $reflection->getMethod($method)->getParameters();
            $cla = [];
            foreach ($params AS $param) {
                $cl = $param->getType()->getName();
                $nc = new $cl();
                if(method_exists($nc, 'toController')){
                    $cla[] = $nc->toController();
                }else{
                    $cla[] = $nc;
                }
            }            
            $get = (new $class)->{$method}(...$cla);
            if (!is_null($get)) {
                $this->get = $get;
            }
        }
        return $this;
    }

    public function console(string $get): route
    {
        $this->startControl();
        if (isset($this->url[1]) && $get == $this->url[1]) {
            $this->get = true;
        } else {
            $this->get = false;
        }
        return $this;
    }

    public function controller($class, $method): void
    {
        if ($this->get) {
            $app = app::app();
            if($app->route->group != $this->groupName){
                $app->route->group = null;
            }
            $controller = $this->namespace . $class;
            $reflection = new \ReflectionClass($controller);
            $params = $reflection->getMethod($method)->getParameters();
            $cla = [];
            foreach ($params AS $param) {
                $cl = $param->getType()->getName();
                if($cl == 'system\core\app\app'){
                    $nc = $app;
                }else{
                    $nc = new $cl();
                }
                
                if(method_exists($nc, 'toController')){
                    $cla[] = $nc->toController();
                }else{
                    $cla[] = $nc;
                }
            }
            $app->controller->class = $class;
            $app->controller->method = $method;
            time_system($class.':'.$method);
            (new $controller)->$method(... $cla);
            time_system('finish controller');
            if ($this->autoExitController) {
                exit();
            }
        }
        if($this->groupControl){

        }
        $this->get = false;
        $this->start = false;
    }

    public function closure(callable $function)
    {
        if ($this->get) {
            $app = app::app();
            $refFunction = new \ReflectionFunction($function);
            $params = $refFunction->getParameters();
            $cla = [];
            foreach ($params AS $param) {
                $cl = $param->getType()->getName();
                if($cl == 'system\core\app\app'){
                    $nc = $app;
                }else{
                    $nc = new $cl();
                }
                
                if(method_exists($nc, 'toController')){
                    $cla[] = $nc->toController();
                }else{
                    $cla[] = $nc;
                }
            } 
            
            $function(... $cla);
            exit();
        }
        $this->get = false;
        $this->start = false;
    }

    private function parseUrl(string $get): void
    {
        $app = app::app();
        $app->route->mask = $get;
        $app->getparams->clean();
        if($get == '/*'){
            return;
        }
        if ($this->groupName) {
            if ($get == '/') {
                $get = substr($get, 1);
            }
            $get = '/' . $this->groupName . $get;
        }

        //Парсинг $get
        $g = explode('/', $get);
        unset($g[0]);

        $url = (array) $this->url;
        $check = true;

        //Если длина url меньше роута без необязательных параметров
        $gg = $this->delParametr($g);
        if (count($url) < count($gg)) {
            $check = false;
        }

        foreach ($url as $a => $i) {
            //Если на последней итерации пусто. пропускаем
            if (empty($i) && count($url) == $a) {
                if ($i != @$g[$a]) {
                    $check = false;
                }
                continue;
            }

            if (isset($g[$a])) {
                preg_match('/\{(.*?)\}/si', $g[$a], $param);
                preg_match('/\{(.*?)\?\}/si', $g[$a], $freeParam);

                //Если сработал необязательный параметр, удаляем обязательный
                if (isset($freeParam[1])) {
                    unset($param);
                }

                //Проверка не обязательного параметра
                if (isset($freeParam[1])) {
                    $getReturn = preg_replace($this->param_regex, '', urldecode($url[$a]));
                    $app->getparams->{$freeParam[1]} = $getReturn;
                    continue;
                }

                //Проверка обязательного параметра
                if (!empty($param) && empty($url[$a])) {
                    $check = false;
                    break;
                } elseif ((isset($param[1]) && !empty($url[$a])) && $check) {
                    $getReturn = preg_replace($this->param_regex, '', urldecode($url[$a]));
                    $app->getparams->{$param[1]} = $getReturn;
                }

                //Проверка элемента url
                if ($url[$a] != $g[$a] && !isset($param[1]) && !isset($freeParam[1])) {
                    $check = false;
                    break;
                }
            } else {

                $check = false;
            }
        }
        $this->get = $check;
    }

    private function delParametr(array $param)
    {
        preg_match('/\{(.*?)\?\}/si', $param[count($param)], $freeParam);
        if (isset($freeParam[0])) {
            unset($param[count($param)]);
            return $this->delParametr($param);
        } else {
            return $param;
        }
    }

    private function slash(string $str): string
    {
        return '/' . trim(str_replace('\\', '/', $str), '/');            
    }

    private function startControl() : void
    {
        if(!$this->start){
            $this->get = true;
            $this->start = true;
        }
    }
}