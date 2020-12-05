<?php declare(strict_types = 1);

namespace App\Foundation\Router;

use App\Foundation\Container;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    private Container $container;
    private Request $request;
    private array $routes = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get($uri, $action = null): self
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action = null): self
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function addRoute($method, $uri, $action): self
    {
        $this->routes[$method][] = [
            'uri'    => $uri,
            'action' => $action,
        ];
        return $this;
    }

    public function load(): self
    {
        $file = $this->container->basePath() . '/app/routes/routes.php';
        if (!file_exists($file)) {
            throw new RuntimeException('Routes file not found');
        }
        require $file;
        return $this;
    }

    public function dispatch(Request $request)
    {
        $this->request = $request;
        if (!$route = $this->findRoute($request)) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $controller = $this->container->make($route['action']);

        return $this->runRoute($request, $controller);
    }

    private function findRoute(Request $request): ?array
    {
        if (!array_key_exists($request->getMethod(), $this->routes)) {
            throw new RuntimeException('Method not allowed', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        foreach ($this->routes[$request->getMethod()] as $route) {
            if ($route['uri'] === $request->getPathInfo()) {
                return $route;
            }
        }
    }

    private function runRoute(Request $request, callable $action)
    {
        
    }
}
