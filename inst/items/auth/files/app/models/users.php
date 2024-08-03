<?php 
namespace {app}\models;
use {app}\models\user_role;
use electronic\core\model\model;

class users extends model
{    
    protected function role()
    {
        return user_role::find($this->user_role_id);
    }
}