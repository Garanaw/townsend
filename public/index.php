<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Foundation\Container;
use App\Foundation\Router\Router;
use App\Http\Kernel;
use Symfony\Component\HttpFoundation\Request;

$app = new Container(dirname(__DIR__));
$app->singleton(Kernel::class);
$app->singleton(Router::class);
$app->singleton(Request::class);

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$kernel->handle($request = Request::createFromGlobals())->send();

//$router->dispatch($request)->send();

//var_dump($router);
