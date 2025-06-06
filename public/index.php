<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App();

require __DIR__ . '/../src/routes.php';

$app->run();
?>
