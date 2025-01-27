<?php

namespace system\inst\classes;

use system\inst\classes\text;
use system\core\app\app;
use system\core\files\files;
use system\core\zip\zip;

class functions
{
    public static function yes(string $request): bool
    {
        $ok = ['1', 'y', 'yes', 'да', 'д'];
        return in_array(mb_strtolower($request), $ok) ? true : false;
    }

    public static function constantFilesItem(): void
    {
        $app = app::app();
        $app->item->path->files = ITEMS . '/' . $app->item->name . '/files.ini';
        $app->item->path->help = ITEMS . '/' . $app->item->name . '/help.txt';
        $app->item->path->info = ITEMS . '/' . $app->item->name . '/info.txt';
        $app->item->path->params = ITEMS . '/' . $app->item->name . '/params.ini';
        $app->item->path->relations = ITEMS . '/' . $app->item->name . '/relations.ini';
        $app->item->path->index = ITEMS . '/' . $app->item->name . '/index.php';
        $app->item->path->class = 'system\\inst\\items\\' . $app->item->name . '\\index';
    }

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
                    $app->params->{$s[0]} = (count($s) == 1) ? true : $s[1];
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
                text::print('    ' . text::color($i, 'Yellow') . ': ' . file_get_contents($item . '/info.txt'));
            } else {
                text::print($i);
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
    public static function listItemsForInstall(): array
    {
        $installIni = functions::parseInstallIni();
        $data = [];
        $r = [];
        if (file_exists(INSTALL_JSON)) {
            $data = json_decode(file_get_contents(INSTALL_JSON), true);
        }
        $control = false;
        foreach ($installIni as $a => $i) {
            foreach ($i as $aa => $ii) {
                if (isset($data[$a]['relations'])) {
                    if (in_array($aa, $data[$a]['relations'])) {
                        $control = true;
                        text::warn('Элемент ' . $aa . ' уже установлен.');
                        continue;
                    }
                }
                if ($aa == 'all') {
                    continue;
                }
                $r[$a][$aa] = $ii;
            }
        }
        if($control){
            text::print('Для переустановки уже установленного элемента отредактируйте install.json в корне проекта.');
            text::print('Для принудительной перезаписи файлов используйте параметр - f');
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

    /**
     * Проверяет наличие необходимых параметров
     */
    public static function complectParams(): bool
    {
        $app = app::app();
        $installIni = self::parseInstallIni();
        $r = true;
        if (file_exists($app->item->path->params)) {
            $c = parse_ini_file($app->item->path->params);
            foreach ($c as $j => $i) {
                $r = $r && (!empty($app->item->params->{$j}) || isset($installIni[$app->item->params->app][$app->item->name][$j])) ? true : false;
            }
        }
        return $r;
    }

    /**
     * Проверяет наличие записи о компоненте в файле install.json
     */
    public static function checkRelation(): bool
    {
        $app = app::app();
        $data = [];
        if (file_exists(INSTALL_JSON)) {
            $data = json_decode(file_get_contents(INSTALL_JSON), true);
        }

        if (file_exists($app->item->path->relations)) {
            $rel = parse_ini_file($app->item->path->relations);
            $r = isset($rel['items']) ? explode(',', $rel['items']) : [];

            $rr = isset($data[(string)$app->item->params->app]['relations']) ? $data[$app->item->params->app]['relations'] : [];
            $res = true;
            foreach ($r as $i) {
                $res = in_array(trim($i), $rr) && $res ? true : false;
            }
            return $res;
        }
        return true;
    }

    /**
     * Добавляет имя компонента в файл install.json
     */
    public static function addNameRelation(): void
    {
        $app = app::app();
        if (file_exists(INSTALL_JSON)) {
            $data = json_decode(file_get_contents(INSTALL_JSON), true);
        } else {
            $data = [];
        }
        $data[$app->item->params->app]['relations'][] = $app->item->name;
        $data[$app->item->params->app]['relations'] = array_unique($data[$app->item->params->app]['relations']);
        file_put_contents(INSTALL_JSON, json_encode($data));
    }

    public static function installIni(): void
    {
        if (file_exists(ROOT . '/install.ini')) {
            text::print('Файл install.ini уже существует', true);
        }
        $t = file_get_contents(INST . '/sample.install.ini');
        file_put_contents(ROOT . '/install.ini', $t);
        text::success('Файл install.ini создан');
        text::warn('Файл содержит шаблонные данные. Проверьте и отредактируйте его', true);
    }

    // public static function print(string $text, bool $exit = false): void
    // {
    //     echo text::color('---> ', 'Green') . $text . PHP_EOL;
    //     if ($exit) {
    //         self::delItems();
    //         exit();
    //     }
    // }

    public static function delItems()
    {
        $app = app::app();
        if($app->params->d === true){
            return;
        }
        files::deleteDir(INST . '/items');
    }

    public static function unpuckItems()
    {
        $app = app::app();
        if($app->params->d === true){
            return;
        }
        if(file_exists(INST . '/items')){
            text::warn('Директория "' . INST . '/items" уже существует');
            $a = null;
            while ($a === null) {
                text::info("Удалить и продолжить стандартную установку? (yes/no): ");
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if ($a) {
                self::delItems();
                text::print('Директория перезаписана');
            }else{
                $app->params->d = true;
            }
            
        }
        zip::zipOpen(INST . '/items.zip', INST);
    }

    /**
     * возвращает последний элемент пути
     */
    public static function lastItemPath(string $path): string
    {
        $a = explode('/',$path);
        return array_pop($a);
    }
}
