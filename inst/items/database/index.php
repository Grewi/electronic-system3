<?php 
namespace system\inst\items\database;

class index
{
    public function param($param)
    {
        $param['admin_pass'] =  password_hash($param['admin_pass'], PASSWORD_DEFAULT);
        return $param;
    }
}