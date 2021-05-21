<?php

/*
 * The MIT License
 *
 * Copyright 2021 bertmaurau.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ConsumptionTracker\Core;

use ConsumptionTracker\Models AS Models;

/**
 * Description of Auth
 *
 * Handles everything concerning the Authentication and user sessions etc.
 *
 * @author Bert Maurau
 */
class Auth
{

    // Put auth items here for easier access (JWT token data values)
    // Don't forget to add the necessary getter/setter.
    private static $userId;
    private static $token;
    private static $env;
    // Location info
    private static $logRequestIncoming;
    private static $location;
    private static $geoLocation;

    /**
     * Assign the Auth values
     *
     * @param array $properties The array with the properties to map to this Auth session.
     *
     * @returns void
     */
    public static function assign(array $properties = [])
    {
        // loop properties and attempt to call the setter
        foreach ($properties as $key => $value) {

            // construct the setter name
            $setter = 'set' . ucfirst($key);

            // check if the setter exists and if it is callable
            if (is_callable(array(Auth::class, $setter))) {
                // execute the setter
                call_user_func(array(Auth::class, $setter), $value);
            }
        }
    }

    /**
     * Check for and get the Bearer token from the Authorization header
     *
     * @return string The extracted token
     */
    public static function getBearerToken(string $urlTokenKey = null)
    {

        $token = null;

        // get the headers first.
        $authorizationHeaderValue = self::getAuthorizationHeaderValue();

        if (!empty($authorizationHeaderValue)) {

            if (preg_match('/Bearer\s(\S+)/', $authorizationHeaderValue, $matches)) {

                // return the token
                $token = $matches[1];
            }
        } else if ($urlTokenKey) {
            $token = filter_input(INPUT_GET, $urlTokenKey);
        }

        return $token;
    }

    /**
     * Find the provided token for given User
     *
     * @param string $tokenUid Token UID
     * @param int $userId User ID
     *
     * @return Models\UserToken
     */
    public static function findTokenForUserId(string $token, int $userId): ?Models\User\UserToken
    {
        return (new Models\User\UserToken) -> findBy(['token' => $token, 'user_id' => $userId], $take = 1);
    }

    /**
     * Get the Authorization header from the request Headers
     *
     * @return string The value of the Authorization header
     */
    public static function getAuthorizationHeaderValue()
    {
        $authorizationHeaderValue = null;

        if (!$authorizationHeaderValue = trim(ValidatedRequest::filterInput(INPUT_SERVER, 'Authorization'))) {

            if (!$authorizationHeaderValue = trim(ValidatedRequest::filterInput(INPUT_SERVER, 'HTTP_AUTHORIZATION'))) { // Nginx or fast CGI
                if (function_exists('apache_request_headers')) {

                    $apacheRequestHeaders = apache_request_headers();

                    // Server-side fix for bug in old Android versions (a nice side-effect of
                    // this fix means we don't care about capitalization for Authorization)
                    $requestHeaders = array_combine(array_map('ucwords', array_keys($apacheRequestHeaders)), array_values($apacheRequestHeaders));

                    if (isset($requestHeaders['Authorization'])) {
                        $authorizationHeaderValue = trim($requestHeaders['Authorization']);
                    }
                }
            }
        }

        return $authorizationHeaderValue;
    }

    /**
     * Get the remote address of the client
     *
     * @return null|string
     */
    public static function getRemoteAddress(): ?string
    {
        $ipaddress = '';

        if (filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } else if (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } else if (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED');
        } else if (filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR');
        } else if (filter_input(INPUT_SERVER, 'HTTP_FORWARDED')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_FORWARDED');
        } else if (filter_input(INPUT_SERVER, 'REMOTE_ADDR')) {
            $ipaddress = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * Get Geo Location
     *
     * @return array
     */
    public static function getGeoLocation()
    {

        if (!self::$geoLocation) {

            $parts = [
                'Unknown', 'Unknown'
            ];
            $geoLat = null;
            $geoLng = null;

            // get location infor for IP address
            $remoteAddress = self::getRemoteAddress();
            if ($remoteAddress && $remoteAddress !== 'UNKNOWN') {
                // get location info for remote address
                try {
                    // not sure how you can catch/handle warnings thrown by the file_get_contents?
                    // surpressing for now..
                    $resp = @file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $remoteAddress);
                    if ($resp) {
                        $geoResponse = unserialize($resp);
                        if (!$geoResponse) {

                        } else {
                            $parts[0] = (isset($geoResponse['geoplugin_city']) ? $geoResponse['geoplugin_city'] : 'Unknown');
                            $parts[1] = (isset($geoResponse['geoplugin_countryCode']) ? $geoResponse['geoplugin_countryCode'] : 'Unknown');
                            $geoLat = (isset($geoResponse['geoplugin_latitude']) ? $geoResponse['geoplugin_latitude'] : null);
                            $geoLng = (isset($geoResponse['geoplugin_longitude']) ? $geoResponse['geoplugin_longitude'] : null);
                        }
                    }
                } catch (\Exception $ex) {
                    Core\Logger::Write(json_encode($ex), 4);
                }
            }

            self::setGeoLocation(implode(', ', $parts), $geoLat, $geoLng);
            self::setLocation(implode(', ', $parts));
        }

        return self::$geoLocation;
    }

    /**
     * Set Geo Location
     *
     * @param string $location
     * @param float $geoLat
     * @param float $geoLng
     */
    public static function setGeoLocation(string $location, float $geoLat = null, float $geoLng = null)
    {
        self::$geoLocation = [$location, $geoLat, $geoLng];
    }

    /**
     * Get Location
     *
     * @return string
     */
    public static function getLocation(): string
    {

        if (!self::$location) {

            self::getGeoLocation();
        }

        return self::$location;
    }

    /**
     * Set Location
     *
     * @param string $location
     */
    public static function setLocation($location)
    {
        self::$location = $location;
    }

    /**
     * Get User ID
     *
     * @return integer
     */
    public static function getUserId(): int
    {
        return self::$userId;
    }

    /**
     * Set User ID
     *
     * @param integer $userId
     */
    private static function setUserId(int $userId)
    {
        self::$userId = $userId;
    }

    /**
     * Get Token
     *
     * @return string
     */
    public static function getToken(): string
    {
        return self::$token;
    }

    /**
     * Set Token
     *
     * @param string $token
     */
    private static function setToken(string $token)
    {
        self::$token = $token;
    }

    /**
     * Get Env
     *
     * @return string
     */
    public static function getEnv()
    {
        return self::$env;
    }

    /**
     * Set Env
     *
     * @param string $env
     */
    private static function setEnv(string $env)
    {
        self::$env = $env;
    }

    /**
     * Get Log Request Incoming
     *
     * @return \ConsumptionTracker\Models\LogRequestIncoming
     */
    public static function getLogRequestIncoming()
    {
        return self::$logRequestIncoming;
    }

    /**
     * Set Log Request Incoming
     *
     * @param \ConsumptionTracker\Models\LogRequestIncoming $logRequestIncoming
     */
    public static function setLogRequestIncoming(Models\LogRequestIncoming $logRequestIncoming)
    {
        self::$logRequestIncoming = $logRequestIncoming;
    }

}
