<?php

use system\core\route\route;
use system\console\controller\createController;
use system\console\database\migrate;
use system\console\database\database;
use system\console\config\createConfig;
use system\console\config\createConfigIni;
use system\console\config\config;
use system\console\clear\clear;
use system\console\symlink\symlink;
use system\console\symlink\createSymlink;
use system\console\model\createModel;
use system\console\sass\sass;

use system\console\help;
use system\console\updateSystem;
use system\console\addComplement;

$route = new route();

$route->console('update/system')->controller(updateSystem::class, 'index');
$route->console('add/complement')->controller(addComplement::class, 'index');

$route->console('create/controller')->controller(createController::class, 'index');
$route->console('create/model')->controller(createModel::class, 'index');

$route->console('migrate')->controller(migrate::class, 'index');
$route->console('create/migration')->controller(migrate::class, 'createMigration');

$route->console('clean')->controller(clear::class, 'index');
$route->console('clean/cache')->controller(clear::class, 'cache');

$route->console('create/dump')->controller(database::class, 'createDump');
$route->console('restore/dump')->controller(database::class, 'restoreDump');
$route->console('drop/tables')->controller(database::class, 'dropTables');

//Config
$route->console('create/config')->controller(createConfig::class, 'index');
$route->console('create/config/ini')->controller(createConfigIni::class, 'index');
$route->console('clean/config')->controller(clear::class, 'config');

$route->console('symlink')->controller(symlink::class, 'index');
$route->console('create/symlink')->controller(createSymlink::class, 'index');

$route->console('config')->controller(config::class, 'actual');

$route->console('style')->controller(sass::class, 'compile');
$route->console('style/info')->controller(sass::class, 'info');

$route->console('help')->controller(help::class, 'index');
