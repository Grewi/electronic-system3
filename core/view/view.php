<?php

namespace system\core\view;
use system\core\database\database;
use system\core\app\app;
use system\core\lang\lang;

class view
{
    protected $file;
    protected $forseCompile = false;
    protected $cacheDir;
    protected $viewsDir;
    protected $validElement;
    protected $countInclude;

    public function __construct(?string $file=null, array $data = [])
    {
        //Создать запрос в настройки
        $this->cacheDir = APP . '/cache/views';
        $this->viewsDir = APP . '/views';
        $this->validElement = "/^[a-zA-Z0-9а-яА-ЯёЁ\-_]+$/u"; //Допустимые символы в переменных
        $this->countInclude = [];
        if(!file_exists($this->viewsDir . '/' . $file . '.php')){
            throw new \TempException('Файл шаблона "/apps/' . APP_NAME . '/views/' . $file . '.php" отсутствует!');
        }
        if($file){
            $this->out($file, $data);
        }
    }

    public function cacheDir(string $path)
    {
        $this->cacheDir = APP . '/' . $path;
        return $this;
    }

    public function viewsDir(string $path)
    {
        $this->viewsDir = APP . '/' . $path;
        return $this;
    }

    public function out(string $file, $data = null) : void
    {
        $this->render($file);
        try{
            $db = database::connect();
        }catch(\Exception $e){}
        
        $app = app::app();
        $lang = new lang();
        extract($data);
        // $file = $this->countInclude[0];
        if(file_exists($this->cacheDir . '/' . $this->countInclude[0] . '.php')){
            $app->views->add($this->countInclude[0]);
            time_system('view: '.$this->countInclude[0]);
            require $this->cacheDir . '/' . $file . '.php';
        }else{
            throw new \TempException('Отсутствует файл вывода для шаблона "' . $file . '"!');
        }
        
    }

    public function return(string $file, $data = null) : string
    {
        $this->render($file);
        extract($data);
        $file = $this->countInclude[0];
        if(file_exists($this->cacheDir . '/' . $file . '.php')){
            ob_start();
            require $this->cacheDir . '/' . $file . '.php';
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }else{
            throw new \TempException('Отсутствует файл вывода для шаблона "' . $file . '"!');
        }
    }

    private function render(string $file)
    {
        $this->file = $file;
        $this->countInclude[] = $file;
        $fullPathOriginal = $this->viewsDir . '/' .  $file . '.php';
        $fullPathCache = $this->cacheDir . '/' .  $file . '.php';

        //Время последнего изменения в шаблонах и файла в кеше
        $timeCacheFile = file_exists($fullPathCache) ? filemtime($fullPathCache) : 0;
        $timeOriginal = $this->foldermtime($this->viewsDir);

        //Файл изменился или включена принудительная перекомпиляция
        if ($timeOriginal > $timeCacheFile || $this->forseCompile) {
            $content = $this->getFile($fullPathOriginal);
            $content = $this->useLauoyt($content); // Сборка по шаблону
            $content = $this->variable($content);
            $content = $this->include($content);   // Подключение файлов
            $content = $this->csrf($content);      // Токен csrf
            // $content = $this->history($content);   // History js
            $content = $this->clearing($content);  // Очистка
            $this->save($file, $content);          // Сохранение файла в кеш
        }
        return $this;
    }

    //Подключаем внешние файлы
    private function include($content): string
    {
        preg_match_all('/\<include \s*file\s*=\s*"(.*?)"\s*\/*\>/si', $content, $matches);
        if ($matches && count($matches[1]) > 0) {
            foreach ($matches[1] as $key => $i) {
                if(!file_exists($this->viewsDir . '/' . $i . '.php')){
                    throw new \TempException('Файл шаблона "/apps/' . APP_NAME . '/views/' . $i . '.php" отсутствует! Подключен в шаблоне "/apps/' . APP_NAME . '/views/' . $this->file. '.php"');
                }
                $inc = '<?php include \'' . $this->cacheDir . '/' . $i . '.php\' ?>';
                $content = str_replace($matches[0][$key], $inc, $content);
                $this->render($i);
            }
        }

        preg_match_all('/\<include \s*class\s*=\s*"(.*?)"\s* \s*method\s*=\s*"(.*?)" \/*\>/si', $content, $matches);
        if ($matches && count($matches[1]) > 0) {
            foreach ($matches[1] as $key => $i) {
                $i = $this->pathR($i);
                $inc = '<?php (new ' . $i . '())->' . $matches[2][$key] . '() ?>';
                $content = str_replace($matches[0][$key], $inc, $content);
            }
        }
        return $content;
    }

    private function getFile(string $path): string
    {
        if (file_exists($path)) {
            $temp = file_get_contents($path);
        } else {
            throw new \TempException('Файл "' . $path . '" не найден!');
        }
        return $temp;
    }

