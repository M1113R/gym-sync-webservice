<?php

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($config);

require __DIR__ . '/../src/routes.php';

$app->run();
?>
