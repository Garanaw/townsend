<?php declare(strict_types = 1);

namespace App\Foundation\Router;

use App\Foundation\Container;
use App\Foundation\Request\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
            return $this->prepareResponse($this->runRoute($request, function () {
                return '404.php';
            }));
        }

        $controller = $this->container->make($route['action']);

        return $this->prepareResponse($this->runRoute($request, $controller));
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
        return null;
    }

    private function runRoute(Request $request, callable $action)
    {
        $response = $action();
        if (is_string($response) && strpos($response, '.php')) {
            $response = $this->renderResponse($response);
        }

        return $response;
    }

    private function renderResponse(string $path): string
    {
        $obLevel = ob_get_level();

        ob_start();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            require $this->container->basePath() . '/app/Views/' . $path;
        } catch (Throwable $e) {
//            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    private function prepareResponse($content)
    {
        if ($content instanceof Response) {
            return $content;
        }
        return new Response($content);
    }
}
