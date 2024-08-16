<?php 

namespace {app}\configs;
!INDEX ? exit('exit') : true;

class mail
{

    public function set() : array
    {
        return [
            'host'         => '',
            'port'         => '',
            'userName'     => '',
            'password'     => '',
            'fromEmail'    => '',
            'fromName'     => '',
            'replyTo'      => '',
            'logs'         => '1',
        ];
    }
}