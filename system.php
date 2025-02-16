<?php 
use system\core\history\history;
use system\core\bootstrap\bootstrap;
use system\core\error\errorPhp;

ob_start();

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

// register_shutdown_function(function(){

// });

require_once __DIR__ . '/consts.php';

date_default_timezone_set(TIMEZONE);

require_once SYSTEM . '/exception/exception.php';

try {
    require_once SYSTEM . '/function.php';
    require_once SYSTEM . '/autoloader.php';
    errorPhp::config();

    $composer = ROOT . '/composer/vendor/autoload.php';
    if (file_exists($composer)) {
        require_once $composer;
    }

    if (ENTRANSE == 'web') {
        bootstrap::load();

        history::start()->save();
        require_once ENTRY_POINT_WEB;
    } elseif (ENTRANSE == 'console') {
        require_once ENTRY_POINT_CONSOLE_SYSTEM;
        require_once ENTRY_POINT_CONSOLE;
        exit('no controller ');
    } elseif (ENTRANSE == 'cron') {
        require_once ENTRY_POINT_CRON;
    }

} catch (Throwable $e) {
    exeptionVar::dump($e, $e->getMessage(), 0);
    exit();
}