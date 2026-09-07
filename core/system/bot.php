<?php

namespace system\core\system;

use system\core\app\app;

class bot
{
    private int $time = BOT_TIME;
    private int $httpCode = BOT_RESPONSE_CODE;
    private string $fileName;
    private string $secret;
    private string $dirIp = BOT_DIR . '/ip';
    private string $dirUnlocking = BOT_DIR . '/unlocking';
    private string $stopWordsFile = BOT_DIR . '/files/stopWords.php';
    private string $infoFile = BOT_DIR . '/files/infoFile.php';
    private array $stopWordsList = [
        '@fs',
        '.env',
        '.aws',
        '.git',
        '.sql',
        '.swp',
        '.ssh',
        'VALUE(',
        'CONCAT(',
        '(SELECT',
        'ELT(',
        'wp-config',
        'wp-login',
        'wp-json',
        'wordpress',
        'backup.tar',
        'backup.zip',
        'backup.sql',
        'dump.sql',
        'actuator',
        '_ignition',
        'credentials.json',
        'secrets.json',
        'Dockerfile',
        'docker-compose',
        'composer',
        'appsettings',
        'values.yaml',
        'settings.json',
        'var/www',
        'htdocs',
        'laravel',
        'rest_route',
    ];

    public function __construct()
    {
        $app = app::app();
        if (!file_exists(BOT_DIR)) {
            createDir(BOT_DIR);
        }
        if (!file_exists($this->dirIp)) {
            createDir($this->dirIp);
        }
        $this->fileName = $this->dirIp . '/' . $app->bootstrap->ip;
        $this->secret = md5($app->bootstrap->ip . BOT_SECRET . date('YmdH'));
        $_SESSION['bot_session'] = $this->secret;
        $this->clean($this->dirIp, $this->time);
        $this->unlocking();
        $this->control();
        if (file_exists($this->stopWordsFile)) {
            $this->stopWordsList = array_merge($this->stopWordsList, include $this->stopWordsFile);
        }
        $this->valid();
    }

    private function unlocking()
    {
        $app = app::app();
        if ('/' . $_SESSION['bot_session'] != $app->bootstrap->uri) {
            return;
        }
        if (!file_exists($this->dirUnlocking)) {
            createDir($this->dirUnlocking);
        }
        $this->clean($this->dirUnlocking, $this->time);
        $f = $this->dirUnlocking . '/' . $app->bootstrap->ip;
        if (file_exists($f)) {
            return;
        }
        unlink($this->fileName);
        file_put_contents($f, date('Y-m-d H:i') . PHP_EOL, FILE_APPEND);
        redirect('/');
    }

    private function control()
    {
        if (file_exists($this->fileName)) {
            $this->stop();
        }
    }

    private function valid()
    {
        $app = app::app();
        foreach ($this->stopWordsList as $i) {
            if (stripos($app->bootstrap->uri, $i) !== false) {
                $this->stop();
            }
        }
    }

    private function stop()
    {
        $app = app::app();
        file_put_contents($this->fileName, date('Y-m-d H:i') . ' ' . $app->bootstrap->url . $app->bootstrap->uri . PHP_EOL, FILE_APPEND);
        http_response_code($this->httpCode);
        if (file_exists($this->infoFile)) {
            include $this->infoFile;
        } else {
            echo '<h1>403 Forbidden</h1>' . PHP_EOL;
            echo 'Доступ запрещён! ';
        }
        exit();
    }

    private function clean(string $dir, int $time)
    {
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..' || !file_exists($dir . $file)) {
                continue;
            }
            if (filectime($dir . $file) < (time() - ($time))) {
                unlink($dir . $file);
            }
        }
    }
}
