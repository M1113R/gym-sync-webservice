<?php

    $app->get('/', function ($request, $response, $args) {
        return $response->write("Welcome to the Home Page!");
    });

    $app->get('/about', function ($request, $response, $args) {
        return $response->write("This is the About Page!");
    });

?>
