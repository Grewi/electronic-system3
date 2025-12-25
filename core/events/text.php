<?php

namespace system\core\events;

trait text
{
    private function strong($a, $b){

        return 'с <strong>' . $this->empty($a) . '</strong> на <strong>' . $this->empty($b) . '</strong>';
    }

    private function strongBool($a, $b){
        return 'с <strong>' . ($a == 1 ? 'Да':'Нет') . '</strong> на <strong>' . ($b == 1 ? 'Да' : 'Нет') . '</strong>';
    }

    private function empty(mixed $a)
    {
        $n = '<span style="color:#ccc; font-size:10px;"> (не указано) </span>';
        return ($a === '' || is_null($a) ? $n : $a);      
    }
}