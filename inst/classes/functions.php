<?php
namespace system\inst\classes;
use system\inst\classes\install;
use system\inst\classes\text;
use system\core\files\files;
use system\core\zip\zip;

class functions
{
    public static function yes(string $request): bool
    {
        $ok = ['1', 'y', 'yes', 'да', 'д'];
        return in_array(mb_strtolower($request), $ok) ? true : false;
    }

    public static function constantFilesItem(item &$item): void
    {
        $item->pathFiles = ITEMS . '/' . $item->name . '/files.ini';
        $item->pathHelp = ITEMS . '/' . $item->name . '/help.txt';
        $item->pathInfo = ITEMS . '/' . $item->name . '/info.txt';
        $item->pathParams = ITEMS . '/' . $item->name . '/params.ini';
        $item->pathRelations = ITEMS . '/' . $item->name . '/relations.ini';
        $item->pathIndex = ITEMS . '/' . $item->name . '/index.php';
        $item->pathClass = 'system\\inst\\items\\' . $item->name . '\\index';
    }

    /**
     * Определяет параметры запроса
     */
    public static function parametrs(install &$install): void
    {
        global $argv;
        if ($argv) {
            foreach ($argv as $a => $i) {
                if ($a == 1) {
                    $install->param = $i;
                } elseif ($a > 1) {
                    $s = explode('=', $i);
                    $install->params[$s[0]] = (count($s) == 1) ? true : $s[1];
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
                echo '    ';
                text::p()->warn( $i);

                text::p('')->print(': ' . file_get_contents($item . '/info.txt'))->e();
            } else {
                text::p()->print($i)->e();
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
    public static function listItemsForInstall(install &$install): array
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
                        text::p()->warn('Элемент ' . $aa . ' уже установлен.')->e();
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
            text::p()->print('Для переустановки уже установленного элемента отредактируйте install.json в корне проекта.')->e();
            text::p()->print('Для принудительной перезаписи файлов используйте параметр - f')->e();
        }
        return $r;
    }

    /**
     * Получает параметры install.ini файла
     */
    public static function parseInstallIni(): array
    {
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
    public static function complectParams(item &$item): bool
    {
        $installIni = self::parseInstallIni();
        $r = true;
        if (file_exists($item->pathParams)) {
            $c = parse_ini_file($item->pathParams);
            foreach ($c as $j => $i) {
                $r = $r && (!empty($item->params[$j]) || isset($installIni[$item->app][$item->name][$j])) ? true : false;
            }
        }
        return $r;
    }

    /**
     * Проверяет наличие записи о компоненте в файле install.json
     */
    public static function checkRelation(item &$item): bool
    {
        $data = [];
        if (file_exists(INSTALL_JSON)) {
            $data = json_decode(file_get_contents(INSTALL_JSON), true);
        }

        if (file_exists($item->pathRelations)) {
            $rel = parse_ini_file($item->pathRelations);
            $r = isset($rel['items']) ? explode(',', $rel['items']) : [];

            $rr = isset($data[(string)$item->app]['relations']) ? $data[$item->app]['relations'] : [];
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
    public static function addNameRelation(item &$item): void
    {
        if (file_exists(INSTALL_JSON)) {
            $data = json_decode(file_get_contents(INSTALL_JSON), true);
        } else {
            $data = [];
        }
        $data[$item->app]['relations'][] = $item->name;
        $data[$item->app]['relations'] = array_unique($data[$item->app]['relations']);
        file_put_contents(INSTALL_JSON, json_encode($data));
    }

    public static function installIni(install &$install): void
    {
        if (file_exists(ROOT . '/install.ini')) {
            text::p()->print('Файл install.ini уже существует')->exit();
        }
        $t = file_get_contents(INST . '/sample.install.ini');
        file_put_contents(ROOT . '/install.ini', $t);
        text::p()->success('Файл install.ini создан')->e();
        text::p()->warn('Файл содержит шаблонные данные. Проверьте и отредактируйте его')->exit();
    }

    public static function delItems(install &$install)
    {
        if($install->getParams('d') === true){
            return;
        }
        files::deleteDir(INST . '/items');
    }

    public static function unpuckItems(install &$install)
    {
        if($install->getParams('d') === true){
            return;
        }
        if(file_exists(INST . '/items')){
            text::p()->warn('Директория "' . INST . '/items" уже существует')->e();
            $a = null;
            while ($a === null) {
                text::p()->info("Удалить и продолжить стандартную установку? (yes/no): ")->e();
                $a = functions::yes(trim(fgets(STDIN)));
            }
            if ($a) {
                self::delItems($install);
                text::p()->print( 'Директория перезаписана')->e();
            }else{
                $install->setParams('d', true);
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
