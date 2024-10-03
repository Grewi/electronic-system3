<?php
define('INDEX', true);
define('ROOT', str_replace('\\', '/', __DIR__));
define('APP', ROOT . '/' . APP_NAME);
define('APP_NAMESPACE', str_replace('/', '\\', APP_NAME));
define('SYSTEM', ROOT . '/system');
define('MODELS', ROOT . '/db/models');
define('MIGRATIONS', ROOT . '/db/migrations');
define('E_PUBLIC', ROOT . '/public');
require_once SYSTEM . '/system.php';