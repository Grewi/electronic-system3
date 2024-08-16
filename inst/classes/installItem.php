<?php
namespace system\inst\classes;

use system\inst\classes\files;
use system\inst\classes\database;
use system\inst\classes\functions;
use system\core\app\app;

class installItem
{

    public function __construct()
    {
        $app = app::app();
        if (!is_object($app->params->help)) {
            if (file_exists($app->item->path->help)) {
                echo file_get_contents($app->item->path->help) . PHP_EOL;
            } else {
                echo 'Справочная информация не обнаружена.' . PHP_EOL;
            }
            exit();
        }

        if(!is_string($app->item->params->app) || empty($app->item->params->app)){
            echo 'Параметр app не указан, будет применено значение app.' . PHP_EOL;
            $a = null;
            while ($a === null) {
                echo "Продолжить установку компонента " . $app->item->name . "? (yes/no): ";
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                echo 'Установка прервана' . PHP_EOL;
                exit();
            } 
            $app->item->params->app = 'app';
        }

        //Читаем параметры по умолчанию
        if (file_exists($app->item->path->params)) {
            $param = parse_ini_file($app->item->path->params);
        } else {
            $param = [];
        }

        //Читаем параметры из ini файла
        $installIni = functions::parseInstallIni();

        $iniParam = [];
        if(isset($installIni[$app->item->params->app][$app->item->name])){
            $iniParam = $installIni[$app->item->params->app][$app->item->name];
        }


        //Собираем все параметры в app
        foreach($iniParam as $a => $i){
            if(empty($app->item->params->{$a})){
                $app->item->params->{$a} = $i;
            }
        }

        foreach($param as $a => $i){
            if(empty($app->item->params->{$a})){
                $app->item->params->{$a} = $i;
            }
        }



        if (!functions::complectParams()) {
            echo 'К некоторым параметрам будут применены значения по умолчанию' . PHP_EOL;
            $a = null;
            while ($a === null) {
                echo "Продолжить установку компонента " . $app->item->name . "? (yes/no): ";
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                echo 'Установка прервана' . PHP_EOL;
                exit();
            }            
        }

        if(!functions::checkRelation()){
            if(file_exists($app->item->path->relations)){
                $rel = parse_ini_file($app->item->path->relations);
                echo 'Для продолжения требуется установить: ' . $rel['items'] . PHP_EOL;
            }
            $a = null;
            while ($a === null) {
                echo "Продолжить установку компонента " . $app->item->name . "? (yes/no): ";
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                echo 'Установка прервана' . PHP_EOL;
                exit();
            }
        } 

 
        // Если в компоненте есть обработчик index.php создаём его объект       
        $itemIndex = null;
        if(file_exists($app->item->path->index)){
            $class = $app->item->path->class;
            $itemIndex = new $class();
        }    
        
        if($itemIndex){
            $itemIndex->params();
        }

        files::copy();
 
        if($itemIndex){
            $itemIndex->files();
        }

        database::install();

        if($itemIndex){
            $itemIndex->database();
        }

        functions::addNameRelation();

        if($itemIndex){
            $itemIndex->finish();
        }
        echo 'Установка компонента '. $app->item->name . ' завершена'. PHP_EOL;
    }

    private function checkRelation($p)
    {
        $relPath = ITEMS . '/' . $p['itemName'] . '/relations.ini';
        if(file_exists($relPath)){
            $rel = parse_ini_file($relPath);
            echo 'Для продолжения требуется установить: ' . $rel['items'] . PHP_EOL;
        }
        $a = null;
        while ($a === null) {
            echo "Продолжить установку? (yes/no): ";
            $a = functions::yes(trim(fgets(STDIN)));
        }
        if (!$a) {
            echo 'Установка прервана' . PHP_EOL;
            exit();
        }
    }
}