    private function save($file, $content): void
    {
        $a = explode('/', $file);
        array_pop($a);
        $a = implode('/', $a);

        if (!file_exists($this->cacheDir . '/' . $file . '.php')) {
            if (!file_exists($this->cacheDir . '/' . $a)) {
                mkdir($this->cacheDir . '/' . $a, 0755, true);
            }
        }
        file_put_contents($this->cacheDir . '/' . $file . '.php', $content);
    }

    private function clearing($content): string
    {
        return preg_replace('/\<\!--(.*?)-->/si', '', $content);
    }

    //Если есть тег use используем шаблон
    private function useLauoyt($content): string
    {
        preg_match('/\<use\s*(.*?)\s*\>/si', $content, $matches);
        if ($matches) {
            $a = $this->parserHtmlTag($matches[1]);
            $aa = array_shift($a);
            $html = 'layout/' . $aa;
            if ($matches) {
                $app = app::app();
                $app->view->layout = $html;
                time_system('layout:'.$html);
                $layout = $this->getFile($this->viewsDir . '/' . $html . '.php');
                preg_match_all('/\<block\s*name=\"(.*?)\"\s*\/*>/si', $layout, $matches2);
                foreach ($matches2[1] as $a => $i) {
                    preg_match('/\<block\s*name=\"' . $i . '\"\s*\>(.*?)\<\/block\s*>/si', $content, $m);
                    $r = $m ? $m[1] : '';
                    $layout = str_replace($matches2[0][$a], $r, $layout);
                }
                return $layout;
            }
        }
        return $content;
    }

    private function variable($content): string
    {
        preg_match_all('/\{\{\s*\$(.*?)\s*\}\}(else\{\{(.*?)}\})?/si', $content, $matches);
        foreach ($matches[1] as $a => $i) {
            $r = $matches[3][$a] !== '' ? (string)$matches[3][$a] : '""'; //Значение по умолчанию
            $content = str_replace($matches[0][$a], '<?= isset($' . $i . ') && (is_string($' . $i . ') || is_numeric($' . $i . ') ) ? $' . $i . ' : ' . $r . '; ?>', $content);
        }
        return $content;
    }

    //Принимает два параметра type= input/token и name
    private function csrf($content): string
    {
        preg_match_all('/\<csrf\s*(.*?)\s*\\/*>/si', $content, $matches);
        foreach ($matches[0] as $key => $i) {
            $a = $this->parserHtmlTag($matches[1][$key]);
            if (isset($a['type']) && $a['type'] == 'input' && isset($a['name']) && !empty($a['name'])) {
                $content = str_replace($matches[0][$key], '<input value="<?= csrf(\'' . $a['name'] . '\') ?>" name="csrf" hidden >', $content);
            } elseif (isset($a['type']) && $a['type'] == 'token' && isset($a['name']) && !empty($a['name'])) {
                $content = str_replace($matches[0][$key], '<?= csrf(\'' . $a['name'] . '\') ?>', $content);
            } else {
                $content = str_replace($matches[0][$key], '', $content);
            }
        }
        return $content;
    }

    // private function history($content)
    // {
    //     preg_match('/\<history\s*(.*?)\s*\>/si', $content, $m);
    //     $js = '<script>' . PHP_EOL . history::js() . PHP_EOL . '</script>';
    //     if($m){
    //         $content = str_replace($m[0], $js, $content);
    //     }
    //     return $content;
    // }

    //Последнее изменение в директории
    private function foldermtime(string $dir)
    {
        $foldermtime = 0;
        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_FILEINFO;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, $flags));
        while ($it->valid()) {
            if (($filemtime = $it->current()->getMTime()) > $foldermtime) {
                $foldermtime = $filemtime;
            }
            $it->next();
        }
        return $foldermtime ?: null;
    }

    private function parserHtmlTag(string $parametrs, bool $valid = true)
    {
        preg_match_all('/\s*(.*?)=\"(.*?)\"\s*/siu', $parametrs, $m);
        $result = [];
        foreach ($m[0] as $key => $i) {
            if ((!preg_match($this->validElement, $m[1][$key], $resut) || !preg_match($this->validElement, $m[2][$key], $resut)) && $valid) {
                throw new \TempException('Недопустимое имя переменной в цикле "' . $parametrs . '" в шаблоне "' . $this->file . '"!');
                // continue;
            }
            $result[mb_strtolower($m[1][$key])] = $m[2][$key];
        }
        return $result;
    }

	private function pathR(string $a)
	{
        $i = str_replace('/', '\\', $a); 
        if($i[0] != '\\'){
            $i = '\\' . $i;
        }
		return $i;
	}
}
