<?php

namespace App\Shared\Container;

use Closure;
use RuntimeException;

class Container
{
    /** @var array<string, Closure> */
    private array $definitions = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    public function set(string $id, Closure $factory): void
    {
        $this->definitions[$id] = $factory;
    }

    public function get(string $id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->definitions)) {
            throw new RuntimeException("Service '{$id}' is not defined in the container");
        }

        $this->instances[$id] = $this->definitions[$id]($this);

        return $this->instances[$id];
    }
}
