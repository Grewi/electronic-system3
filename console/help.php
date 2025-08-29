<?php
namespace system\console;

use system\core\text\text;

class help
{
    private $arr = [
        'create/controller' => 'Параметры "v" (+ view) "crud" (create, read, update, delete методы)',
        'create/model' => 'Создать модель',
        'migrate' => 'Запустить миграции',
        'create/migration' => 'Создать файл миграции',
        'clean' => 'Удаление конфигураций и кеша шаблонов',
        'clean/cache' => 'Удаление кеша шаблонов',
        'create/dump' => 'Создание дампа базы',
        'restore/dump' => 'Востановление из дампа, путь к файлу /app/cache/dump/file.sql в параметре только file,',
        '' => 'третьим параметром принимает наименование конфигурации базы данны по умолчанию "database"',
        'drop/tables' => 'Удаление таблиц базы',
        'create/config' => 'Создание файла конфигурации, параметром принимает имя конфигурации',
        'create/config/ini' => 'Генерирование ini файлов конфигурации',
        'clean/config' => 'Удаление всех файлов конфигураций',
        'symlink' => 'Автоматическое создание символических ссылок из списка',
        'create/symlink' => 'Создание единичной символической ссылки "directiry/file directory/linkName"',
        'config' => 'Обновление ini файлов конфигураций',
        // 'add/complement' => 'Установка дополнений из отдельных репозиториев, параметром принимает имя репозитория',
        'style' => 'Компилирование css файлов по значениям из app/system/sass/sass.php',
        'style/info' => 'Список возможных значений для конфигурации sass',
    ];
    public function index()
    {
        $strlen = 0;
        foreach($this->arr as $a => $i){
            $l = strlen($a);
            $strlen = $l > $strlen ? $l : $strlen;
        }
        text::info('Команды доступные из консоли:');
        foreach($this->arr as $a => $i){
            $l = str_pad($a, $strlen, ' ', STR_PAD_RIGHT);
            echo text::pre() . text::color($l, 'Yellow') . ' - ' . $i . PHP_EOL;
        }
    }
}