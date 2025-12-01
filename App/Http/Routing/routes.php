<?php

use App\Http\Controller\HealthController;
use App\Http\Routing\Router;

/** @var Router $router */
$router->get('/api/health', HealthController::class . '@check');
