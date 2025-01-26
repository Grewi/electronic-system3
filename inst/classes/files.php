<?php

namespace system\inst\classes;

use system\inst\classes\text;
use system\core\app\app;

class files
{
    static $permissions = 0755;

    public static function copy()
    {
        $app = app::app();
        //Читаем список файлов
        if (file_exists($app->item->path->files)) {
            $ftext = file_get_contents($app->item->path->files);
            preg_match_all('/\{\s*(.*?)\s*\}/si', $ftext, $matches);
            $t = [];

            foreach ($matches[1] as $a => $i) {
                if (empty($app->item->params->{$i})) {
                    $t[] = $i;
                }
            }
            if (!empty($t)) {
                $t = array_unique($t);
                text::warn('Отсутствует параметр(ы): ' . implode(', ', $t));
                text::danger('Установка прервана', true);
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
                        $i = self::copyFile($ib);
                    }

                    if ($ib[1] == 'd') {
                        $i = self::copyDir($ib);
                    }

                    if ($ib[1] == '>>') {
                        $i = self::copyFiles($ib);
                    }
                }
            }


            $ftext = implode(PHP_EOL, $fArray);
            $files = parse_ini_string($ftext, false, INI_SCANNER_TYPED);

            foreach ($files as $a => $i) {
                $f1 = ITEMS . '/' . $app->item->name . '/files/' . $i;
                $f2 = ROOT . '/' . $a;

                if (!is_null($i)) {
                    $ff = explode('/', $f2);
                    array_pop($ff);
                    self::createDir(implode('/', $ff));
                    if (!file_exists($f1)) {
                        text::print('Файл: ' . $f1 . ' отсутствует');
                    } else {
                        if (file_exists($f2)) {
                            if (($app->params->f === true)) {
                                text::print('Файл: ' . $f2 . ' перезаписан');
                            } else {
                                text::print('Файл: ' . $f2 . ' уже существует');
                                continue;
                            }
                        }

                        copy($f1, $f2);
                        $pext = pathinfo($f2);
                        if ((isset($pext['extension']) && $pext['extension'] == 'php') || empty($pext['extension'])) {
                            $content = file_get_contents($f2);
                            foreach ($app->item->params as $aa => $ii) {
                                preg_match_all('/\{\s*(' . $aa . ')\s*\}/si', $content, $mm);
                                foreach ($mm[1] as $aaa => $iii) {
                                    $content = str_replace($mm[0][$aaa], $app->item->params->{$iii}, $content);
                                }
                            }
                            file_put_contents($f2, $content);
                        }
                    }
                } else {
                    self::createDir($f2);
                }
            }
            text::success('Файлы скопированны');
        } else {
            text::danger('Файлов для копирования нет');
        }
    }

    private static function copyFile(array $ib): string
    {
        $app = app::app();
        if (isset($ib[2])) {
            preg_match('/\{\s*(.*?)\s*\}/si', $ib[2], $m2);
            if (!empty($m2)) {
                $ib[2] = str_replace($m2[0], $app->item->params->{$m2[1]}, $ib[2]);
            }
        }

        preg_match('/\{\s*(.*?)\s*\}/si', $ib[0], $m);
        if (!empty($m)) {
            $a = str_replace($m[0], $m[1], $ib[0]);
            $b = str_replace($m[0], $app->item->params->{$m[1]}, $ib[0]);
            if (isset($ib[2])) {
                return $b . ' = ' . $ib[2];
            } else {
                return $b . ' = ' . $a;
            }
        } else {
            if (isset($ib[2])) {
                return $ib[0] . ' = ' . $ib[2];
            } else {
                return $ib[0] . ' = ' . $ib[0];
            }
        }
    }

    private static function copyFiles(array $ib): string
    {
        $app = app::app();
        preg_match('/\{\s*(.*?)\s*\}/si', $ib[0], $m);
        if (!empty($m)) {
            $ri = str_replace($m[0], $m[1], $ib[0]);
            $l = str_replace($m[0], $app->item->params->{$m[1]}, $ib[0]);
            $i = $l . ' = ' . $ri;
        }
        $sd = ITEMS . '/' . $app->item->name . '/files/' . $ri;
        $sdFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sd),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $list = '';
        foreach ($sdFiles as $i) {
            if ($i->isFile()) {
                $filePath = $i->getRealPath();
                $relativePath = substr($filePath, strlen($sd));
                $relativePath = str_replace('\\', '/', $relativePath);
                $list .= $l . $relativePath . ' = ' . $ri . $relativePath . PHP_EOL;
            }
        }
        return $list;
    }

    private static function copyDir(array $ib): string
    {
        $app = app::app();
        preg_match('/\{\s*(.*?)\s*\}/si', $ib[0], $m);
        if (!empty($m)) {
            $l = str_replace($m[0], $app->item->params->{$m[1]}, $ib[0]);
            $i = $l . ' = null';
        } else {
            $i = $ib[0] . ' = null';
        }
        return $i;
    }

    /**
     * Проверяет наличие и создаёт директории, если их нет
     */
    public static function createDir($path): void
    {
        if (!file_exists($path)) {
            mkdir($path, self::$permissions, true);
        }
    }
}
