<?php

declare(strict_types=1);

use App\Application\Health\CheckHealthService;
use App\Http\Controller\HealthController;
use App\Http\Response\JsonResponder;
use App\Http\Routing\Router;
use App\Infrastructure\Logging\ErrorLogLogger;
use App\Shared\Container\Container;
use App\Shared\Contracts\LoggerInterface;

// Simple PSR-4 style autoloader for the App namespace
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/App/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});

$container = new Container();

// Shared services
$container->set(LoggerInterface::class, fn (): LoggerInterface => new ErrorLogLogger());
$container->set(JsonResponder::class, fn (): JsonResponder => new JsonResponder());
$container->set(Router::class, fn (Container $c): Router => new Router($c->get(JsonResponder::class)));

// Application services
$container->set(CheckHealthService::class, fn (): CheckHealthService => new CheckHealthService());

// Controllers (thin, resolved through the container)
$container->set(HealthController::class, fn (Container $c): HealthController => new HealthController(
    $c->get(CheckHealthService::class),
    $c->get(JsonResponder::class)
));

/** @var Router $router */
$router = $container->get(Router::class);
require __DIR__ . '/App/Http/Routing/routes.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = $_SERVER['REQUEST_URI'] ?? '/';
$cleanPath = parse_url($path, PHP_URL_PATH) ?: '/';

$router->dispatch($method, $cleanPath, $container);
