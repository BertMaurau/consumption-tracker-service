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
 * Description of UserConsumption
 *
 * @author bertmaurau
 */
class UserConsumption extends Models\BaseModel
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
        'table'             => 'user_consumptions',
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
            'id', 'item_id', 'volume', 'created_at', 'updated_at'
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
        'expandable'        => ['item', 'user'],
        /**
         * Resource URI
         */
        'resourceUris'      => [
            'self'      => [
                'users'        => 'user_id',
                'consumptions' => 'id',
            ],
            'parent'    => [
                'users' => 'user_id',
            ],
            'reference' => [
                ['items' => 'item_id',]
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
     * Item ID
     * @var int
     */
    public $item_id;

    /**
     * Get Item ID
     *
     * @return int
     */
    public function getItemId(): int
    {
        return $this -> item_id;
    }

    /**
     * Set Item ID
     *
     * @param int $itemId
     *
     * @return $this
     */
    public function setItemId(int $itemId)
    {
        $this -> item_id = (int) $itemId;
        return $this;
    }

    /**
     * Volume
     * @var int
     */
    public $volume;

    /**
     * Get Volume
     *
     * @return int
     */
    public function getVolume(): int
    {
        return $this -> volume;
    }

    /**
     * Set Volume
     *
     * @param int $volume
     *
     * @return $this
     */
    public function setVolume(int $volume)
    {
        $this -> volume = (int) $volume;
        return $this;
    }

    /**
     * Notes
     * @var string
     */
    public $notes;

    /**
     * Get Notes
     *
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this -> notes;
    }

    /**
     * Set Notes
     *
     * @param string $notes
     *
     * @return $this
     */
    public function setNotes(string $notes = null)
    {
        $this -> notes = $notes;
        return $this;
    }

    public $consumed_at;

    /**
     * Get Consumed At
     *
     * @return \DateTime
     */
    public function getConsumedAt(): \DateTime
    {
        return $this -> consumed_at;
    }

    /**
     * Set Consumed At
     *
     * @param \DateTime $consumedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setConsumedAt(\DateTime $consumedAt)
    {
        try {
            $dt = new \DateTime($consumedAt);
        } catch (\Exception $ex) {
            throw new \Exception("Could not parse given timestamp (UserConsumption::consumedAt).");
        }
        $this -> consumed_at = $dt;
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */
}
