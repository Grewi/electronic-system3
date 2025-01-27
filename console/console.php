<?php

use system\core\route\route;
use system\console\updateSystem;
use system\console\addComplement;
use system\console\createController;
use system\console\createModel;
use system\console\migrate;
use system\console\clean;
use system\console\database;
use system\console\createConfig;
use system\console\createConfigIni;
use system\console\symlink;
use system\console\config;
use system\console\createSymlink;
use system\console\sass;
use system\console\help;
$route = new route();

$route->console('update/system')->controller(updateSystem::class, 'index');
$route->console('add/complement')->controller(addComplement::class, 'index');

$route->console('create/controller')->controller(createController::class, 'index');
$route->console('create/model')->controller(createModel::class, 'index');

$route->console('migrate')->controller(migrate::class, 'index');
$route->console('create/migration')->controller(migrate::class, 'createMigration');

$route->console('clean')->controller(clean::class, 'index');
$route->console('clean/cache')->controller(clean::class, 'cleanCache');

$route->console('create/dump')->controller(database::class, 'createDump');
$route->console('restore/dump')->controller(database::class, 'restoreDump');
$route->console('drop/tables')->controller(database::class, 'dropTables');

//Config
$route->console('create/config')->controller(createConfig::class, 'index');
$route->console('create/config/ini')->controller(createConfigIni::class, 'index');
$route->console('clean/config')->controller(clean::class, 'cleanConfig');

$route->console('symlink')->controller(symlink::class, 'index');
$route->console('config')->controller(config::class, 'actual');

$route->console('create/symlink')->controller(createSymlink::class, 'index');

$route->console('style')->controller(sass::class, 'compile');
$route->console('style/info')->controller(sass::class, 'info');

$route->console('help')->controller(help::class, 'index');
