<?php

namespace {app}\controllers\auth;
use {app}\models\users;
use {app}\models\user_role;
use {app}\controllers\controller;
use electronic\core\view\view;
use electronic\core\validate\validate;
use system\core\app\app;
class registerController extends controller
{
    public function index()
    {
        $this->title('Вход');
        new view('auth/register/index', $this->data);
    } 

    public function register(validate $valid, app $app)
    {
        // ban::cl();
        // if((!empty($_POST['login']) || !empty($_POST['email'])) || ban::status()){ 
        //     alert2('В бан!');
        //     ban::add();
        // }

        $valid->name('csrf')->csrf('register');
        $valid->name('name')->latRuInt()->empty()->strlenMin(3)->unique('users', 'login');
        $valid->name('email2')->mail()->empty()->unique('users', 'email');
        $valid->name('password')->empty()->strlenMin(5)->pass();       
        $valid->name('confirm_password')->confirmPass();
        dump($_POST);
        if($valid->control()){
            $userRole = user_role::where('slug', 'user')->get();
            $emailCode = rand(1000, 9999);
            $data = [
                'email' => $valid->return('email2'),
                'email_code' => $emailCode,
                'email_status' => 0,
                'password' => $valid->return('password'),
                'login' => $valid->return('name'),
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
            dd($valid);
            redirect(referal_url(), $valid->data(), $valid->error());
        } 
    }
}