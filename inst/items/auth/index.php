<?php 
namespace system\inst\items\auth;
use system\core\app\app;

class index
{
    public function param()
    {
        $app = app::app();
        $app->item->params->admin_pass =  password_hash($app->item->params->admin_pass, PASSWORD_DEFAULT);
    }
}