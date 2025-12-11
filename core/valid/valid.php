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
use system\core\valid\bisness\valid_bik;
use system\core\valid\bisness\valid_kpp;
use system\core\valid\bisness\valid_ogrn;
use system\core\valid\bisness\valid_schet;

class valid
{
    private bool $control = true;

    /**
     * control
     * errors
     * original
     * result
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

        if (isset($this->original[$name])) {  
            $item->setOriginal($this->original[$name]);
        }
        if ($function) {
            $function($item);
        }
        $item->control();
        $this->data[$name]['control'] = $item->getControl();
        $this->data[$name]['errors'] = $item->getErrors();
        $this->data[$name]['original'] = $item->getOriginal();
        if ($item->getResult || !isset($this->data[$name])) {
            $this->data[$name]['result'] = $item->getResult();
        }

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
            if (isset($i['original'])) {
                $original[$a] = $i['original'];
            }
        }
        return $original;
    }

    public function results(): array
    {
        $original = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['result'])) {
                $original[$a] = $i['result'];
            }
        }
        return $original;
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
     * @return void
     */
    public function int(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_int(), $function);
    }

    /**
     * Значение должно быть дробным числом
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function float(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_float(), $function);
    }  
    
    /**
     * Значение не должно быть менее указаного
     * @param string $name - наименование проверяемого параметра
     * @param int $min
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function min(string $name, int $param, callable|null $function = null):void
    {
        $this->add($name, new valid_min($param), $function);
    }

    /**
     * Значение не должно быть более указанного
     * @param string $name - наименование проверяемого параметра
     * @param int $max
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function max(string $name, int $param, callable|null $function = null):void
    {
        $this->add($name, new valid_max($param), $function);
    }

    // other

    /**
     * Значение будет преобразовано в булевое значение
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function bool(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_bool(), $function);
    }

    /**
     * Проверяет токен csrf 
     * @param string $name - наименование проверяемого параметра
     * @param string $param имя токена
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function csrf(string $name, string $param, callable|null $function = null):void
    {
        $this->add($name, new valid_csrf($param), $function);
    }
    
    /**
     * Значение проверяется на соответствие правилам написания почты
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function email(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_email(), $function);
    }

    /**
     * Проверяет на "пустоту" значения
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function required(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_empty(), $function);
    }  

    // text
    
    /**
     * Значение может содержать только латинские символы и цифры
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function latInt(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_latInt(), $function);
    }

    /**
     * Значение может содержать только латинские, кириллические символы и цифры
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function latRuInt(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_latRuInt(), $function);
    }

    /**
     * Значение может содержать только киреллические символы
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function ru(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_ru(), $function);
    }

    /**
     * Значение преобразует символы в html сущности функцией htmlspecialchars
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function text(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_text(), $function);
    }

    /**
     * Длина строки
     * @param string $name - наименование проверяемого параметра
     * @param string|int $strlen
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function strlen(string $name, int $param, callable|null $function = null):void
    {
        $this->add($name, new valid_strlen($param), $function);
    } 

    /**
     * Минимальная длина строки
     * @param string $name - наименование проверяемого параметра
     * @param int|string $strlen
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function strlenMin(string $name, int $param, callable|null $function = null):void
    {
        $this->add($name, new valid_strlenMin($param), $function);
    }   
    
    /**
     * Максимальная длина строки
     * @param string $name - наименование проверяемого параметра
     * @param string|int $strlen
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function strlenMax(string $name, int $param, callable|null $function = null):void
    {
        $this->add($name, new valid_strlenMax($param), $function);
    } 

    // datetame

    /**
     * Значение проверяется на соответствие написания даты 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function date(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_date(), $function);
    }

    /**
     * Значение проверяется на соответствие написания времени 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */      
    public function time(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_time(), $function);
    }    
    
    /**
     * Значение проверяется на соответствие написания параметра datatime 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */  
    public function datetime(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_datetime(), $function);
    }  
    
    // database

    /**
     * Проверка на уникальность записи в базе данных
     * @param string $name - наименование проверяемого параметра
     * @param string $table Имя таблицы
     * @param string $col Имя стобца
     * @param int $id id исключение (0 если не требуется)
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function unique(string $name, string $table, string $col, int $id = 0, callable|null $function = null):void
    {
        $this->add($name, new valid_unique($table, $col, $id), $function);
    }
    
    /**
     * Проверка на наличие записи в базе данных
     * @param string $name - наименование проверяемого параметра
     * @param string $table Имя таблицы
     * @param string $col Имя стобца
     * @param int $id id исключение (0 если не требуется)
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function isset(string $name, string $table, string $col = 'id', callable|null $function = null):void
    {
        $this->add($name, new valid_isset($table, $col), $function);
    } 
    
    // to
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function toFloat(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_toFloat(), $function);
    }   
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function toInt(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_toInt(), $function);
    } 
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function toString(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_toString(), $function);
    } 
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function toNull(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_toNull(), $function);
    }     
    
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function bik(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_bik(), $function);
    }      
        
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function kpp(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_kpp(), $function);
    }  
            
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function ogrn(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_ogrn(), $function);
    }
            
    /**
     * 
     * @param string $name - наименование проверяемого параметра
     * @param callable|null $function(item $item)
     * @return void
     */    
    public function schet(string $name, callable|null $function = null):void
    {
        $this->add($name, new valid_schet(), $function);
    }    
}