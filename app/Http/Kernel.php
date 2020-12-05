<?php declare(strict_types = 1);

namespace App\Http;

use App\Foundation\Container;
use App\Foundation\Router\Router;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class Kernel
{
    private Container $container;
    private Router $router;
    private Request $request;

    public function __construct(Container $container, Router $router)
    {
        $this->container = $container;
        $this->router = $router->load();
    }

    public function handle(Request $request)
    {
        try {
            $request->enableHttpMethodParameterOverride();
            $this->request = $request;
            $this->container->bind('request', $request, true);

            $response = $this->router->dispatch($request);
        } catch (Throwable $e) {
            var_dump($e);
            die();
//            $this->reportException($e);
//
//            $response = $this->renderException($request, $e);
        }

        return $response;
    }

    protected function sendRequestThroughRouter($request)
    {

//        return (new Pipeline($this->container))
//            ->send($request)
//            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
//            ->then($this->dispatchToRouter());
    }
}
