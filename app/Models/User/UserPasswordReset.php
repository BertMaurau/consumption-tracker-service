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
 * Description of UserPasswordReset
 *
 * @author bertmaurau
 */
class UserPasswordReset extends Models\BaseModel
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
        'table'             => 'user_password_resets',
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
            'id', 'claimed_at', 'expires_at', 'created_at', 'updated_at'
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
                'users'           => 'user_id',
                'password-resets' => 'id',
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
     * Claimed At
     * @var \DateTime
     */
    public $claimed_at;

    /**
     * Get Claimed At
     *
     * @return \DateTime
     */
    public function getClaimedAt(): ?\DateTime
    {
        return $this -> claimed_at;
    }

    /**
     * Set Claimed At
     *
     * @param \DateTime $claimedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setClaimedAt($claimedAt)
    {
        $this -> claimed_at = $claimedAt;
        if ($claimedAt && is_string($claimedAt)) {
            try {
                $dt = new \DateTime($claimedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (UserToken::claimedAt).");
            }
            $this -> claimed_at = $dt;
        }
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */

    /**
     * Check if reset is valid (based on all conditions)
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return (!$this -> isClaimed() && !$this -> isExpired());
    }

    /**
     * Check if reset is expired
     *
     * @return bool Expired
     */
    public function isExpired(): bool
    {
        return (new \DateTime() > $this -> getExpiresAt());
    }

    /**
     * Check if reset is claimed
     *
     * @return bool
     */
    public function isClaimed(): bool
    {
        return !!$this -> getClaimedAt();
    }

    /**
     * Generate a new token for given User ID
     *
     * @param int $userId
     * @param array $properties
     *
     * @return UserPasswordReset
     */
    public static function create(int $userId, array $properties = []): UserPasswordReset
    {

        // generate the datetime until expires
        $expiresAt = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // create the record
        $userPasswordReset = (new self)
                -> setUserId($userId)
                -> setToken(Core\Generator::Uid())
                -> setExpiresAt($expiresAt)
                -> insert();

        return $userPasswordReset;
    }

    /**
     * Get the public URL to reset the password
     *
     * @return string
     */
    public function getResetLink()
    {
        return Core\Config::getInstance() -> API() -> webApp . '/reset-password?token=' . $this -> getToken();
    }

}
