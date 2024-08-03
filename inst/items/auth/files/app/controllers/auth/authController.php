<?php

namespace {app}\controllers\auth;
use {app}\models\users; 
use {app}\controllers\controller;
use electronic\core\view\view;
use system\core\app\app;
class authController extends controller
{
    public function index()
    {
        $this->title('Вход');
        $this->data['app'] = app::app();
        new view('auth/auth/index', $this->data);
    } 

    public function auth()
    {
        $login = new \system\core\user\auth();
        $login->setPass($_POST['pass']);
        // $login->setEmail($_POST['email']);
        $login->setLogin($_POST['login']);
        $login->redirect('/auth');
        $login->login(function($auth, $user, $valid){
            if($user && $valid->control() && $auth->status){
                alert('Добро пожаловать!', 'success');
                redirect('/auth');
            }else{
                alert('Ошибка авторизации!', 'danger');
                redirect(referal_url(), $valid->data(), array_merge($valid->error(), ['auth' => 'Ошибка авторизации!']));
            }
        });
    }

    public function out()
    {
        $login = new \system\core\user\auth();
        $login->redirect('/auth');
        $login->out();        
    }
}