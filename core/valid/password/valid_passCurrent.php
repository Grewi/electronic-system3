<?php

namespace system\core\valid\password;

use system\core\valid\item;
use system\core\user\register;

class valid_passCurrent extends item
{
    private string $table;
    private string $col;
    private int $id;

    public function __construct(string $table, string $col, int $id)
    {
        $this->table = $table;
        $this->col = $col;
        $this->id = $id;
    }

    public function control()
    {
        if ($this->original) {
            $user = db()->fetch('SELECT * FROM `' . $this->table . '` WHERE `id` = "' . $this->id . '"', []);
            if (isset($user->{$this->col})) {
                if (!password_verify($this->original, $user->{$this->col})) {
                    $this->setError('Ошибка пароля');
                    $this->setControl(false);
                }
            } else {
                $this->setError('Данные не найдены');
                $this->setControl(false);
            }
        }
    }

    public function getResult(): mixed
    {
        return null;
    }
}
