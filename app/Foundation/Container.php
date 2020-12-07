<?php declare(strict_types = 1);

namespace App\Foundation;

use Closure;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Throwable;

class Container
{
    private static self $instance;

    private string $basePath;
    private array $bindings = [];
    private array $resolved = [];
    private array $instances = [];
    private array $with = [];
    private array $buildStack = [];

    public static function getInstance(): self
    {
        return static::$instance;
    }

    public function __construct($basePath)
    {
        $this->registerSelf();
        $this->basePath = rtrim($basePath, '\/');
    }

    private function registerSelf()
    {
        static::$instance = $this;
        $this->instances['app'] = $this;
        $this->instances[static::class] = $this;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function bind(string $abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    public function make(string $abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

    public function resolve($abstract, array $parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $this->with[] = $parameters;

        $concrete = $this->getConcrete($abstract);

        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        $this->resolved[$abstract] = true;

        array_pop($this->with);

        return $object;
    }

    protected function getConcrete($abstract)
    {
        if (!is_string($abstract)) {
            return $abstract;
        }
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    protected function isBuildable($concrete, $abstract): bool
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    protected function build($concrete)
    {
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new RuntimeException("Target class [$concrete] does not exist.", 0, $e);
        }

        $this->buildStack[] = $concrete;

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            array_pop($this->buildStack);
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        try {
            $instances = $this->resolveDependencies($dependencies);
        } catch (Throwable $e) {
            array_pop($this->buildStack);
            throw $e;
        }

        array_pop($this->buildStack);

        return $reflector->newInstanceArgs($instances);
    }

    protected function resolveDependencies($dependencies): array
    {
        return collect($dependencies)
            ->map(function ($dependency) {
                if (!$type = $dependency->getType()) {
                    return null;
                }
                return $this->resolve($type->getName());
            })
            ->filter()
            ->all();
    }

    public function isShared($abstract): bool
    {
        return isset($this->instances[$abstract]) ||
            (isset($this->bindings[$abstract]['shared']) &&
                $this->bindings[$abstract]['shared'] === true);
    }
}
