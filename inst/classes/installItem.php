<?php
namespace system\inst\classes;

use system\inst\classes\files;
use system\inst\classes\database;
use system\inst\classes\functions;

class installItem
{

    public function __construct($p)
    {
        $iDir = ITEMS . '/' . $p['itemName'];
        if (isset($p['help'])) {
            $iDirHelp = $iDir . '/help.txt';
            if (file_exists($iDirHelp)) {
                echo file_get_contents($iDirHelp) . PHP_EOL;
            } else {
                echo 'Справочная информация не обнаружена.' . PHP_EOL;
            }
            exit();
        }

        if(!isset($p['app'])){
            echo 'Параметр app не указан, будет применено значение app.' . PHP_EOL;
            $a = null;
            while ($a === null) {
                echo "Продолжить установку? (yes/no): ";
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                echo 'Установка прервана' . PHP_EOL;
                exit();
            } 
            $p['app'] = 'app';
        }

        //Читаем параметры по умолчанию
        $paramsPath = ITEMS . '/' . $p['itemName'] . '/params.ini';
        if (file_exists($paramsPath)) {
            $param = parse_ini_file($paramsPath);
        } else {
            $param = [];
        }

        //Читаем параметры из ini файла
        $installIni = functions::parseInstallIni();
        $app = isset($p['app']) ? $p['app'] : 'app';
        $iniParam = [];
        if(isset($installIni[$app][$p['itemName']])){
            $iniParam = $installIni[$app][$p['itemName']];
        }
        $p = array_merge($param, $iniParam, $p);

        if (!functions::complectParams($p['itemName'], $p)) {
            echo 'К некоторым параметрам будут применены значения по умолчанию' . PHP_EOL;
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

        if(!functions::checkRelation($p)){
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
         
        $ItemIndexPath = ITEMS . '/' . $p['itemName'] . '/index.php';
        $itemIndex = null;
        if(file_exists($ItemIndexPath)){
            $class = 'system\\inst\\items\\' . $p['itemName'] . '\\index';
            $itemIndex = new $class();
        }    
        
        if($itemIndex){
            $p = $itemIndex->param($p);
        }

        files::copy($p);
        database::install($p);
        functions::addNameRelation($p);
        echo 'Установка компонента '. $p['itemName'] . ' завершена'. PHP_EOL;
    }
}