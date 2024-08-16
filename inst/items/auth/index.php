<?php 
namespace system\inst\items\auth;
use system\core\app\app;
use system\inst\classes\itemIndex;

class index implements itemIndex
{
    public function params() : void
    {
        $app = app::app();
        $app->item->params->admin_pass =  password_hash($app->item->params->admin_pass, PASSWORD_DEFAULT);
    }

    public function files() : void
    {

    }

    public function database() :void
    {

    }

    public function finish() :void
    {

    }    
}