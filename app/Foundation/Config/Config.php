<?php declare(strict_types = 1);

namespace App\Foundation\Config;

use App\Foundation\Container;
use Dotenv\Dotenv;
use Illuminate\Contracts\Config\Repository;

class Config implements Repository
{
    private array $config;

    public function __construct()
    {
        $env = Dotenv::createImmutable(Container::getInstance()->basePath());
        $this->config = $env->load();
    }

    public function has($key): bool
    {
        return array_key_exists($key, $this->config);
    }

    public function get($key, $default = null): mixed
    {
        return $this->config[$key];
    }

    public function all(): array
    {
        return $this->config;
    }

    public function set($key, $value = null)
    {
        $this->config[$key] = $value;
    }

    public function prepend($key, $value)
    {
        $this->push($key, $value);
    }

    public function push($key, $value)
    {
        $this->config[$key] = $value;
    }
}
