<?php

use App\Foundation\Container;
use App\Foundation\Router\Router;
use App\Http\Controllers\Home;
use App\Http\Controllers\ProcessContact;
use App\Http\Controllers\ShowContact;

/** @var Router $router */
$router = Container::getInstance()->make(Router::class);

$router->get('/', Home::class);
$router->get('/contact', ShowContact::class);

$router->post('/contact', ProcessContact::class);
