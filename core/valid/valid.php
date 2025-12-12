<?php

namespace system\core\valid;

use Closure;
use system\core\valid\item;
use system\core\valid\other\valid_bool;
use system\core\valid\other\valid_csrf;
use system\core\valid\other\valid_email;
use system\core\valid\other\valid_empty;
use system\core\valid\number\valid_float;
use system\core\valid\number\valid_int;
use system\core\valid\number\valid_min;
use system\core\valid\number\valid_max;
use system\core\valid\text\valid_latInt;
use system\core\valid\text\valid_latRuInt;
use system\core\valid\text\valid_ru;
use system\core\valid\text\valid_text;
use system\core\valid\text\valid_strlen;
use system\core\valid\text\valid_strlenMax;
use system\core\valid\text\valid_strlenMin;
use system\core\valid\datetime\valid_date;
use system\core\valid\datetime\valid_time;
use system\core\valid\datetime\valid_datetime;
use system\core\valid\database\valid_unique;
use system\core\valid\database\valid_isset;
use system\core\valid\to\valid_toFloat;
use system\core\valid\to\valid_toInt;
use system\core\valid\to\valid_toString;
use system\core\valid\to\valid_toNull;
use system\core\valid\bisness\valid_inn;
use system\core\valid\bisness\valid_bik;
use system\core\valid\bisness\valid_kpp;
use system\core\valid\bisness\valid_ogrn;
use system\core\valid\bisness\valid_schet;

class valid
{
    private bool $control = true;

    /**
     * control - текущее состояние
     * errors - список ошибок
     * original - полученное значение
     * result - конечное значение
     * return - возвращает или нет значения
     */
    private array $data = [];

    /**
     * Массив изначальных значений key => value
     */
    private array $original = [];

    /**
     * 
     * @param string $name
     * @param item|string|array $item
     * @param callable|null $function
     * @return void
     */
    public function add(string $name, item $item, callable|null $function = null)
    {
        if (!isset($this->data[$name])) {
            // return;
        }
        
        if(isset($this->data[$name]['control'])){
            $item->setControl($this->data[$name]['control']);
        }

        if (isset($this->original[$name])) {  
            $item->setOriginal($this->original[$name]);
        }
        if ($function) {
            $function($item);
        }
        dump($item);
        $item->control();
        $this->data[$name]['control'] = $item->getControl();
        $this->data[$name]['errors'] = $item->getErrors() + ($this->data[$name]['errors']??[]);
        $this->data[$name]['original'] = $item->getOriginal();

        if ($item->getResult || !isset($this->data[$name])) {
            $this->data[$name]['result'] = $item->getResult();
        }
        $this->data[$name]['return'] = $item->getResult;
        $this->setControl($item->getControl());
    }

    public function error(string $name): array
    {
        return isset($this->data[$name]) ? $this->data[$name]['errors'] : [];
    }

