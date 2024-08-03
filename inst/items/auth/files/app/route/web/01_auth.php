<?php

$route->namespace('{app}/controllers/auth');
$route->get('/register')->controller('registerController', 'index');
$route->post('/register')->controller('registerController', 'register');

$route->get('/auth')->controller('authController', 'index');
$route->post('/auth')->controller('authController', 'auth'); 
$route->all('/exit')->controller('authController', 'out'); 