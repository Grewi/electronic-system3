<?php

namespace system\core\events;

trait text
{
    private function strong($a, $b){
        return 'с <strong>' . $a . '</strong> на <strong>' . $b . '</strong>';
    }

    private function strongBool($a, $b){
        return 'с <strong>' . ($a == 1 ? 'Да':'Нет') . '</strong> на <strong>' . ($b == 1 ? 'Да' : 'Нет') . '</strong>';
    }
}