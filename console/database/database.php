<?php

namespace system\console\database;

use system\core\config\config;
use system\core\database\database as db;
use system\core\text\text;
use system\core\zip\zip;

class database
{

    public function createDump()
    {
        $database = isset(ARGV[2]) ? preg_replace('/[^a-zA-Z0-9.-_]/ui', '', ARGV[2]) : 'database';
        try {
            $dbName = getConfig($database, 'name');
            $dbHost = getConfig($database, 'host');
            $dbPass = getConfig($database, 'pass');
            $dbUser = getConfig($database, 'user');
        } catch (\Exception $e) {
            text::warn('Проверьте файл конфигурации и указанные параметры.');
            text::danger('Не удалось загрузить конфигурацию базы данных "' . $database . '"', true);
        }
        $dumpPath = APP . '/cache/dump';
        $dumpSql = $dumpPath . '/' . date('Y-m-d', time());
        $fileName = date('Y-m-d__U', time());
        createDir($dumpSql);
        $fileSql = $dumpSql . '/' . $fileName . '.sql';
        // exec('mysqldump --user=' . $dbUser . ' --password=' . $dbPass . ' --host=' . $dbHost . ' ' . $dbName . ' --set-charset=utf8mb4 > ' . $fileSql, $output, $status);
        exec('mysqldump --user=' . $dbUser . ' --password=' . $dbPass . ' --host=' . $dbHost . ' ' . $dbName . ' --result-file=' . $fileSql, $output, $status);
        zip::zip($dumpSql, $dumpPath . '/' . $fileName . '.zip');
        $files = scandir($dumpSql);
        if (is_iterable($files)) {
            foreach ($files as $file) {
                if (is_file($dumpSql . '/' . $file)) {
                    unlink($dumpSql . '/' . $file);
                }
            }
        }
        rmdir($dumpSql);
        text::success('Создан файл "' . $dumpPath . '/' . $fileName . '.zip"');
        text::primary('Операция завершена', true);
    }

    public function restoreDump()
    {
        $ARGV = ARGV;
        if (is_array($ARGV)) {
            if (!isset(ARGV[2])) {
                text::danger('Не указан обязательный параметр');
                text::warn('Необходимо указать имя файла дампа (с расширением).');
                text::warn('Файл должен быть расположен в каталоге "' . APP . '/cache/dump/"', true);
            }
            $parametr = $ARGV[2];

            $database = isset(ARGV[3]) ? preg_replace('/[^a-zA-Z0-9.-_]/ui', '', ARGV[3]) : 'database';

            try {
                $dbName = getConfig($database, 'name');
                $dbHost = getConfig($database, 'host');
                $dbPass = getConfig($database, 'pass');
                $dbUser = getConfig($database, 'user');
            } catch (\Exception $e) {
                text::warn('Проверьте файл конфигурации и указанные параметры.');
                text::danger('Не удалось загрузить конфигурацию базы данных "' . $database . '"', true);
            }
        } else {
            text::danger('Не удалось получить необходимые параметры', true);
        }

        $dir = APP . '/cache/dump/' . $parametr;
        $print = exec('mysql  --user=' . $dbUser . ' --password=' . $dbPass . ' --host=' . $dbHost . ' ' . $dbName . ' < ' . $dir, $output, $status);
        text::primary('Операция завершена', true);
    }

    /**
     * Очистка базы данных
     */
    public function dropTables()
    {
        $database = isset(ARGV[2]) ? preg_replace('/[^a-zA-Z0-9.-_]/ui', '', ARGV[2]) : 'database';
        try {
            $dbName = getConfig($database, 'name');
            $dbHost = getConfig($database, 'host');
            $dbPass = getConfig($database, 'pass');
            $dbUser = getConfig($database, 'user');
        } catch (\Exception $e) {
            text::warn('Проверьте файл конфигурации и указанные параметры.');
            text::danger('Не удалось загрузить конфигурацию базы данных "' . $database . '"', true);
        }
        $db = db::connect();
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $tables = $db->fetchAll('SELECT TABLE_NAME FROM `INFORMATION_SCHEMA`.`TABLES` WHERE TABLE_SCHEMA = "' . getConfig('database', 'name') . '"', []);
        foreach ($tables as $t) {
            $db->query("DROP TABLE " . $t->TABLE_NAME, []);
        }
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');
        text::primary('Операция завершена', true);
    }

    private function zip($folder, $zipFile)
    {
        // Получаем реальный путь к нашей папке
        $rootPath = realpath($folder);

        // Инициализация объекта архива
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Создание рекурсивного итератора каталогов

        /** @var SplFileInfo[] $files */

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Пропустите каталоги (они будут добавлены автоматически)
            if (!$file->isDir()) {
                // Получение реального и относительного пути для текущего файла
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                // Добавить текущий файл в архив
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip-архив будет создан только после закрытия объекта
        $zip->close();
    }
}
