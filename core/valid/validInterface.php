<?php

namespace system\core\valid;

interface validInterface 
{
    public function setControl(bool $status):static;
    public function getControl():bool;
    public function setError(string $status):static;
    public function getErrors():array;    
    public function getError():string;    
}