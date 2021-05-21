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

/**
 * Description of UserLogRequestsIncoming
 *
 * @author bertmaurau
 */
class UserLogRequestsIncoming extends Models\BaseModel
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
        'table'             => 'user_log_requests_incoming',
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
            'id', 'created_at', 'updated_at'
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
        'expandable'        => ['user', 'log_request_incoming'],
        /**
         * Resource URI
         */
        'resourceUris'      => [
            'self'      => [
                'users'                 => 'user_id',
                'log-requests-incoming' => 'id',
            ],
            'parent'    => [
                'users' => 'user_id',
            ],
            'reference' => [
                'log-requests-incoming' => 'log_request_incoming_id',
            ]
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
     * User Token ID
     * @var int
     */
    public $user_token_id;

    /**
     * Get User Token ID
     *
     * @return int
     */
    public function getUserTokenId(): int
    {
        return $this -> user_token_id;
    }

    /**
     * Set User Token ID
     *
     * @param int $userTokenId
     *
     * @return $this
     */
    public function setUserTokenId(int $userTokenId)
    {
        $this -> user_token_id = (int) $userTokenId;
        return $this;
    }

    /**
     * Log Request Incoming ID
     * @var int
     */
    public $log_requests_incoming_id;

    /**
     * Get Log Requests Incoming ID
     *
     * @return int
     */
    public function getLogRequestsIncomingId(): int
    {
        return $this -> log_requests_incoming_id;
    }

    /**
     * Set Log Requests Incoming ID
     *
     * @param int $logRequestsIncomingId
     *
     * @return $this
     */
    public function setLogRequestsIncomingId(int $logRequestsIncomingId)
    {
        $this -> log_requests_incoming_id = (int) $logRequestsIncomingId;
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */
}
