<?php
use system\inst\classes\files;
use system\inst\classes\database;

$p = parametrs();

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

        if (!complectParams($p['itemName'])) {
            echo 'Будут применены параметры по умолчанию' . PHP_EOL;
        }

        //Читаем параметры по умолчанию
        $paramsPath = ITEMS . '/' . $p['itemName'] . '/params.ini';
        if (file_exists($paramsPath)) {
            $param = parse_ini_file($paramsPath);
        } else {
            $param = [];
        }
        $p = array_merge($param, $p);

        $a = null;
        while ($a === null) {
            echo "Продолжить установку? (yes/no): ";
            $a = yes(trim(fgets(STDIN)));
        }
        if (!$a) {
            echo 'Установка прервана' . PHP_EOL;
            exit();
        }

        if(!checkRelation($p)){
            $relPath = ITEMS . '/' . $p['itemName'] . '/relations.ini';
            if(file_exists($relPath)){
                $rel = parse_ini_file($relPath);
                echo 'Для продолжения требуется установить: ' . $rel['items'] . PHP_EOL;
            }
            $a = null;
            while ($a === null) {
                echo "Продолжить установку? (yes/no): ";
                $a = yes(trim(fgets(STDIN)));
            }
            if (!$a) {
                echo 'Установка прервана' . PHP_EOL;
                exit();
            }
        }         

        files::copy($p);
        database::install($p);
        addNameRelation($p);
        

    } else {
        echo ('Проверьте параметры запроса. ' . $p['itemName'] . ' не найден!' . PHP_EOL);
        echo ('Для установки доступны: ' . PHP_EOL);
        infoListItem();
        echo ('Для более подробной информации используйте параметр help ' . PHP_EOL);
    }
} else {
    echo 'Необходимо указать элемент установки.' . PHP_EOL;
    echo ('Для установки доступны: ' . PHP_EOL);
    infoListItem();
}
