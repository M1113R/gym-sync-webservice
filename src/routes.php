<?php

use Src\Controllers\AuthController;
use Src\Middleware\AuthMiddleware;

// Public routes
$app->post('/auth/login', function ($request, $response) {
    $controller = new AuthController();
    return $controller->login($request, $response);
});

$app->post('/auth/register', function ($request, $response) {
    $controller = new AuthController();
    return $controller->register($request, $response);
});

// Protected routes
$app->group('', function ($group) {
    $group->get('/', function ($request, $response, $args) {
        return $response->write("Welcome to the Home Page!");
    });

    $group->get('/about', function ($request, $response, $args) {
        return $response->write("This is the About Page!");
    });
})->add(new AuthMiddleware());

?>
