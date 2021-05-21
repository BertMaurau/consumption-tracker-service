<?php

namespace ConsumptionTracker\Routes;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Controllers AS Controllers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// index
$route -> map('GET', '/', function(ServerRequestInterface $request, ResponseInterface $response) {

    // You can do whatever you want here, This route is not required or anything.
    return Core\Output::OK($response, date('Y-m-d H:i:s'));
});

// User related endpoints without middleware
$route -> map('POST', '/register', [new Controllers\UserController, 'create']);
$route -> map('POST', '/login', [new Controllers\UserController, 'login']);

$route -> group('', function ($route) {

    // User related endpoints
    $route -> map('GET', '/me', [new Controllers\UserController, 'show']);
    $route -> map('PATCH', '/me', [new Controllers\UserController, 'update']);
    $route -> map('DELETE', '/me', [new Controllers\UserController, 'delete']);

    $route -> map('GET', '/my/consumptions', [new Controllers\UserCOnsumptionController, 'index']);
    $route -> map('GET', '/my/consumptions/{id}', [new Controllers\UserCOnsumptionController, 'show']);
    $route -> map('POST', '/my/consumptions', [new Controllers\UserCOnsumptionController, 'create']);
    $route -> map('PATCH', '/my/consumptions/{id}', [new Controllers\UserCOnsumptionController, 'update']);
    $route -> map('DELETE', '/my/consumptions/{id}', [new Controllers\UserCOnsumptionController, 'delete']);

    // User consumptions related endpoints
}) -> middleware($user);

// Item related endpoints
$route -> group('/items', function ($route) {

    $route -> map('GET', '/', [new Controllers\ItemController, 'index']);
    $route -> map('GET', '/{id}', [new Controllers\ItemController, 'show']);
});

// Item Type related endpoints
$route -> group('/item-types', function ($route) {

    $route -> map('GET', '/', [new Controllers\ItemTypeController, 'index']);
    $route -> map('GET', '/{id}', [new Controllers\ItemTypeController, 'show']);
});

// Item Category related endpoints
$route -> group('/item-categories', function ($route) {

    $route -> map('GET', '/', [new Controllers\ItemCategoryController, 'index']);
    $route -> map('GET', '/{id}', [new Controllers\ItemCategoryController, 'show']);
});

// Volume Definitions related endpoints
$route -> group('/volume-definitions', function ($route) {

    $route -> map('GET', '/', [new Controllers\VolumeDefinitionController, 'index']);
    $route -> map('GET', '/{id}', [new Controllers\VolumeDefinitionController, 'show']);
});
