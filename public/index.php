<?php

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Core\Router;

$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
