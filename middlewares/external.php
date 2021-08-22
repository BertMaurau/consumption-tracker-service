<?php

namespace ConsumptionTracker\Middelwares;

use ConsumptionTracker\Core AS Core;

/**
 * The actual middleware function that gets called upon routing.
 * You can do whatever you'd like here before continuing executing the requested
 * actions etc.
 */
$external = function ($request, $response, callable $next) {

    // get the auth-token from the request headers
    $token = Core\Auth::getBearerToken();

    // check if there's even a token present
    if ($logRequestIncoming = Core\Auth::getLogRequestIncoming()) {

        $remoteAddress = $logRequestIncoming -> getRemoteAddress();

        if (!in_array($remoteAddress,
                        [
                            // Remote: cron-job.org
                            '195.201.26.157',
                            '116.203.134.67',
                            '116.203.129.16',
                            // Home network
                            '2a02:1812:2427:7100:d4d:b3b3:3358:c4d7',
                            '78.23.45.92',
                        ]
                )) {
            return Core\Output::NotAuthorized($response, "Unauthorized origin ($remoteAddress).");
        }
    } else {
        return Core\Output::ServerError($response, "Failed to process incoming request.");
    }

    // continue the request if the user is allowed (passed the above checks)
    $response = $next($request, $response);
    return $response;
};
