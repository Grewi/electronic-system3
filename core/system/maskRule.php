<?php

namespace system\core\system;

class maskRule
{
    private array $rules;

    /**
     * Маска прав 
     */
    public function __construct(string $rules, $size = 128)
    {
        $a = str_split($rules);
        for($i = 0; $i <= ceil($size / 4); $i++){
            $this->rules[$i] = isset($a[$i]) ? str_pad(base_convert($a[$i], 16, 2), 4, '0', STR_PAD_LEFT) : '0000';
        }
    }

    //Возвращает маску прав
    public function getRules()
    {
        $a = '';
        foreach($this->rules as $i){
            $a .= base_convert($i, 2, 16);
        }
        return $a;
    }

    //Возвращает значение по номеру
    public function getRule(int $number) 
    {
        if($number < 0){
            throw new \GlobalException('Номер правила не может быть меньше единицы');
        }
        $a = floor($number/4);
        $b = $number%4;
        return isset($this->rules[$a][$b]) && $this->rules[$a][$b] == '1' ? true : false;
    }

    //Устанавливает значение по номеру
    public function setRule(int $number, bool $value): void
    {
        $a = floor($number/4);
        $b = $number%4;
        if(isset($this->rules[$a][$b])){
            $this->rules[$a][$b] = $value ? '1' : '0';
        }
    }
}
