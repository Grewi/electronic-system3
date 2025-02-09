<?php

namespace system\inst\classes;

use system\inst\classes\text;
use system\inst\classes\connectDb;
use system\inst\classes\item;
use system\inst\classes\install;

class database
{
    public static function install(item &$item)
    {
        $configIni = ROOT . '/' . $item->name . '/configs/.database.ini';
        $configPhp = ROOT . '/' . $item->name . '/configs/database.php';
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
            $class = $item->app . '\\configs\\database';
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
            if (isset($instIni[$item->app]['database.type'])) {
                $dbType = $instIni[$item->app]['database.type'];
                $dbFile = $instIni[$item->app]['database.file'];
                $dbName = $instIni[$item->app]['database.name'];
                $dbHost = $instIni[$item->app]['database.host'];
                $dbUser = $instIni[$item->app]['database.user'];
                $dbPass = $instIni[$item->app]['database.pass'];
            }
        }

        $filesPath = ITEMS . '/' . $item->name . '/database/' . $dbType . '.sql';

        if (file_exists($filesPath)) {

            if (!$dbType) {
                text::print('Недостаточно данных для подключения к базе данных');
                $a = null;
                while ($a === null) {
                    text::print('Продолжить установку? (yes/no): ');
                    $a = functions::yes(trim(fgets(STDIN)));
                }
                if (!$a) {
                    text::print('Установка прервана', true);
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
                foreach ($item->params as $aa => $ii) {
                    preg_match_all('/\{\s*(' . $aa . ')\s*\}/si', $sql, $mm);
                    foreach ($mm[1] as $aaa => $iii) {
                        $sql = str_replace($mm[0][$aaa], $item->params[$iii], $sql);
                    }
                }
                $db->query($sql);
            } catch (\Exception $e) {
                text::danger('Ошибка загрузки в базу данных');
                text::print($e->getMessage());
            }

        }

    }
}