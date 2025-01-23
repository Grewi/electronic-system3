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
                functions::print(file_get_contents($app->item->path->help), true);
            } else {
                functions::print('Справочная информация не обнаружена.', true);
            }
        }

        if(!is_string($app->item->params->app) || empty($app->item->params->app)){
            functions::print('Параметр app не указан, будет применено значение "apps/app".');
            $a = null;
            while ($a === null) {
                functions::print("Продолжить установку компонента " . $app->item->name . "? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                functions::print('Установка прервана', true);
            } 
            $app->item->params->app = 'apps/app';
        }
        if($app->params->n === true){
            $app->item->params->namespace = str_replace('/', '\\', $app->item->params->app);
        }else{
            $app->item->params->namespace = functions::lastItemPath($app->item->params->app);
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
            functions::print('К некоторым параметрам будут применены значения по умолчанию');
            $a = null;
            while ($a === null) {
                functions::print("Продолжить установку компонента " . $app->item->name . "? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                functions::print('Установка прервана', true);
            }            
        }

        if(!functions::checkRelation()){
            if(file_exists($app->item->path->relations)){
                $rel = parse_ini_file($app->item->path->relations);
                functions::print('Для продолжения требуется установить: ' . $rel['items']);
            }
            $a = null;
            while ($a === null) {
                functions::print("Продолжить установку компонента " . $app->item->name . "? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                functions::print('Установка прервана', true);
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
        functions::print('Установка компонента '. $app->item->name . ' завершена');
    }

    private function checkRelation($p)
    {
        $relPath = ITEMS . '/' . $p['itemName'] . '/relations.ini';
        if(file_exists($relPath)){
            $rel = parse_ini_file($relPath);
            functions::print('Для продолжения требуется установить: ' . $rel['items']);
        }
        $a = null;
        while ($a === null) {
            functions::print("Продолжить установку? (yes/no): ");
            $a = functions::yes(trim(fgets(STDIN)));
        }
        if (!$a) {
            functions::print('Установка прервана', true);
        }
    }
}