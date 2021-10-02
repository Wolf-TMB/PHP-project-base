<?php

global $app;

use App\Http\Controllers\UserController;
use App\Http\Controllers\WebController;

$app::$router->get('/',    [WebController::class, 'getIndex']);
$app::$router->get('/404', [WebController::class, 'get404']);
$app::$router->get('/405', [WebController::class, 'get405']);
$app::$router->get('/security', [WebController::class, 'getSecurity']);

