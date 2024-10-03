<?php 
namespace {namespace}\prefix;
use db\models\users;
use db\models\user_role;
use {namespace}\controllers\error\error;

class admin
{
    public function index()
    {
        $adminRole = user_role::where('slug', 'admin')->get();
        if(user_id()){
            $user = users::find(user_id());
            if($user->user_role_id != $adminRole->id){
                return false;
            }
        }else{
            return false;
        }
    }
}