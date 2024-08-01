<?php
use system\inst\classes\files;
use system\inst\classes\database;
use system\inst\classes\functions;

$p = functions::parametrs();

if (isset($p['itemName'])) {
    $iDir = ITEMS . '/' . $p['itemName'];
    if (file_exists($iDir)) {
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

        files::copy($p);
        database::install($p);
        functions::addNameRelation($p);
        

    } else {
        echo ('Проверьте параметры запроса. ' . $p['itemName'] . ' не найден!' . PHP_EOL);
        echo ('Для установки доступны: ' . PHP_EOL);
        functions::infoListItem();
        echo ('Для более подробной информации используйте параметр help ' . PHP_EOL);
    }
} else {
    echo 'Необходимо указать элемент установки.' . PHP_EOL;
    echo ('Для установки доступны: ' . PHP_EOL);
    functions::infoListItem();
}
