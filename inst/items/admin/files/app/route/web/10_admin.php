<?php

$route->namespace('{app}/controllers/admin')->prefix('admin')->group(ADMIN, function ($route) {
    
    $route->get('/')->controller('adminController', 'index');
});

$route->namespace('{app}/controllers/admin')->prefix('admin')->group('admin/users', function ($route) {
    $route->get('/')->controller('usersController', 'index');
});

$route->namespace('{app}/controllers/admin')->prefix('admin')->group('admin/user', function ($route) {
    $route->get('/create')->controller('usersController', 'create');
    $route->post('/create')->controller('usersController', 'createAction');
    $route->get('/edit/{user_id}')->controller('usersController', 'update');
    $route->post('/edit/{user_id}')->controller('usersController', 'updateAction');
    $route->get('/delete/{user_id}')->controller('usersController', 'delete');
    $route->post('/delete/{user_id}')->controller('usersController', 'deleteAction');
});

$route->namespace('{app}/controllers/admin')->prefix('admin')->group('admin/roles', function($route){
    $route->get('/')->controller('userRoleController', 'index');
});
