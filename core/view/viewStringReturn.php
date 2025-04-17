<?php
namespace system\core\view;
use system\core\view\view;

class viewStringReturn
{
    private $content;

    public function __construct($file, $data)
    {
        ob_start();
        new view($file, $data);
        $this->content = ob_get_contents();
        ob_end_clean();
    }

    public function return() : string
    {
        return $this->content;
    }
}