<?php

namespace system\core\user;

use system\core\validate\validate;
use system\core\user\bruteforce;
use system\core\system\header;
use system\core\app\app;
use system\inst\classes\functions;

class auth
{
    public $status;
    private $session_time;
    private $loginRegex = "/^[\s a-zA-Z0-9а-яА-ЯёЁ.,\(\)$@!?#=+\-_]+$/u";
    private $urlFailed = null;
    private $urlSuccess = null;
    private $login;
    private $email;
    private $pass;
    private $csrf = true;
    public $error;
    private string $cookieName;
    private string $cookieDomains;
    private string $cookiePath;

    public function __construct()
    {
        $app = app::app();
        $this->cookieName = $app->cookies->name;
        $this->cookieDomains = $app->cookies->domains;
        $this->cookiePath = $app->cookies->path;
        $this->session_time = $app->cookies->time;
    }
    protected function setLogin($login)
    {
        $this->login = $login;
    }

    protected function setEmail($email)
    {
        $this->email = $email;
    }

    protected function setPass($pass)
    {
        $this->pass = $pass;
    }

    protected function setLoginRegex($regex)
    {
        $this->loginRegex = $regex;
    }

    protected function setCsrf(bool $status)
    {
        $this->csrf = $status;
    }

    public function setSessionTime(int $time)
    {
        $this->session_time = $time;
    }

    /**
     * @var  Вход пользователя 
     * 
     */
    protected function login($function = null): void
    {
        $app = app::app();
        $valid = new validate();
        $where = [];
        $bild = [];
        if ($this->email) {
            $valid->name('email', $this->email)->mail()->empty();
            $where[] = '`email` = :email';
            $bild['email'] = $valid->return('email');
        }
        if ($this->login) {
            $valid->name('login', $this->login)->free($this->loginRegex)->empty();
            $where[] = '`login` = :login';
            $bild['login'] = $valid->return('login');
        }

        $valid->name('password', $this->pass)->empty();

        if ($this->csrf) {
            $valid->name('csrf')->csrf('auth')->empty();
        }


        $bruteforce = new bruteforce();
        $bruteforce->addTry();

        $user = !empty($where) ? db()->fetch('SELECT * FROM `users` WHERE ' . implode(' AND ', $where), $bild) : null;
        
        if ($valid->control() && $user && password_verify($valid->return('password'), is_null($user->password) ? '' : $user->password) && $bruteforce->status()) {
            $bruteforce->resetTry();
            $passForCook = bin2hex(random_bytes(15)); //временный хеш сессии
            $date        = date('U'); // Дата сессии

            $param = [
                'user_id'     => $user->id,
                'session_key' => $passForCook,
                'active_time' => $date,
                'user_agent' => $app->bootstrap->user_agent,
                'ip' => $app->bootstrap->ip,
            ];

            db()->query('INSERT INTO `sessions` (`user_id`, `session_key`, `active_time`, `user_agent`, `ip`) VALUES (:user_id,  :session_key, :active_time, :user_agent, :ip)', $param);

            setcookie($this->cookieName, $passForCook, date('U') + $this->session_time(), $this->cookiePath, $this->cookieDomains);
            $_SESSION[$this->cookieName] = $passForCook;
            $this->status = $user->id;

            if ($function) {
                $function($this, $user, $valid);
            }
            if ($this->urlSuccess) {
                (new header())->location($this->urlSuccess);
            }
        } else {
            $this->status = 0;
            if ($function) {
                $function($this, $user, $valid);
            }
            if ($this->urlFailed) {
                (new header())->location($this->urlFailed);
            }
        }
    }

    /**
     * 
     * @var Выход пользователя
     */
    protected function out($function = null): void
    {
        db()->query('DELETE FROM `sessions` WHERE `session_key` = :session_key', ['session_key' => $_SESSION[$this->cookieName]]);
        $this->deleteSessionsAndCookies();      
        if ($function) {
            $function();
        }
        if ($this->urlFailed) {
            (new header())->location($this->urlFailed);
        } elseif ($this->urlSuccess) {
            (new header())->location($this->urlSuccess);
        } else {
            (new header())->location('/');
        }
    }

    /**
     * Авторизация пользователя по конкретному ключу
     * @param string $key
     * @return void
     */
    public function authUserSystem(string $key)
    {
        setcookie($this->cookieName, $key, date('U') + 60 * 60, $this->cookiePath, $this->cookieDomains);
        $_SESSION[$this->cookieName] = $key;
        // dd(123456);
        redirect('/');
    }

