<?php
// Without a container, you would need to write code like this every time: like "create a new PDO" and etc.

namespace Core\Container;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

class Container
{
    /** @var array<string, callable(self): mixed> */
    private array $bindings = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function (self $container) use ($abstract, $factory): mixed {
            if (!array_key_exists($abstract, $this->instances)) {
                $this->instances[$abstract] = $factory($container);
            }

            return $this->instances[$abstract];
        };
    }

    public function get(string $abstract): mixed
    {
        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])($this);
        }

        return $this->resolve($abstract);
    }

    public function resolve(string $class): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $this->resolveParameter($parameter);
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            throw new RuntimeException('Unresolvable constructor parameter: ' . $parameter->getName());
        }

        return $this->get($type->getName());
    }
}
