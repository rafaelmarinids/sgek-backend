<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    
    $file = __DIR__ . $url['path'];
    
    if (is_file($file)) {
        return false;
    }
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Expose-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

session_start();

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/src/settings.php';

$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/src/dependencies.php';

// Register middleware
require __DIR__ . '/src/middleware.php';

// Register routes
require __DIR__ . '/src/routes.php';

// Run app
$app->run();
