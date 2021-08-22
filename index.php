<?php

namespace ConsumptionTracker;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Models AS Models;

// -----------------------------------------------------------------------------
//  Handle CORS
// -----------------------------------------------------------------------------
// This must be done before any output has been sent from the server..
// Modify to your own specifications
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: '
        . 'POST, '
        . 'GET, '
        . 'DELETE, '
        . 'PUT, '
        . 'PATCH, '
        . 'OPTIONS');
header('Access-Control-Allow-Headers: '
        . 'Authorization, '
        . 'X-PINGOTHER, '
        . 'Origin, '
        . 'X-Requested-With, '
        . 'Content-Type, '
        . 'Accept, '
        . 'Cache-Control, '
        . 'Pragma, '
        . 'Accept-Encoding,'
        . 'ResponseType');
header('Access-Control-Max-Age: 1728000');

// load all requirements, database connection, ..
require_once __DIR__ . '/require.php';

// log requests
try {
    Models\LogRequestIncoming::create();
} catch (\Exception $ex) {

}

// -----------------------------------------------------------------------------
// Start the routing build-up
// -----------------------------------------------------------------------------
$container = new \League\Container\Container;

$container -> share('response', \Zend\Diactoros\Response::class);
$container -> share('request', function () {

    // change Constants API ROOT if the "api" is not running on the root of the
    // domain. For ex. if this is hosted within a subdirectory API then set this
    // to "/api" so that it matches the actual url: http://domain.com/api for ex.
    $_SERVER['REQUEST_URI'] = str_replace(Core\Config::getInstance() -> API() -> root, '', Core\ValidatedRequest::filterInput(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING));

    return \Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
});

$container -> share('emitter', \Zend\Diactoros\Response\SapiEmitter::class);

// start the Router
$route = new \League\Route\RouteCollection;

// require the middlewares
require __DIR__ . '/middlewares/user.php';

// require the list of available routes
require __DIR__ . '/routes/routes-v1.php';

try {
    $response = $route -> dispatch($container -> get('request'), $container -> get('response'));
} catch (\League\Route\Http\Exception\NotFoundException $exception) {
    $response = Core\Output::NotFound(Core\Output::Clear(), 'Route not found.');
} catch (\League\Route\Http\Exception\MethodNotAllowedException $exception) {
    $response = Core\Output::Conflict(Core\Output::Clear(), 'Method not allowed.');
}

// sends headers and output using PHP's standard SAPI mechanisms
try {
    $container -> get('emitter') -> emit(
            $response -> withAddedHeader('Content-Type', 'application/json'));
} catch (\RuntimeException $ex) {

}
// -----------------------------------------------------------------------------
// end of script

