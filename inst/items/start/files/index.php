<?php
session_start();
require_once __DIR__ . '/system/core/domains/route.php';
$domain = \system\core\domains\route::connect();
$domain->addApp('localhost', '{app}');
$domain->addApp('electronic', '{app}');
$domain->addApp('electronic.ru', '{app}');
$domain->addDefault('{app}');
define('ENTRANSE', 'web');
define('APP_NAME', $domain->app());
define('ARGV', null);
require_once __DIR__ . '/consts.php';
