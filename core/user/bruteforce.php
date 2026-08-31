<?php

namespace system\core\user;

class bruteforce
{
    private int $firstTry;
    private int  $firstTime;
    private int $timeTry;
    private string $sessionName;
    private int $timeOutTry;

    /**
     * @param int $firstTry  - Количество попыток
     * @param int $firstTime - Время блокировки
     * @param int $timeTry   - Максимальное время между запросами, иначе сброс счётчика
     * @param string $sessionName 
     */
    public function __construct(int $firstTry = 5, int $firstTime = 60, int $timeTry = 60, string $sessionName = 'bruteforce')
    {
        $this->firstTry = $firstTry;
        $this->firstTime = $firstTime;
        $this->timeTry = $timeTry;
        $this->sessionName = $sessionName;
        $this->timeOutTry = time();
    }

    /**
     * //Регистрация попытки
     * @return void 
     */
    public function addTry(): void
    {
        if (time() - $this->timeOutTry > $this->timeTry) {
            $this->resetTry();
            return;
        }
        $try = isset($_SESSION[$this->sessionName]['count']) ? $_SESSION[$this->sessionName]['count'] : 0;
        $_SESSION[$this->sessionName]['count'] = ++$try;
        if ($this->remain() < 1) {
            $this->resetTry();
            $this->blocking();
        }
    }

    /**

     * @return void 
     */
    public function resetTry(): void
    {
        $_SESSION[$this->sessionName]['count'] = 0;
    }

    /**
     * //Остаток попыток
     * @return int 
     */
    public function remain(): int
    {
        $i = $this->firstTry - $_SESSION[$this->sessionName]['count'];
        return $i < 1 ? 0 : $i;
    }

    /**
     * //Блокировка
     * @return void 
     */
    public function blocking(): void
    {
        $_SESSION[$this->sessionName]['block']['status'] = true;
        $_SESSION[$this->sessionName]['block']['time'] = time();
    }

    /**
     * //Статус
     * @return bool 
     */
    public function status(): bool
    {
        if (isset($_SESSION[$this->sessionName]['block'])) {
            if ($this->timeBlocked() > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * //Остаток времени блокировки
     * @return int 
     */
    public function timeBlocked(): int
    {
        if (isset($_SESSION[$this->sessionName]['block']['time'])) {
            $i = $_SESSION[$this->sessionName]['block']['time'] + $this->firstTime;
            if ($i < time()) {
                unset($_SESSION[$this->sessionName]['block']);
                return 0;
            } else {
                return $i - time();
            }
        } else {
            return 0;
        }
    }
}

