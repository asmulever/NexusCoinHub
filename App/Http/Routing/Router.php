<?php

namespace App\Http\Routing;

use App\Shared\Container\Container;
use App\Http\Response\JsonResponder;
use RuntimeException;

class Router
{
    /** @var array<string, array<string, array{controller: string, action: string}>> */
    private array $routes = [];

    public function __construct(
        private ?JsonResponder $responder = null
    ) {
    }

    public function get(string $path, string $controllerAction): void
    {
        $this->addRoute('GET', $path, $controllerAction);
    }

    private function addRoute(string $method, string $path, string $controllerAction): void
    {
        [$controller, $action] = explode('@', $controllerAction, 2);

        $this->routes[$method][$path] = [
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function dispatch(string $method, string $path, Container $container): void
    {
        $path = rtrim($path, '/') ?: '/';

        if (!isset($this->routes[$method][$path])) {
            if ($this->responder) {
                $this->responder->respond(['error' => 'Not Found'], 404);
                return;
            }

            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
            return;
        }

        $route = $this->routes[$method][$path];
        $controller = $container->get($route['controller']);
        $action = $route['action'];

        if (!method_exists($controller, $action)) {
            throw new RuntimeException("Action '{$action}' not found in controller '{$route['controller']}'");
        }

        $controller->{$action}();
    }
}
