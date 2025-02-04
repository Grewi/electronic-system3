<?php

namespace system\console\symlink;
use system\core\symlink\symlink;
use system\core\text\text;

class createSymlink
{
    public function index()
    {
        $ARGV = ARGV;
        if (is_array($ARGV)) {
            if (empty($target) || empty($link)) {
                text::danger('Отсутствуют необходимые значения.');
                text::warn('Первым значением необходимо указать существующий источник (директория или файл),');
                text::warn('а вторым место, где будет создана ссылка', true);
            }
            $target = $ARGV[2];
            $link = $ARGV[3];
        } else {
            text::danger('Не удалось получить необходимые параметры', true);
        }

        if ((new symlink())->create($target, $link)) {
            text::success('Выполненно успешно.');
        } else {
            text::danger('Ошибка выполнения.');
        }
    }


}
