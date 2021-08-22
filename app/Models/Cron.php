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

namespace ConsumptionTracker\Models;

use \ConsumptionTracker\Core AS Core;
use \ConsumptionTracker\Models AS Models;

/**
 * Description of Cron
 *
 * @author bertmaurau
 */
class Cron extends Models\BaseModel
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
        'table'             => 'crons',
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
        'updatable'         => [
        ],
        /**
         * List of properties that are allowed to be searchable
         */
        'searchable'        => [
            'description'
        ],
        /**
         * List of properties that are allowed to be filtered on
         */
        'filterable'        => [
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
        'imageDirectory'    => null,
        /**
         * Linkable definition
         */
        'linkable'          => [],
        /**
         * Expandable definition
         */
        'expandable'        => [],
        /**
         * Resource URI
         */
        'resourceUris'      => [
            'self' => [
                'crons' => 'id',
            ],
        ],
        /**
         * Parent
         */
        'parent'            => null,
    ];

    /**
     * |======================================================================
     * | Model Properties
     * |======================================================================
     */

    /**
     * Key
     * @var string
     */
    public $key;

    /**
     * Get Key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this -> key;
    }

    /**
     * Set Key
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key)
    {
        $this -> key = $key;
        return $this;
    }

    /**
     * Title
     * @var string
     */
    public $title;

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this -> title;
    }

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this -> title = $title;
        return $this;
    }

    /**
     * Description
     * @var string
     */
    public $description;

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this -> description;
    }

    /**
     * Set Description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this -> description = $description;
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */
    public function execute()
    {

        // init a new log
        $logCron = (new LogCron) -> setCronId($this -> getId()) -> setStartedAt(date('Y-m-d H:i:s'));




        // finalize the log
        $logCron -> setEndedAt(date('Y-m-d H:i:s')) -> insert();

        // link the log to the user
        (new User\UserLogCron)
                -> setUserId($userCronKey -> getUserId())
                -> setUserCronKeyId($userCronKey -> getId())
                -> setLogCronId($logCron -> getId())
                -> insert();
    }

}
