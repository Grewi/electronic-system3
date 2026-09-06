<?php

namespace system\core\system;

use system\core\app\app;

class bot
{
    private int $time = BOT_TIME;
    private int $httpCode = BOT_RESPONSE_CODE;
    private string $fileName;
    private string $dirIp = BOT_DIR . '/ip';
    private string $stopWordsFile = BOT_DIR . '/stopWords.php';
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

        $this->clean();
        $this->fileName = $this->dirIp . '/' . $app->bootstrap->ip;
        $this->control();
        if (file_exists($this->stopWordsFile)) {
            $this->stopWordsList = array_merge($this->stopWordsList, include $this->stopWordsFile);
        }
        $this->valid();
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
        file_put_contents($this->fileName, $app->bootstrap->uri . PHP_EOL, FILE_APPEND);
        http_response_code($this->httpCode);
        echo '<h1>403 Forbidden</h1>' . PHP_EOL;
        echo 'Доступ запрещён! ';
        exit();
    }

    private function clean()
    {
        foreach (scandir($this->dirIp) as $file) {
            if ($file == '.' || $file == '..' || !file_exists($this->dirIp . $file)) {
                continue;
            }
            if (filectime($this->dirIp . $file) < (time() - ($this->time))) {
                unlink($this->dirIp . $file);
            }
        }
    }
}
