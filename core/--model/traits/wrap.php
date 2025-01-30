<?php 
namespace system\core\model\traits;

trait wrap
{
    private function wrap(string $param) : string
    {
        $r = [];
        foreach(explode('.', $param) as $i){
            preg_match('/`(.*?)`/si', $i, $m);
            $r[] = (empty($m) ? '`' . $i . '`' : $i);
        }
        return implode('.', $r);
    }
}