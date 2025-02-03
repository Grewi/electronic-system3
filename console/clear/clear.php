<?php

namespace system\console\clear;
use system\core\text\text;
class clear
{
    public function index(): void
    {
        $this->cleanConfig();
        $this->cleanCache();
        text::primary('Операция завершена', true);
    }

    public function cache()
    {
        $this->cleanCache();
        text::primary('Операция завершена', true);
    }

    public function config()
    {
        $this->cleanConfig();
        text::primary('Операция завершена', true);
    }

    private function cleanConfig(): void
    {
        $dirConfigs = scandir(APP . '/configs/');
        foreach ($dirConfigs as $i) {
            if (substr($i, -4) == '.ini') {
                text::warn(APP . '/configs/' . $i);
                unlink(APP . '/configs/' . $i);
            }
        }
        text::success('Файлы конфигураций удалены');
    }

    private function cleanCache(): void
    {
        if (file_exists(APP . '/cache/views/')) {
            $dirCache = scandir(APP . '/cache/views/');
            if(count($dirCache) <= 2){
                text::warn('Нет файлов для удаления.');
                return;
            }
            foreach ($dirCache as $i) {
                if ($i != '.' && $i != '..') {
                    $this->deleteDir(APP . '/cache/views/' . $i);
                }
            }
            text::success('Файлы кеш шаблонов удалён.');
        }else{
            text::danger('Директории "' . APP . '/cache/views/' . '" не существует.');
        }
    }

    private function deleteDir(string $dirPath): void
    {
        if (!is_dir($dirPath)) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}