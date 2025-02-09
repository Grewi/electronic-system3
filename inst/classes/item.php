<?php
namespace system\inst\classes;

class item
{
    public $name;
    public $app;
    public $params = [];

    public $pathFiles;
    public $pathHelp;
    public $pathInfo;
    public $pathParams;
    public $pathRelations;
    public $pathIndex;
    public $pathClass;
    

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}