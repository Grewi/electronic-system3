<?php
$p = parametrs();

if (isset($p['itemName'])) {
    $iDir = SYSTEM . '/inst/items/' . $p['itemName'];
    if (file_exists($iDir)) {
        if (isset($p['help'])) {
            $iDirHelp = $iDir . '/help.txt';
            if (file_exists($iDirHelp)) {
                echo file_get_contents($iDirHelp) . PHP_EOL;
            } else {
                echo 'Справочная информация не обнаружена.' . PHP_EOL;
            }
        } else {
            if (!complectParams($p['itemName'])) {
                echo 'Будут применены параметры по умолчанию' . PHP_EOL;
            }
        }

        $adminPanel = null;
        while ($adminPanel === null) {
            echo "Продолжить установку? (yes/no): ";
            $adminPanel = bollRequest(trim(fgets(STDIN)));
        }
        if (!$adminPanel) {
            echo 'Установка прервана' . PHP_EOL;
            exit();
        }else{
            copyFile($p);
        }

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
