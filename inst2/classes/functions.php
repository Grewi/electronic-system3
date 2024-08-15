<?php

namespace system\inst2\classes;

use system\core\app\app;

class functions
{
    /**
     * Определяет параметры запроса
     */
    public static function parametrs(): void
    {
        global $argv;
        $app = app::app();
        if ($argv) {
            foreach ($argv as $a => $i) {
                if ($a == 1) {
                    $app->param = $i;
                } elseif ($a > 1) {
                    $s = explode('=', $i);
                    $app->params->{$s[0]} = (count($s) == 1) ? 1 : $s[1];
                }
            }
        }
    }

    /**
     * Выводит содержимое info.txt установленных в системе компонентов
     */
    public static function infoListItem(): void
    {
        foreach (self::listItems() as $i) {
            $item = ITEMS . '/' . $i;
            if (file_exists($item . '/info.txt')) {
                echo '    ' . $i . ': ' . file_get_contents($item . '/info.txt') . PHP_EOL;
            } else {
                echo $i . PHP_EOL;
            }
        }
    }

    /**
     * Генерирует список установленных в системе компонентов
     */
    public static function listItems(): array
    {
        $items = [];
        $itemsDir = scandir(ITEMS);
        if ($itemsDir) {
            foreach ($itemsDir as $i) {
                if ($i == '.' || $i == '..') {
                    continue;
                }
                $items[] = $i;
            }
        }
        return $items;
    }

        /**
     * Список доступных к установке компонентов
     */
    public static function listItemsForInstall() : array
    {
        $installIni = functions::parseInstallIni();
        $data = [];
        $r = [];
        if (file_exists(INSTALL_JSON)) {
            $data = json_decode(file_get_contents(INSTALL_JSON), true);
        }

        foreach($installIni as $a => $i){
            foreach($i as $aa => $ii){
                if(isset($data[$a]['relations'])){
                    if(in_array($aa, $data[$a]['relations'])){
                        continue;
                    }
                }
                if($aa == 'all'){
                    continue;
                }
                $r[$aa] = $ii;
            }
        }

        return $r;
    }

        /**
     * Получает параметры install.ini файла
     */
    public static function parseInstallIni(): array
    {
        // $app = app::app();
        if (!file_exists(INSTALL_INI)) {
            return [];
        }
        $ini = parse_ini_file(INSTALL_INI, true, INI_SCANNER_TYPED);
        $r = [];
        foreach ($ini as $a => $i) {
            foreach ($i as $aa => $ii) {
                $n = explode('.', $aa);

                // $item = empty($n[0]) ? $app->item->name : $n[0];
                $item = $n[0];
                
                if (isset($n[1])) {
                    $param = $n[1];
                    $r[$a][$item][$param] = $ii;
                } else {
                    $p = explode(',', $ii);
                    foreach ($p as $pp) {
                        $r[$a][$item] = trim($pp);
                    }
                }
            }
        }
        
        return $r;
    }
}
