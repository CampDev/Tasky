<?php

session_start();

require __DIR__.'/vendor/autoload.php';

$app = new Silex\Application;

require __DIR__.'/src/app.php';
require __DIR__.'/src/router.php';
require __DIR__.'/src/functions.php';

$app->run();