<?php

namespace system\inst\classes;

use system\inst\classes\connectDb;

class database
{
    public static function install($param)
    {


        $configIni = ROOT . '/' . $param['app'] . '/configs/.database.ini';
        $configPhp = ROOT . '/' . $param['app'] . '/configs/database.php';
        $dbType = null;
        $dbFile = null;
        $dbName = null;
        $dbHost = null;
        $dbUser = null;
        $dbPass = null;

        if (file_exists($configIni)) {
            $dbData = parse_ini_file($configIni);
            $dbType = $dbData['type'];
            $dbFile = $dbData['file'];
            $dbName = $dbData['name'];
            $dbHost = $dbData['host'];
            $dbUser = $dbData['user'];
            $dbPass = $dbData['pass'];

        } elseif (file_exists($configPhp)) {
            $class = $param['app'] . '\\configs\\database';
            $dbClass = new $class();
            $dbData = $dbClass->set();
            $dbType = $dbData['type'];
            $dbFile = $dbData['file'];
            $dbName = $dbData['name'];
            $dbHost = $dbData['host'];
            $dbUser = $dbData['user'];
            $dbPass = $dbData['pass'];
        } elseif (file_exists(INSTALL_INI)) {
            $instIni = parse_ini_file(INSTALL_INI, true);
            if (isset($instIni[$param['app']]['database.type'])) {
                $dbType = $instIni[$param['app']]['database.type'];
                $dbFile = $instIni[$param['app']]['database.file'];
                $dbName = $instIni[$param['app']]['database.name'];
                $dbHost = $instIni[$param['app']]['database.host'];
                $dbUser = $instIni[$param['app']]['database.user'];
                $dbPass = $instIni[$param['app']]['database.pass'];
            }
        }

        $filesPath = ITEMS . '/' . $param['itemName'] . '/database/' . $dbType . '.sql';

        if (file_exists($filesPath)) {

            if (!$dbType) {
                echo 'Недостаточно данных для подключения к базе данных' . PHP_EOL;
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

            try {
                $configs = [
                    'file' => $dbFile,
                    'type' => $dbType,
                    'name' => $dbName,
                    'host' => $dbHost,
                    'user' => $dbUser,
                    'pass' => $dbPass,
                ];
                $db = connectDb::c($configs);

                $sql = file_get_contents($filesPath);
                foreach ($param as $aa => $ii) {
                    preg_match_all('/\{\s*(' . $aa . ')\s*\}/si', $sql, $mm);
                    foreach ($mm[1] as $aaa => $iii) {
                        $sql = str_replace($mm[0][$aaa], $param[$iii], $sql);
                    }
                }
                $db->query($sql);
            } catch (\Exception $e) {
                echo 'Ошибка загрузки в базу данных' . PHP_EOL;
                echo $e->getMessage() . PHP_EOL;
            }

        }

    }
}