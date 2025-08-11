<?php

use App\App;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$app = App::bootstrap();

// Run app
$app->run();