<?php

namespace system\console\database;

use system\core\database\database;
use system\core\text\text;

class migrate
{
    public function start(): void 
    {
         if (!file_exists(MIGRATIONS)) {
            createDir(MIGRATIONS . '/');
            text::warn('Создана директория миграций');
        }
        $allFiles = scandir(MIGRATIONS . '/');

        //Запуск миграции
        foreach ($allFiles as $i) {
            if ($i == '.' || $i == '..') {
                continue;
            }
            if(!is_dir(MIGRATIONS . '/' . $i)){
                text::warn('Файл ' . $i . ' не определён');
                continue;
            }
            $type = getConfig($i, 'type');
            if($type != 'mysql' && $type != 'sqlite' && $type != 'postgre'){
                text::warn('Для директории ' . $i . ' не определёна конфигурация');
                continue;
            }
            $this->index($i);
        }
        text::info('Программа миграции завершена');

    }

    public function index(string $database): void
    {
        $db = database::connect($database);
        //Автоопределение таблицы миграции в БД
        try {
            $m = $db->fetch('SELECT COUNT(*) as count FROM `migrations`', []);
        } catch (\PDOException $e) {
            $this->createTableMigration($database);
        }

        $allFiles = scandir(MIGRATIONS . '/' . $database);

        if (!is_iterable($allFiles)) {
            text::warn('Миграций для ' . $database . ' нет!');
            return;
        }
        //Запуск миграции
        foreach ($allFiles as $i) {
            if ($i == '.' || $i == '..') {
                continue;
            }
            $i = str_replace('.sql', '', $i);
            $m = $db->fetch('SELECT * FROM migrations WHERE name = "' . $i . '"', []);

            if (empty($m)) {
                $mSql = file_get_contents(MIGRATIONS . '/' . $database . '/' . $i . '.sql');
                if (!empty($mSql)) {
                    $db->query('INSERT INTO migrations (`name`, `active`) VALUES ("' . $i . '", "' . date('Y-m-d H:i', time()) . '")', []);
                    if(getConfig($database, 'type') == 'sqlite'){
                        $listSql = explode(';', $mSql);
                        foreach($listSql as $oneSql){
                            $db->query($oneSql, []);
                        }
                    }else{
                        $db->query($mSql, []);
                    }
                    text::success('Применён ' . $i );
                } else {
                    text::danger('Пустой файл миграции ' . $i);
                }
            } else {
                text::info('Пропущен ' . $i);
            }
        }
        text::primary('Операция для ' . $database . ' завершена');
    }

    public function createMigration(): void
    {
        $args = ARGV;
        if (isset($args[2])) {
            $type = getConfig($args[2], 'type');
            if($type != 'mysql' && $type != 'sqlite' && $type != 'postgre'){
                text::warn('Для имени ' . $args[2] . ' не определёна конфигурация', true);
            }            
            $database = $args[2];
        }else{
            text::warn('Необходимо указать имя конфигурации БД', true);
        }
        
        if (isset($args[3])) {
            $parametr = $args[3];
        }else{
            text::warn('Имя миграции не указано.');
            $parametr = '';
        }

        $dir = MIGRATIONS . '/' . $database;

        createDir($dir);

        $s = preg_replace("/[^a-zA-Z0-9\s]/", '_', $parametr);
        $fileName = $dir . '/' . date('Y_m_d_U') . '_' . $s . '.sql';
        file_put_contents($fileName, '');
        text::success('Файл миграции "' . $fileName . '" создан');
        text::primary('Операция завершена', true);
    }

    private function createTableMigration(string $database)
    {
        $db = database::connect($database);
        if (getConfig($database, 'type') == 'sqlite') {
            $startSql = '
            CREATE TABLE `migrations` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` varchar(255) NOT NULL,
                `active` timestamp NULL DEFAULT NULL
                );
                ';
        } else {
            $startSql = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
            START TRANSACTION;
            SET time_zone = "+00:00";
            
            CREATE TABLE `migrations` (
            `id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `active` timestamp NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            
            ALTER TABLE `migrations`
            ADD PRIMARY KEY (`id`);
            
            
            ALTER TABLE `migrations`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            COMMIT;';
        }
        $db->query($startSql, []);
        text::warn('Создана стартовая таблица миграции ' . $database . '!');

    }
}
