<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define("LARAVEL_START", microtime(true));

// Force HTTPS detection (behind LiteSpeed proxy)
$_SERVER["HTTPS"] = "on";
$_SERVER["SERVER_PORT"] = 443;

// Point to the correct paths
$publicPath = __DIR__ . "/public";

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__."/storage/framework/maintenance.php")) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__."/vendor/autoload.php";

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__."/bootstrap/app.php";

// Override the public path to point to the public directory
$app->usePublicPath($publicPath);

$app->handleRequest(Request::capture());
