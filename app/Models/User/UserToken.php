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

namespace ConsumptionTracker\Models\User;

use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Modules AS Modules;

/**
 * Description of UserToken
 *
 * @author bertmaurau
 */
class UserToken extends Models\BaseModel
{

    /**
     * |======================================================================
     * | Model Configuration
     * |======================================================================
     */
    const MODEL_CONFIG = [
        /**
         * Database table name
         */
        'table'             => 'user_tokens',
        /**
         * Field that represents the primary key
         */
        'primaryKey'        => 'id',
        /**
         * Use record timestamps (created_at, updated_at, deleted_at)
         */
        'timestamps'        => true,
        /**
         * Prefer soft-deletes (deleted_at) over hard deletes
         */
        'softDelete'        => true,
        /**
         * List of properties that are allowed to be updated
         */
        'updatable'         => [],
        /**
         * List of properties that are allowed to be searchable
         */
        'searchable'        => [
            'name'
        ],
        /**
         * List of properties that are allowed to be ordered on
         */
        'orderable'         => [
            'id', 'token', 'expires_at', 'created_at', 'updated_at'
        ],
        /**
         * If the model contains an image, return the paths to the base image
         * directory
         */
        'hasImageReference' => false,
        /**
         * Directory for the images
         */
        'imageDirectory'    => '',
        /**
         * Linkable definition
         */
        'linkable'          => [],
        /**
         * Expandable definition
         */
        'expandable'        => ['user'],
        /**
         * Resource URI
         */
        'resourceUris'      => [
            'self'   => [
                'users'  => 'user_id',
                'tokens' => 'id',
            ],
            'parent' => [
                'users' => 'user_id',
            ],
        ],
        /**
         * Parent
         */
        'parent'            => '\ConsumptionTracker\Models\User',
    ];

    /**
     * |======================================================================
     * | Model Properties
     * |======================================================================
     */

    /**
     * User ID
     * @var int
     */
    protected $user_id;

    /**
     * Get User ID
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this -> user_id;
    }

    /**
     * Set User ID
     *
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId(int $userId)
    {
        $this -> user_id = (int) $userId;
        return $this;
    }

    /**
     * Token
     * @var string
     */
    public $token;

    /**
     * Get Token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this -> token;
    }

    /**
     * Set Token
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token)
    {
        $this -> token = $token;
        return $this;
    }

    /**
     * Device
     * @var string
     */
    public $device;

    /**
     * Get Device
     *
     * @return string|null
     */
    public function getDevice(): ?string
    {
        return $this -> device;
    }

    /**
     * Set Device
     *
     * @param string $device
     *
     * @return $this
     */
    public function setDevice(string $device = null)
    {
        $this -> device = $device;
        return $this;
    }

    /**
     * Browser
     * @var string
     */
    public $browser;

    /**
     * Get Browser
     *
     * @return string|null
     */
    public function getBrowser(): ?string
    {
        return $this -> browser;
    }

    /**
     * Set Browser
     *
     * @param string $browser
     *
     * @return $this
     */
    public function setBrowser(string $browser = null)
    {
        $this -> browser = $browser;
        return $this;
    }

    /**
     * Location
     * @var string
     */
    public $location;

    /**
     * Get Location
     *
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this -> location;
    }

    /**
     * Set Location
     *
     * @param string $location
     *
     * @return $this
     */
    public function setLocation(string $location = null)
    {
        $this -> location = $location;
        return $this;
    }

    /**
     * Is Active
     * @var bool
     */
    public $is_active;

    /**
     * Get Is Active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this -> is_active;
    }

    /**
     * Set Is Active
     *
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive(bool $isActive)
    {
        $this -> is_active = $isActive;
        return $this;
    }

    /**
     * Is Disabled
     * @var bool
     */
    public $is_disabled;

    /**
     * Get Is Disabled
     *
     * @return bool
     */
    public function getIsDisabled(): bool
    {
        return $this -> is_disabled;
    }

    /**
     * Set Is Disabled
     *
     * @param bool $isDisabled
     *
     * @return $this
     */
    public function setIsDisabled(bool $isDisabled)
    {
        $this -> is_disabled = $isDisabled;
        return $this;
    }

    /**
     * Expires At
     * @var \DateTime
     */
    public $expires_at;

    /**
     * Get Expires At
     *
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this -> expires_at;
    }

    /**
     * Set Expires At
     *
     * @param \DateTime $expiresAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setExpiresAt($expiresAt)
    {
        $this -> expires_at = $expiresAt;
        if ($expiresAt && is_string($expiresAt)) {
            try {
                $dt = new \DateTime($expiresAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (UserToken::expiresAt).");
            }
            $this -> expires_at = $dt;
        }
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */

    /**
     * Check if token is valid (based on all conditions)
     *
     * @return bool Valid
     */
    public function isValid(): bool
    {
        return ($this -> isActive() && !$this -> isDisabled() && !$this -> isExpired());
    }

    /**
     * Check if token is expired
     *
     * @return bool Expired
     */
    public function isExpired(): bool
    {
        return (new \DateTime() > $this -> getExpiresAt());
    }

    /**
     * Check if token is disabled
     *
     * @return bool Disabled
     */
    public function isDisabled(): bool
    {
        return !$this -> getIsActive();
    }

    /**
     * Check if token is active
     *
     * @return bool Active
     */
    public function isActive(): bool
    {
        return $this -> getIsActive();
    }

    /**
     * Generate a new token for given User ID
     *
     * @param int $userId
     * @param int $daysValid
     *
     * @return UserToken
     */
    public static function create(int $userId, int $daysValid = 30, array $properties = []): UserToken
    {

        // generate the datetime until expires
        $expiresAt = date("Y-m-d H:i:s", strtotime("+$daysValid day"));

        // fetch the location
        $location = Core\Auth::getLocation();

        // create the record
        $userToken = (new self)
                -> setUserId($userId)
                -> setToken(Core\Generator::Uid())
                -> setIsActive(true)
                -> setExpiresAt($expiresAt)
                -> setLocation($location)
                -> setDevice($properties['device'] ?? null)
                -> setBrowser($properties['device'] ?? null)
                -> insert();

        // generate a new JWT Token
        return $userToken;
    }

    /**
     * Generate Auth Token from UserToken values
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getAuthToken(): string
    {
        if (!$this -> getId()) {
            throw new \Exception('UserToken not initialized.');
        }

        return Modules\JWT::encode(
                        [
                            'env'    => Core\Config::getInstance() -> API() -> env,
                            'userId' => $this -> getUserId(),
                            'token'  => $this -> getToken(),
                        ], Core\Config::getInstance() -> Salts() -> token);
    }

}
