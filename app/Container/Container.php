<?php

declare(strict_types=1);

namespace App\Container;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    /** @var array<string, Closure|string> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $shared = [];

    /** @var array<string, true> */
    private array $resolving = [];

    public function bind(string $abstract, Closure|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->shared[$abstract] = $instance;
    }

    /**
     * @template T of object
     * @param class-string<T>|string $abstract
     * @return T|object
     */
    public function get(string $abstract): object
    {
        if (isset($this->shared[$abstract])) {
            return $this->shared[$abstract];
        }

        if (isset($this->resolving[$abstract])) {
            throw new RuntimeException("Circular dependency detected for {$abstract}");
        }
        $this->resolving[$abstract] = true;

        try {
            $binding = $this->bindings[$abstract] ?? $abstract;

            $object = $binding instanceof Closure
                ? $binding($this)
                : $this->build($binding);

            $this->shared[$abstract] = $object;
            return $object;
        } finally {
            unset($this->resolving[$abstract]);
        }
    }

    /**
     * @param class-string $class
     */
    public function make(string $class): object
    {
        return $this->build($class);
    }

    /**
     * @param class-string $class
     */
    private function build(string $class): object
    {
        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Class {$class} is not instantiable");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $class();
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = $this->get($type->getName());
                continue;
            }
            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }
            throw new RuntimeException(
                "Cannot resolve parameter \${$param->getName()} of {$class}"
            );
        }

        return $reflection->newInstanceArgs($args);
    }
}