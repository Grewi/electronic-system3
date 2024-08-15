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

        if (!empty($app->item->params->help)) {
            if (file_exists($app->item->path->help)) {
                echo file_get_contents($app->item->path->help) . PHP_EOL;
            } else {
                echo 'Справочная информация не обнаружена.' . PHP_EOL;
            }
            exit();
        }

        if(empty($app->item->params->app)){
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
        $appN = !empty($app->item->params->app) ? $app->item->params->app : 'app';

        $iniParam = [];
        if(isset($installIni[$appN][$app->item->name])){
            $iniParam = $installIni[$appN][$app->item->name];
        }

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
         
        $itemIndex = null;
        if(file_exists($app->item->path->index)){
            $class = $app->item->path->class;
            $itemIndex = new $class();
        }    
        
        if($itemIndex){
            $itemIndex->param();
        }

        files::copy();
        database::install();
        functions::addNameRelation();
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