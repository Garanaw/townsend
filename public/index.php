<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Foundation\Config\Config;
use App\Foundation\Container;
use App\Foundation\Database\DBConnection;
use App\Foundation\Database\MySqlConnection;
use App\Foundation\Router\Router;
use App\Foundation\Session\SessionStore;
use App\Http\Kernel;
use App\Foundation\Request\Request;

$app = new Container(dirname(__DIR__));
$config = new Config();
$session = $app->make(SessionStore::class);
$app->singleton(Kernel::class);
$app->singleton(Router::class);
$app->singleton(Request::class);
$app->instance(Config::class, $config);
$app->instance('config', $config);
$app->instance('session', $session);
$app->instance(SessionStore::class, $session);
$app->bind(DBConnection::class, MySqlConnection::class, true);

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$kernel->handle($request = Request::createFromGlobals())->send();