    // возвращает id пользователя или 0 если не зарегистрирован
    protected function status(): string|int
    {
        $session = isset($_SESSION[$this->cookieName]) ? $_SESSION[$this->cookieName] : null;
        $coockie = isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : null;
        $result = 0;
        if (isset($session) && isset($coockie) && $session == $coockie) {
            //Если есть и сессия, и куки 
            //Проверяем актуальность кук и сессии
            $ses = db()->fetch('SELECT * FROM `sessions` WHERE `session_key` = :session_key', ['session_key' => $coockie]);
            if (isset($ses->user_id) && $this->sanitary($ses)) {
                
                //При активности пользователя, продлеваем сессию
                // if($ses->active_time < (time()-(60*60)) ){
                    db()->query('UPDATE `sessions` SET `active_time` = :active_time WHERE `id` = ' . $ses->id, ['active_time' => time()]);
                    @setcookie($this->cookieName, $ses->session_key, date('U') + $this->session_time(), $this->cookiePath, $this->cookieDomains);
                    $result = $ses->user_id; // Актуальная сессия                    
                // }

                $this->delOldSes($ses->user_id);
            } else {
                $this->error = 'Сессия завершенна';
            }
        } elseif (isset($_COOKIE[$this->cookieName])) {
            //Если есть только куки
            //Проверяем актуальность кук,
            $ses = db()->fetch('SELECT * FROM `sessions` WHERE `session_key` = :session_key', ['session_key' => $coockie]);
            if (isset($ses->session_key)  && $this->sanitary($ses)) {
                //востанавливаем сессию, 
                $_SESSION[$this->cookieName] = $ses->session_key;
                $result = $ses->user_id; // Востановленная сессия
                //Обновляем дату
                db()->query('UPDATE `sessions` SET `active_time` =  :active_time WHERE `id` = :id', ['active_time' => date('U'), 'id' =>  $ses->id]);
                $this->delOldSes($ses->user_id);
            } else {
                $this->error = 'Востановить ссесию невозможно';
            }
        } else {
            $this->error = 'Требуется авторизация';
        }
        
        $this->status = $result;
        $app = app::app();
        $user = db()->fetch('SELECT * FROM `users` WHERE id = ' . $result);
        
        if ($result > 0 && $user) {
            foreach ($user as $a => $i) {
                if($a == 'password'){
                    continue;
                }
                $app->user->{$a} = $i;
            }
        } else {
            $app->user->id = 0;
        }
        
        return $result;
    }

    private function delOldSes($user_id = null): void
    {
        //проверяем актуальность всех сессий
        db()->query('DELETE FROM `sessions` WHERE `active_time` < :active_time', ['active_time' => $this->session_time]);

        //Разрешаем одному пользователю только одну сессию.
        // if ($user_id) {
        //     $data = [
        //         'session_key' => $_SESSION['us'],
        //         'user_id'     => $user_id
        //     ];
        //     db()->query('DELETE FROM `sessions` WHERE `session_key` != :session_key AND `user_id` = :user_id', $data);
        // }
    }

    protected function sanitary($ses)
    {
        $app = app::app();
        if ($ses->user_agent && $ses->user_agent == $app->bootstrap->user_agent) {
            return true;
        } elseif (is_null($ses->user_agent) && is_null($app->bootstrap->user_agent)) {
            return true;
        } else {
            return false;
        }
    }

    protected function redirectFailed(string $url)
    {
        $this->urlFailed = $url;
        return $this;
    }

    protected function redirectSuccess(string $url)
    {
        $this->urlSuccess = $url;
        return $this;
    }

    protected function redirect(string $url)
    {
        $this->urlSuccess = $url;
        $this->urlFailed = $url;
        return $this;
    }

    /**
     *Время жизни сессии
     */
    private function session_time()
    {
        $globalConfig = getConfig('globals', 'session_time');
        if ($globalConfig > 0) {
            return (int)$globalConfig;
        } else {
            return $this->session_time;
        }
    }

    public function deleteSessionsAndCookies()
    {
        unset($_SESSION[$this->cookieName]);
        unset($_SESSION['user']);
        setcookie($this->cookieName, '', 1, $this->cookiePath, $this->cookieDomains);
        setcookie($this->cookieName, '', 1, '/', $this->cookieDomains);
        setcookie($this->cookieName, '', 1, $this->cookiePath);
        setcookie($this->cookieName, '', 1, '/');
    }

    public static function __callStatic($method, $parameters)
    {
        if(method_exists(self::connect(), $method)){
            return self::connect()->$method(...$parameters);
        }
    }

    public function __call($method, $param)
    {
        if(method_exists($this, $method)){
            return $this->$method(...$param);
        }
    }
}
