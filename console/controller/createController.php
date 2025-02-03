<?php

namespace system\console\controller;
use system\core\text\text;

class createController
{
    private $parametr;
    private $ArrParam;
    private $className;
    private $path;
    private $path_v;
    private $pathDir;
    private $pathDir_v;
    private $route;
    private $path_crud_v = [];
    private $namespace;



    public function index()
    {
        $ARGV = ARGV;
        if (is_array($ARGV)) {
            if (!isset(ARGV[2])) {
                text::danger('Не указан обязательный параметр');
                text::warn('Необходимо указать путь до контроллера в директории controllers.');
                text::warn('Например: "index/index" создаст "app/controllers/index/indexController.php"', true);
            }
            $parametr = $ARGV[2];
        } else {
            text::danger('Не удалось получить необходимые параметры', true);
        }

        $this->parametr = $parametr;

        $this->ArrParam = $ArrParam = explode('/', $parametr);
        $this->className = array_pop($ArrParam) . 'Controller';
        $this->path = APP . '/controllers/' . $parametr . 'Controller.php';
        $this->pathDir = APP . '/controllers/' . implode('/', $ArrParam);
        $this->route = APP . '/route/web/00_' . str_replace('/', '_', $parametr) . '.php';
        $_ArrParam = array_merge([APP_NAMESPACE, 'controllers'], $ArrParam);
        $this->namespace = implode('\\', $_ArrParam);
        $v = null;
        if (!isset($ARGV[3])) {
            $this->saveController();
            text::primary('Операция завершена', true);
        } else {
            $v = $ARGV[3];
        }

        if ($v == 'v') {
            $this->path_v = APP . '/views/' . $parametr . '.php';
            $this->pathDir_v = APP . '/views/' . implode('/', $ArrParam);
            $this->saveControllerV();
            text::primary('Операция завершена', true);
        }

        if ($v == 'crud') {
            $this->pathDir_v = APP . '/views/' . $parametr;
            $this->crud($parametr);
            text::primary('Операция завершена', true);
        }

        if ($v == 'full') {
            $this->full($parametr);
            text::primary('Операция завершена', true);
        }
    }

    private function saveController()
    {
        if (!file_exists($this->path)) {
            if (!file_exists($this->pathDir)) {
                mkdir($this->pathDir, 0755, true);
            }
            $data = [
                'namespace' => $this->namespace,
                'APP_NAMESPACE' => APP_NAMESPACE,
                'className' => $this->className,
                'parametr' => $this->parametr,
            ];
            $this->view(__DIR__ . '/view1', $data, $this->path);
            text::success('Контроллер "' . $this->path . '" создан');
        } else {
            text::warn('Файл контроллера уже существует.');
        }
    }

    private function saveControllerV()
    {
        if (!file_exists($this->path)) {
            if (!file_exists($this->pathDir)) {
                mkdir($this->pathDir, 0755, true);
            }
            $data = [
                'namespace' => $this->namespace,
                'APP_NAMESPACE' => APP_NAMESPACE,
                'className' => $this->className,
                'parametr' => $this->parametr,
            ];
            $this->view(__DIR__ . '/view1', $data, $this->path);
            text::success('Контроллер "' . $this->path . '" создан');
        } else {
            text::warn('Файл контроллера уже существует.');
        }
        if (!file_exists($this->path_v)) {
            if (!file_exists($this->pathDir_v)) {
                mkdir($this->pathDir_v, 0755, true);
            }
            $this->view(__DIR__ . '/empty', [], $this->path_v);
            text::success('Шаблон "' . $this->path_v . '" создан');
        } else {
            text::warn('Файл шаблона уже существует.');
        }
    }

    private function crud($parametr)
    {
        // Генерируем шаблоны
        $arr = ['index', 'create', 'update', 'delete'];
        foreach ($arr as $i) {
            $f = APP . '/views/' . $parametr . '/' . $i . '.php';
            if (!file_exists($f)) {
                if (!file_exists($this->pathDir_v)) {
                    mkdir($this->pathDir_v, 0755, true);
                }
                $this->view(__DIR__ . '/empty', [], $f);
                text::success('Шаблон "' . $i . '" создан');
            } else {
                text::warn('Файл шаблона "' . $i . '" уже существует.');
            }
        }

        //создаём контроллер
        if (!file_exists($this->path)) {

            if (!file_exists($this->pathDir)) {
                mkdir($this->pathDir, 0755, true);
            }
            $data = [
                'namespace' => $this->namespace,
                'APP_NAMESPACE' => APP_NAMESPACE,
                'className' => $this->className,
                'parametr' => $this->parametr,
            ];
            $this->view(__DIR__ . '/view2', $data, $this->path);
            text::success('Контроллер "' . $this->path . '" создан');
        } else {
            text::warn('Файл контроллера уже существует.');
        }
    }

    private function full($parametr)
    {
        $this->pathDir_v = APP . '/views/' . $parametr;
        $arr = ['index', 'create', 'update', 'delete'];
        // Генерируем шаблоны
        foreach ($arr as $i) {
            $f = APP . '/views/' . $parametr . '/' . $i . '.php';
            if (!file_exists($f)) {
                if (!file_exists($this->pathDir_v)) {
                    mkdir($this->pathDir_v, 0755, true);
                }
                $this->view(__DIR__ . '/view3', [], $f);
                text::success('Шаблон "' . $f . '" создан');
            }else{
                text::warn('Файл шаблона "' . $i . '" уже существует.');
            }
        }

        //создаём контроллер
        if (!file_exists($this->path)) {

            if (!file_exists($this->pathDir)) {
                mkdir($this->pathDir, 0755, true);
            }
            $data = [
                'namespace' => $this->namespace,
                'APP_NAMESPACE' => APP_NAMESPACE,
                'className' => $this->className,
                'parametr' => $this->parametr,
            ];
            $this->view(__DIR__ . '/view4', $data, $this->path);
            text::success('Контроллер "' . $this->path . '" создан');
        } else {
            text::warn('Файл контроллера уже существует.');
        }

        if (!file_exists($this->route)) {
            $arrParam = $this->ArrParam;
            $lastEl = array_pop($arrParam);
            $chiftEl = array_shift($arrParam);
            $r = count($arrParam) > 0 ? '/' . implode('/', $arrParam) : '';
            //Создаём роут
            $data = [
                'namespace' => $this->namespace,
                'APP_NAMESPACE' => APP_NAMESPACE,
                'r' => $r,
                'chiftEl' => $chiftEl,
                'lastEl' => $lastEl,
            ];
            $this->view(__DIR__ . '/view5', $data, $this->route);
            text::success('Роутер "' . $this->route . '" создан');
        }else{
            text::warn('Файл роутера уже существует.');
        }



    }

    /**
     * Summary of view
     * @param string $view Путь к шаблону 
     * @param array $data Массив с данными
     * @param string $file Путь к новому файлу
     * @return void
     */
    private function view(string $view, array $data, string $file)
    {
        $layout = file_get_contents($view);
        foreach ($data as $a => $i) {
            $layout = str_replace('{{' . $a . '}}', $i, $layout);
        }
        file_put_contents($file, $layout);
    }
}
