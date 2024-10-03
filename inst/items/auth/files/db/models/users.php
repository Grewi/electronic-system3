<?php 
namespace db\models;
use db\models\user_role;
use electronic\core\model\model;

class users extends model
{    
    protected function role()
    {
        return user_role::find($this->user_role_id);
    }

    public function toController()
    {
        return $this->find(user_id());
    }
}