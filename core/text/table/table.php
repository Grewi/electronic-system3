<?php

namespace system\core\text\table;

use system\core\text\table\color;
use system\core\text\table\params;
use system\core\text\table\termonal;

class table
{
    private array $struct = [];
    private string $e = '…';

    /**
     * w - ширина в символах
     * c - цвет текста
     * f - цвет фона
     * r - равнение l / c / r
     * z - символ заполнения
     */

    public function test()
    {
        $this->struct['params'] = (new params)
            ->width([33,34,33])
            ->color(color::text(7))
            ->bg(color::bg(0))
            ->r('l')->z(' ')
            ->get();
        
        $this->struct['rows'][0][0]['params'] = (new params)
            ->width([100])
            ->color(color::text(1))
            ->bg(color::bg(2))
            ->z('-')
            ->r('c')
            ->get();
        $this->struct['rows'][0][0]['text'] = '> text - 100 <';

        $this->struct['rows'][1][0]['params'] = (new params)->color(color::text(3))->bg(color::bg(3))->z('*')->get();
        $this->struct['rows'][1][0]['text'] = ' text1 ';

        $this->struct['rows'][1][1]['params'] = (new params)->color(color::text(1))->bg(color::bg(5))->z('~')->get();
        $this->struct['rows'][1][1]['text'] = ' text2 ';     

        $this->struct['rows'][1][2]['params'] = (new params)->color(color::text(3))->bg(color::bg(1))->z('|')->get();
        $this->struct['rows'][1][2]['text'] = ' text31234654654654654654654654654654654654654 ';     
        $this->print();                         
        // var_dump($this->struct);
    }

    public function print()
    {
        $print = '';
        $save = '';
        foreach($this->struct['rows'] as $keyRow => $row){
            if($keyRow == 'params'){
                continue;
            }
            foreach($row as $keyCol => $col){
                if($keyCol == 'params'){
                    continue;
                }
                $w = $this->i('rows.' . $keyRow . '.' . $keyCol . '.params.w.0') ?? $this->i('params.w.' . $keyCol);
                $f = $this->p('f', $keyRow, $keyCol);
                $c = $this->p('c', $keyRow, $keyCol);
                $r = $this->p('r', $keyRow, $keyCol);
                $z = $this->p('z', $keyRow, $keyCol);
                $text = $this->t($col['text'], $w, $r, $z);
                $print .= $c . $f . $text;
                $save  .= $text;
                $print .= color::text(7) . color::bg(0);
            }
            $save .= PHP_EOL;
            $print .= PHP_EOL;
        }
        file_put_contents(__DIR__ . '/test.log', $save);
        echo $print;
    }

    private function p($type, $keyRow, $keyCol)
    {
        return $this->i('rows.' . $keyRow . '.' . $keyCol . '.params.' . $type) 
            ?? $this->i('rows.' . $keyRow . '.params.' . $type) 
            ?? $this->i('params.' . $type);
    }

    private function t($text, $w, $r, $z)
    {
        if(mb_strlen($text) > $w){
            $text = mb_substr($text, 0, $w-1) . $this->e;
        }
        if($r == 'r'){
            return str_pad($text, $w, $z, STR_PAD_LEFT);
        }elseif($r == 'c'){ 
            return str_pad($text, $w, $z, STR_PAD_BOTH);
        }else{
            return str_pad($text, $w, $z, STR_PAD_RIGHT);
        }
    }

    private function i($p)
    {
        $a = explode('.', $p);
        $s = $this->struct;
        for($i = 0; $i < count($a); ++$i){
            if(!isset($s[$a[$i]])){
                return null;
            }
            $s = $s[$a[$i]];
        }
        return $s;
    }
}