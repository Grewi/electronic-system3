<?php

namespace system\core\view;

use system\core\view\view;

class viewJson
{
    private string $content;

    /**
     * @param string $file 
     * @param array $data 
     */
    public function __construct($file, $data)
    {
        ob_start();
        new view($file, $data);
        $this->content = ob_get_contents();
        ob_end_clean();
        echo json_encode($this->content);
    }
}

