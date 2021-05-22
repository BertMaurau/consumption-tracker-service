<?php

namespace ConsumptionTracker\Middelwares;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Modules AS Modules;

/**
 * The actual middleware function that gets called upon routing.
 * You can do whatever you'd like here before continuing executing the requested
 * actions etc.
 */
$user = function ($request, $response, callable $next) {

    // get the auth-token from the request headers
    $token = Core\Auth::getBearerToken();

    // check if there's even a token present
    if ($token) {

        try {
            // Get the JSON data that has been encoded (can contain whatever you like)
            // and assign that data to the Auth object
            $tokenData = (object) Modules\JWT::decode($token, Core\Config::getInstance() -> Salts() -> token);

            // check for required token data values
            foreach (['env', 'userId', 'token'] as $key => $value) {
                if (!isset($tokenData -> {$value})) {
                    return Core\Output::NotAuthorized($response, "Invalid authToken.");
                }
            }


            // check if the token ID exists and user Id actually matches
            if (!$foundAuthToken = Core\Auth::findTokenForUserId($tokenData -> token, $tokenData -> userId)) {
                return Core\Output::NotAuthorized($response, "Invalid authToken.");
            }

            // check the environment of the token against the running environment
            if ($tokenData -> env !== Core\Config::getInstance() -> API() -> env) {
                return Core\Output::NotAuthorized($response, "The provided authToken does not match the current Environment.");
            }

            // check if token is valid (all conditions), if not, check which condition failed
            if (!$foundAuthToken -> isValid()) {

                // check for expired
                if ($foundAuthToken -> isExpired()) {
                    return Core\Output::NotAuthorized($response, "The provided authToken has expired.");
                }

                // check for disabled
                if ($foundAuthToken -> isDisabled()) {
                    return Core\Output::NotAuthorized($response, "The provided authToken has been disabled.");
                }

                // check for destoryed
                if ($foundAuthToken -> isDestroyed()) {
                    return Core\Output::NotAuthorized($response, "The provided authToken has been destroyed.");
                }
            }

            try {
                if ($logRequestIncoming = Core\Auth::getLogRequestIncoming()) {
                    (new Models\User\UserLogRequestsIncoming)
                            -> setUserId($foundAuthToken -> getUserId())
                            -> setUserTokenId($foundAuthToken -> getId())
                            -> setLogRequestsIncomingId($logRequestIncoming -> getId())
                            -> insert();
                }
            } catch (\Exception $ex) {

            }

            // assign the data to the auth object
            Core\Auth::assign((array) $tokenData);
        } catch (\Exception $ex) {

            // Send a response when the integrity-check of the token failed.
            // This will happen if the passed encrypted data is not a valid JSON-string
            // due to a wrong encryption or bad encoding.
            return Core\Output::NotAuthorized($response, "The authToken integrity check failed!");
        }
    } else {

        // the response when there is no token present
        return Core\Output::NotAuthorized($response, "No authToken provided!");
    }

    // continue the request if the user is allowed (passed the above checks)
    $response = $next($request, $response);
    return $response;
};