    public function errors(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors'])) {
                $errors[$a] = $i['errors'];
            }
        }
        return $errors;
    }

    public function errorArray(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors']) && count($i['errors']) > 0) {
                foreach ($i['errors'] as $er) {
                    $errors[$a][] = $er;
                }
            }
        }
        return $errors;
    }

    public function errorList(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors']) && count($i['errors']) > 0) {
                $errors[$a] = mb_strtolower(implode(', ', $i['errors']));
            }
        }
        return $errors;
    }

    public function original(string $name):mixed
    {
        return (isset($this->data[$name]['original']) ? $this->data[$name]['original'] : null);
    }

    public function originals(): array
    {
        $original = [];
        foreach ($this->data as $a => $i) {
            if(!$i['return']){
                continue;
            }
            if (isset($i['original'])) {
                $original[$a] = $i['original'];
            }
        }
        return $original;
    }

    public function results(bool $null = true): array
    {
        $result = [];
        
        foreach ($this->data as $a => $i) {
            if(!$i['return']){
                continue;
            }
            if (isset($i['result'])) {
                $result[$a] = $i['result'];
            }else{
                if($null){
                    $result[$a] = null;
                }
            }
        }
        return $result;
    }

    public function result(string $name):mixed
    {
        return (isset($this->data[$name]['result']) ? $this->data[$name]['result'] : null);
    }    

    public function control(): bool
    {
        return $this->control;
    }

    public function setOriginalArray(array $data)
    {
        $this->original = $data;
    }

    public function setRequest(): void
    {
        $this->original = $_REQUEST;
    }

    private function setControl(bool $control)
    {
        if ($this->control) {
            $this->control = $control;
        }
    }

    // number

    /**
     * Значение должно быть целочисленным числом
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */
    public function int(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_int(), $function);
        return $this;
    }

    /**
     * Значение должно быть дробным числом
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function float(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_float(), $function);
        return $this;
    }  
    
    /**
     * Значение не должно быть менее указаного
     * @param string $name - наименование проверяемого параметра
     * @param int $min
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function min(string $name, int $param, callable|null $function = null):static
    {
        $this->add($name, new valid_min($param), $function);
        return $this;
    }

    /**
     * Значение не должно быть более указанного
     * @param string $name - наименование проверяемого параметра
     * @param int $max
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function max(string $name, int $param, callable|null $function = null):static
    {
        $this->add($name, new valid_max($param), $function);
        return $this;
    }

    // other

    /**
     * Значение будет преобразовано в булевое значение
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function bool(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_bool(), $function);
        return $this;
    }

    /**
     * Проверяет токен csrf 
     * @param string $name - наименование проверяемого параметра
     * @param string $param имя токена
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function csrf(string $name, string $param, callable|null $function = null):static
    {
        $this->add($name, new valid_csrf($param), $function);
        return $this;
    }
    
    /**
     * Значение проверяется на соответствие правилам написания почты
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function email(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_email(), $function);
        return $this;
    }

    /**
     * Проверяет на "пустоту" значения
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function required(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_empty(), $function);
        return $this;
    }  

    // text
    
    /**
     * Значение может содержать только латинские символы и цифры
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function latInt(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_latInt(), $function);
        return $this;
    }

    /**
     * Значение может содержать только латинские, кириллические символы и цифры
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function latRuInt(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_latRuInt(), $function);
        return $this;
    }

    /**
     * Значение может содержать только киреллические символы
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function ru(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_ru(), $function);
        return $this;
    }

    /**
     * Значение преобразует символы в html сущности функцией htmlspecialchars
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function text(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_text(), $function);
        return $this;
    }

    /**
     * Длина строки
     * @param string $name - наименование проверяемого параметра
     * @param string|int $strlen
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function strlen(string $name, int $param, callable|null $function = null):static
    {
        $this->add($name, new valid_strlen($param), $function);
        return $this;
    } 

    /**
     * Минимальная длина строки
     * @param string $name - наименование проверяемого параметра
     * @param int|string $strlen
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function strlenMin(string $name, int $param, callable|null $function = null):static
    {
        $this->add($name, new valid_strlenMin($param), $function);
        return $this;
    }   
    
    /**
     * Максимальная длина строки
     * @param string $name - наименование проверяемого параметра
     * @param string|int $strlen
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function strlenMax(string $name, int $param, callable|null $function = null):static
    {
        $this->add($name, new valid_strlenMax($param), $function);
        return $this;
    } 

    // datetame

    /**
     * Значение проверяется на соответствие написания даты 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function date(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_date(), $function);
        return $this;
    }

    /**
     * Значение проверяется на соответствие написания времени 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */      
    public function time(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_time(), $function);
        return $this;
    }    
    
    /**
     * Значение проверяется на соответствие написания параметра datatime 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */  
    public function datetime(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_datetime(), $function);
        return $this;
    }  
    
    // database

    /**
     * Проверка на уникальность записи в базе данных
     * @param string $name - наименование проверяемого параметра
     * @param string $table Имя таблицы
     * @param string $col Имя стобца
     * @param int $id id исключение (0 если не требуется)
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function unique(string $name, string $table, string $col, int $id = 0, callable|null $function = null):static
    {
        $this->add($name, new valid_unique($table, $col, $id), $function);
        return $this;
    }
    
    /**
     * Проверка на наличие записи в базе данных
     * @param string $name - наименование проверяемого параметра
     * @param string $table Имя таблицы
     * @param string $col Имя стобца
     * @param int $id id исключение (0 если не требуется)
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function isset(string $name, string $table, string $col = 'id', callable|null $function = null):static
    {
        $this->add($name, new valid_isset($table, $col), $function);
        return $this;
    } 
    
    // to
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function toFloat(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_toFloat(), $function);
        return $this;
    }   
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function toInt(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_toInt(), $function);
        return $this;
    } 
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function toString(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_toString(), $function);
        return $this;
    } 
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function toNull(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_toNull(), $function);
        return $this;
    } 
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function inn(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_inn(), $function);
        return $this;
    }      
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function bik(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_bik(), $function);
        return $this;
    }      
        
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function kpp(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_kpp(), $function);
        return $this;
    }  
            
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function ogrn(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_ogrn(), $function);
        return $this;
    }
            
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return static
     */    
    public function schet(string $name, callable|null $function = null):static
    {
        $this->add($name, new valid_schet(), $function);
        return $this;
    }    
}