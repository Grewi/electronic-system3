<?php

namespace system\inst\classes;

class functions
{
    public static function yes(string $request): bool
    {
        $ok = ['1', 'y', 'yes', 'да', 'д'];
        return in_array(mb_strtolower($request), $ok) ? true : false;
    }

    public static function parametrs(): array
    {
        global $argv;
        $r = [];
        if ($argv) {
            foreach ($argv as $a => $i) {
                if ($a == 1) {
                    $r['itemName'] = $i;
                } elseif ($a > 1) {
                    $s = explode('=', $i);
                    $r[$s[0]] = (count($s) == 1) ? 1 : $s[1];
                }
            }
        }
        return $r;
    }

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

    public static function complectParams($item, $param): bool
    {
        $a = ITEMS . '/' . $item . '/params.ini';
        $installIni = functions::parseInstallIni();
        $b = self::parametrs();
        $r = true;
        if (file_exists($a)) {
            $c = parse_ini_file($a);
            foreach ($c as $j => $i) { 
                $r = $r && (isset($b[$j]) || isset($installIni[$param['app']][$param['itemName']][$j]) ) ? true : false;
            }
        }
        return $r;
    }

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

    public static function addNameRelation($param) : void
    {

        if (file_exists(ROOT . '/relation.json')) {
            $data = json_decode(file_get_contents(ROOT . '/relation.json'), true);
        } else {
            $data = [];
        }
        $data[$param['app']]['relations'][] = $param['itemName'];
        $data[$param['app']]['relations'] = array_unique($data[$param['app']]['relations']);
        file_put_contents(ROOT . '/relation.json', json_encode($data));
    }

    public static function checkRelation($param) : bool
    {
        $data = [];
        if (file_exists(ROOT . '/relation.json')) {
            $data = json_decode(file_get_contents(ROOT . '/relation.json'), true);
        }

        $relPath = ITEMS . '/' . $param['itemName'] . '/relations.ini';
        if (file_exists($relPath)) {
            $rel = parse_ini_file($relPath);
            $r = isset($rel['items']) ? explode(',', $rel['items']) : [];

            $rr = isset($data[$param['app']]['relations']) ? $data[$param['app']]['relations'] : [];
            $res = true;
            foreach ($r as $i) {
                $res = in_array(trim($i), $rr) && $res ? true : false;
            }
            return $res;
        }
    }

    public static function parseInstallIni(): array
    {
        $iniPath = ROOT . '/install.ini';
        if (!file_exists($iniPath)) {
            return [];
        }
        $ini = parse_ini_file($iniPath, true, INI_SCANNER_TYPED);
        $r = [];
        foreach ($ini as $a => $i) {
            foreach ($i as $aa => $ii) {
                $n = explode('.', $aa);
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
