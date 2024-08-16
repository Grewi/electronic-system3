<?php 
namespace electronic\lib\email;
use electronic\core\view\viewStringReturn;
trait mailLayout 
{
    private $data;

    public function layout(string $view, $data = null)
    {
        $this->data = $data;
        $this->body = (new viewStringReturn($view, $data))->return(); 
        return $this;          
    }
}