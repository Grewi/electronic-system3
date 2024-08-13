<?php 

declare(strict_types = 1);

namespace system\core\controller;
use system\core\config\config;
use system\core\collection\collection;
use system\core\lang\lang;

!INDEX ? exit('exit') : true;

abstract class controller
{
	
    protected $data;
    protected $return;
    protected $siteUrl;

    public function __construct()
    {
        $this->return = new collection();
        $this->title(config::globals('title'));
        $this->alert();
        $this->error();
        $this->data();
        $this->data['title'] = lang::global('title');
        $this->data['return'] = $this->return;
    }

    protected function bc(string $name, string $url = '')
    {
        if(empty($this->data['bc'])){
            $this->data['bc'][] = ['name' => lang::global('home'), 'url' => '/'];
        }
        $this->data['bc'][] = ['name' => $name, 'url' => $url];
    }

    protected function alert()
    {
        if ( isset($_SESSION['alert']) ) {
            $this->return->alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
        } else {
            $this->data['alert'] = [];
        }
    }

    protected function title(string $title = '')
    {
        $configTitle = config::globals('title');
        $sep = ' | ';
        if(!empty($title)){
            $this->data['title'] = $title . $sep . $configTitle;
        }else{
            $this->data['title'] = $configTitle;
        }
    }

    protected function return($data = null)
    {
        if(is_object($data)){
            foreach($data as $a => $i){
                if(empty( $this->data['return']->data->{$a})){
                    $this->data['return']->data->{$a} = $i;
                }
            }
        }
    }

    protected function error()
    {
        if (isset($_SESSION['error'])) {
            foreach ($_SESSION['error'] as $k => $i) {
                $this->return->error->{$k} = $i;
                $this->return->class->{$k} = 'is-invalid';
            }
            $_SESSION['unset'][] = 'error';
            // unset($_SESSION['error']);
        }
    }

    protected function data()
    {
        if (isset($_SESSION['data']) && is_iterable($_SESSION['data'])) {
            foreach ($_SESSION['data'] as $k => $i) {
                $this->return->data->{$k} = $i;
            }
            $_SESSION['unset'][] = 'data';
            // unset($_SESSION['data']);
        }
    }
}