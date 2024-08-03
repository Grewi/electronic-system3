<?php

namespace {app}\controllers\auth;
use {app}\models\users;
use {app}\models\user_role;
use {app}\controllers\controller;
use electronic\core\view\view;
use electronic\core\validate\validate;
use system\core\app\app;
class registrationController extends controller
{
    public function index()
    {
        $this->title('Вход');
        new view('auth/registration/index', $this->data);
    } 

    public function register()
    {
        $app = app::app();
        // ban::cl();
        // if((!empty($_POST['login']) || !empty($_POST['email'])) || ban::status()){ 
        //     alert2('В бан!');
        //     ban::add();
        // }

        $valid = new validate();
        $valid->name('csrf')->csrf('register');
        $valid->name('loginUser')->latRuInt()->empty()->strlenMin(3)->unique('users', 'login');
        $valid->name('emailUser')->mail()->empty()->unique('users', 'email');
        $valid->name('pass')->empty()->strlenMin(5)->pass();       
        $valid->name('confirm')->confirmPass();
        
        if($valid->control()){
            $userRole = user_role::where('slug', 'user')->get();
            $emailCode = rand(1000, 9999);
            $data = [
                'email' => $valid->return('emailUser'),
                'email_code' => $emailCode,
                'email_status' => 0,
                'password' => $valid->return('pass'),
                'login' => $valid->return('loginUser'),
                'active' => 1,
                'user_role_id' => $userRole->id,
            ];
            $user = users::insert($data);
            alert('Спасибо за регистрацию!', 'primary');

            $login = new \system\core\user\auth();
            $login->setCsrf(false);
            $login->setPass($_POST['pass']);
            $login->setEmail($user->email);
            $login->setLogin($user->login);
            $login->redirect(referal_url());
            $login->login(function($auth, $user, $valid){
                if($valid->control()){
                    redirect('/');
                }else{
                    redirect(referal_url(), $valid->data(), $valid->error());
                }
            });
        }else{
            redirect(referal_url(), $valid->data(), $valid->error());
        } 
    }
}