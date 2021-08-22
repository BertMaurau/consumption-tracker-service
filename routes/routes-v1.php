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

// CRON related endpoint
$route -> map('GET', '/external/crons/scheduler', [new Controllers\CronController, 'scheduler']) -> middleware($external);


// User related endpoints without middleware
$route -> map('POST', '/register', [new Controllers\UserController, 'create']);
$route -> map('POST', '/login', [new Controllers\UserController, 'login']);
$route -> map('POST', '/password-resets/request', [new Controllers\UserPasswordResetController, 'request']);
$route -> map('POST', '/password-resets/reset', [new Controllers\UserPasswordResetController, 'reset']);
//$route -> map('POST', '/reset-password/validate', [new Controllers\UserPasswordResetController, 'validate']); // DEPRECATED
// User related endpoints with middleware
$route -> group('', function ($route) {

    $route -> map('POST', '/logout', [new Controllers\UserController, 'logout']);

    // User related endpoints
    $route -> map('GET', '/me', [new Controllers\UserController, 'show']);
    $route -> map('GET', '/users/{id}', [new Controllers\UserController, 'show']);
    $route -> map('PATCH', '/users/{id}', [new Controllers\UserController, 'update']);
    $route -> map('DELETE', '/users/{id}', [new Controllers\UserController, 'delete']);
    $route -> map('POST', '/users/{id}/avatar', [new Controllers\UserController, 'avatar']);

    $route -> map('GET', '/my/consumptions', [new Controllers\UserConsumptionController, 'index']);
    $route -> map('GET', '/my/consumptions/{id}', [new Controllers\UserConsumptionController, 'show']);
    $route -> map('POST', '/my/consumptions', [new Controllers\UserConsumptionController, 'create']);
    $route -> map('PATCH', '/my/consumptions/{id}', [new Controllers\UserConsumptionController, 'update']);
    $route -> map('DELETE', '/my/consumptions/{id}', [new Controllers\UserConsumptionController, 'delete']);

    $route -> map('GET', '/users/{userId}/consumptions', [new Controllers\UserConsumptionController, 'index']);
    $route -> map('GET', '/users/{userId}/consumptions/{id}', [new Controllers\UserConsumptionController, 'show']);
    $route -> map('POST', '/users/{userId}/consumptions', [new Controllers\UserConsumptionController, 'create']);
    $route -> map('PATCH', '/users/{userId}/consumptions/{id}', [new Controllers\UserConsumptionController, 'update']);
    $route -> map('DELETE', '/users/{userId}/consumptions/{id}', [new Controllers\UserConsumptionController, 'delete']);

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

// Country related endpoints
$route -> group('/countries', function ($route) {

    $route -> map('GET', '/', [new Controllers\CountryController, 'index']);
    $route -> map('GET', '/{id}', [new Controllers\CountryController, 'show']);
});

// Timezone related endpoints
$route -> group('/timezones', function ($route) {

    $route -> map('GET', '/', [new Controllers\TimezoneController, 'index']);
    $route -> map('GET', '/{id}', [new Controllers\TimezoneController, 'show']);
});
