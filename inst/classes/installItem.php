<?php
namespace system\inst\classes;

use system\inst\classes\text;
use system\inst\classes\files;
use system\inst\classes\database;
use system\inst\classes\functions;

use system\inst\classes\install;
use system\inst\classes\item;

class installItem
{

    public $item;
    public $install;

    public function __construct(install $install, item $item)
    {
        $this->item = $item;
        $this->install = $install;
        if($install->getParams('help')){
            if (file_exists($item->pathHelp)) {
                text::print(file_get_contents($item->pathHelp), true);
            } else {
                text::warn('Справочная информация не обнаружена.', true);
            }
        }

        if(empty($item->app)){
            text::warn('Параметр app не указан, будет применено значение "app".');
            $a = null;
            while ($a === null) {
                text::info( "Продолжить установку компонента " . $item->name . "? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                text::danger('Установка прервана', true);
            } 
            $item->app = 'app';
        }

        if($install->getParams('n') === true){
            $item->params['namespace'] = str_replace('/', '\\', $item->app);
        }else{
            $item->params['namespace'] = functions::lastItemPath($item->app);
        }
        

        //Читаем параметры по умолчанию
        if (file_exists($item->pathParams)) {
            $param = parse_ini_file($item->pathParams);
        } else {
            $param = [];
        }

        //Читаем параметры из ini файла
        $installIni = functions::parseInstallIni();

        $iniParam = [];
        if(isset($installIni[$item->app][$item->name])){
            $iniParam = $installIni[$item->app][$item->name];
        }


        //Собираем все параметры в app
        foreach($iniParam as $a => $i){
            if(empty($item->params[$a])){
                $item->params[$a] = $i;
            }
        }

        foreach($param as $a => $i){
            if(empty($item->params[$a])){
                $item->params[$a] = $i;
            }
        }

        if (!functions::complectParams($item)) {
            text::warn('К некоторым параметрам будут применены значения по умолчанию');
            $a = null;
            while ($a === null) {
                text::info("Продолжить установку компонента " . $item->name . "? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                text::danger('Установка прервана', true);
            }            
        }

        if(!functions::checkRelation($item)){
            if(file_exists($item->pathRelations)){
                $rel = parse_ini_file($item->pathRelations);
                text::warn('Для продолжения требуется установить: ' . $rel['items']);
            }
            $a = null;
            while ($a === null) {
                text::info("Продолжить установку компонента " . $item->name . "? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                text::danger('Установка прервана', true);
            }
        } 

 
        // Если в компоненте есть обработчик index.php создаём его объект       
        $itemIndex = null;
        if(file_exists($item->pathIndex)){
            $class = $item->pathClass;
            $itemIndex = new $class();
        }    
        
        if($itemIndex){
            $itemIndex->params();
        }

        files::copy($install, $item);
 
        if($itemIndex){
            $itemIndex->files();
        }

        database::install($item);

        if($itemIndex){
            $itemIndex->database();
        }

        functions::addNameRelation($item);

        if($itemIndex){
            $itemIndex->finish();
        }
        text::success('Установка компонента '. $item->name . ' завершена');
    }

    private function checkRelation($p)
    {
        $relPath = ITEMS . '/' . $p['itemName'] . '/relations.ini';
        if(file_exists($relPath)){
            $rel = parse_ini_file($relPath);
            text::warn('Для продолжения требуется установить: ' . $rel['items']);
        }
        $a = null;
        while ($a === null) {
            text::info("Продолжить установку? (yes/no): ");
            $a = functions::yes(trim(fgets(STDIN)));
        }
        if (!$a) {
            text::danger('Установка прервана', true);
        }
    }
}