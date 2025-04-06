<?php

namespace system\console\database;

use system\core\database\database;
use system\core\text\text;
use system\core\config\config;

class migrate
{
    public function index(): void
    {
        $db = database::connect();
        //Автоопределение таблицы миграции в БД
        try {
            $start = $db->fetch('SELECT COUNT(*) as count FROM `migrations`', []);
        } catch (\PDOException $e) {
            if (!$start) {
                if (getConfig('database', 'type') == 'sqlite') {
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
                text::warn('Создана стартовая таблица миграции!');
            }
        }

        if (!file_exists(MIGRATIONS)) {
            createDir(MIGRATIONS . '/');
            text::warn('Создана директория миграций');
        }
        $allFiles = scandir(MIGRATIONS . '/');

        if (!is_iterable($allFiles)) {
            text::warn('Миграций нет!');
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
                $mSql = file_get_contents(MIGRATIONS . '/' . $i . '.sql');
                if (!empty($mSql)) {
                    $db->query('INSERT INTO migrations (`name`, `active`) VALUES ("' . $i . '", "' . date('Y-m-d H:i', time()) . '")', []);
                    $db->query($mSql, []);
                    text::success('Применён ' . $i );
                } else {
                    text::danger('Пустой файл миграции ' . $i);
                }
            } else {
                text::info('Пропущен ' . $i);
            }
        }
        text::primary('Операция завершена', true);
    }

    public function createMigration(): void
    {
        $ARGV = ARGV;
        if (is_array($ARGV)) {
            if (!isset(ARGV[2])) {
                text::warn('Имя миграции не указано.');
                $parametr = '';
            }else{
                $parametr = $ARGV[2];
            }
        } else {
            text::danger('Не удалось получить необходимые параметры', true);
        }
        $s = preg_replace("/[^a-zA-Z0-9\s]/", '_', $parametr);
        $fileName = MIGRATIONS . '/' . date('Y_m_d_U') . '_' . $s . '.sql';
        file_put_contents($fileName, '');
        text::success('Файл миграции "' . $fileName . '" создан');
        text::primary('Операция завершена', true);
    }
}
