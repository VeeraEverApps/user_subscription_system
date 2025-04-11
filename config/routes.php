<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {
            $builder->connect('/', ['controller' => 'Plans', 'action' => 'index']);
            $builder->connect('/register', ['controller' => 'Users', 'action' => 'register']);
            $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
            $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
            $builder->connect('/dashboard', ['controller' => 'Users', 'action' => 'dashboard']);
            $builder->connect('/users/validate-field', ['controller' => 'Users', 'action' => 'validateField']);
            $builder->connect('/users/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);

        // Fallback routes
        $builder->fallbacks(DashedRoute::class);
    });
};