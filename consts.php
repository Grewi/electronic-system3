<?php
if (!defined('ROOT')) {
    define('ROOT', str_replace('\\', '/', dirname(__DIR__)));
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'app');
}

if (!defined('APP')) {
    define('APP', ROOT . '/' . APP_NAME);
}

if (!defined('APP_NAMESPACE')) {
    define('APP_NAMESPACE', str_replace('/', '\\', APP_NAME));
}

if (!defined('SYSTEM_NAME')) {
    define('SYSTEM_NAME', 'system');
}

if (!defined('SYSTEM')) {
    define('SYSTEM', ROOT . '/' . SYSTEM_NAME);
}

if (!defined('MIGRATIONS')) {
    define('MIGRATIONS', ROOT . '/db/migrations');
}

if (!defined('MODELS')) {
    define('MODELS', APP . '/db/models');
}

if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'UTC');
}

if (!defined('ENTRY_POINT_WEB')) {
    define('ENTRY_POINT_WEB', APP . '/route/web.php');
}

if (!defined('ENTRY_POINT_CONSOLE')) {
    define('ENTRY_POINT_CONSOLE', APP . '/route/console.php');
}

if (!defined('ENTRY_POINT_CONSOLE_SYSTEM')) {
    define('ENTRY_POINT_CONSOLE_SYSTEM', SYSTEM . '/console/console.php');
}

if (!defined('ENTRY_POINT_CRON')) {
    define('ENTRY_POINT_CRON', APP . '/route/cron.php');
}