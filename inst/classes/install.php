<?php 
namespace system\inst\classes;
use system\inst\classes\item;

class install
{
    public string $param;
    public array $params = [];

    public function getParams(string $param)
    {
        if(isset($this->params[$param])){
            return $this->params[$param];
        }else{
            return null;
        }
    }

    public function setParams(string $key, mixed $value)
    {
        $this->params[$key] = $value;
    }
}