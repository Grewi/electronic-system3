<?php 
use system\core\history\history;
use system\core\bootstrap\bootstrap;
use system\core\error\errorPhp;
use system\core\app\app;

ob_start();

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
    $app = app::app();
    $app->set('bootstrap');
    $app->set('controller');
    $app->set('views');
    $app->set('view');
    $app->set('request');
    $app->set('getparams');
    $app->set('user');
    $app->set('route');
    $app->set('time');
    $app->set('memory');
    $app->set('cookies');
    if (file_exists($composer)) {
        require_once $composer;
    }

    if (ENTRANSE == 'web') {
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