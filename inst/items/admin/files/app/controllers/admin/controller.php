<?php
namespace {namespace}\controllers\admin;
use system\core\lang\lang;

class controller extends \{namespace}\controllers\controller
{
    protected function bc(string $name, string $url = '')
    {
        if(empty($this->data['bc'])){
            $this->data['bc'][] = ['name' => lang::admin('home'), 'url' => '/'];
            $this->data['bc'][] = ['name' => lang::admin('admin'), 'url' => '/' . ADMIN];
        }
        $this->data['bc'][] = ['name' => $name, 'url' => $url];
    }
}