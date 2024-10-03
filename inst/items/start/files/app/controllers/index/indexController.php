<?php

namespace {namespace}\controllers\index;

use {namespace}\controllers\controller;
use electronic\core\view\view;

class indexController extends controller
{

    public function index()
    {
        $this->title('Главная страница');
        new view('index/index', $this->data);
    }

}