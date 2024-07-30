<?php

namespace system\inst\classes;

class files
{
    public static function copy($p)
    {
        //Читаем параметры по умолчанию
        $paramsPath = ROOT . '/system/inst/items/' . $p['itemName'] . '/param.ini';
        if (file_exists($paramsPath)) {
            $param = parse_ini_file($paramsPath);
        } else {
            $param = [];
        }

        $param = array_merge($param, $p);

        //Читаем список файлов
        $filesPath = ROOT . '/system/inst/items/' . $p['itemName'] . '/files.ini';
        if (file_exists($paramsPath)) {
            $ftext = file_get_contents($filesPath);
            preg_match_all('/\{\s*(.*?)\s*\}/si', $ftext, $matches);
            $t = [];
            foreach ($matches[1] as $a => $i) {
                if (!isset($param[$i])) {
                    $t[] = $i;
                }
            }
            if (!empty($t)) {
                $t = array_unique($t);
                echo 'Отсутствует параметр(ы): ' . implode(', ', $t) . PHP_EOL;
                echo 'Установка прервана' . PHP_EOL;
            }

            $fArray = explode(PHP_EOL, $ftext);
            foreach ($fArray as &$i) {
                $ia = explode('=', $i);
                if (count($ia) < 2) {
                    $i = preg_replace('/\s\s+/', ' ', $i);
                    $ib = explode(' ', $i);
                    if (!isset($ib[1])) {
                        continue;
                    }

                    if ($ib[1] == '>') {
                        preg_match('/\{\s*(.*?)\s*\}/si', $ib[0], $m);
                        if (!empty($m)) {
                            $ri = str_replace($m[0], $m[1], $ib[0]);
                            $l = str_replace($m[0], $param[$m[1]], $ib[0]);
                            $i = $l . ' = ' . $ri;
                        } else {
                            $i = $ib[0] . ' = ' . $ib[0];
                        }
                    }

                    if ($ib[1] == 'd') {
                        preg_match('/\{\s*(.*?)\s*\}/si', $ib[0], $m);
                        if (!empty($m)) {
                            $l = str_replace($m[0], $param[$m[1]], $ib[0]);
                            $i = $l . ' = null';
                        } else {
                            $i = $ib[0] . ' = null';
                        }
                    }
                }
            }
            $ftext = implode(PHP_EOL, $fArray);

            $files = parse_ini_string($ftext, false, INI_SCANNER_TYPED);

            foreach ($files as $a => $i) {
                //Кого
                $f1 = ROOT . '/system/inst/items/' . $p['itemName'] . '/files/' . $i;
                //Куда
                $f2 = ROOT . '/' . $a;

                if (!is_null($i)) {
                    $ff = explode('/', $f2);
                    array_pop($ff);
                    createDir(implode('/', $ff));
                    if (!file_exists($f1)) {
                        echo 'Файл: ' . $f1 . ' отсутствует' . PHP_EOL;
                    } else {
                        if (file_exists($f2)) {
                            if (!isset($param['-f'])) {
                                echo 'Файл: ' . $f2 . ' уже существует' . PHP_EOL;
                                continue;
                            } else {
                                echo 'Файл: ' . $f2 . ' перезаписан' . PHP_EOL;
                            }
                        }
                        copy($f1, $f2);
                        $pext = pathinfo($f2);
                        if (isset($pext['extension']) && $pext['extension'] == 'php') {
                            $content = file_get_contents($f2);
                            foreach ($param as $aa => $ii) {
                                preg_match_all('/\{\s*(' . $aa . ')\s*\}/si', $content, $mm);
                                foreach ($mm[1] as $aaa => $iii) {
                                    $content = str_replace($mm[0][$aaa], $param[$iii], $content);
                                }
                            }
                            file_put_contents($f2, $content);
                        }
                    }
                } else {
                    createDir($f2);
                }
            }
            echo 'Файлы скопированны' . PHP_EOL;
        } else {
            echo 'Файлов для копирования нет' . PHP_EOL;
        }
    }
}
