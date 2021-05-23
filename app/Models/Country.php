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

use ConsumptionTracker\Core AS Core;

/**
 * Description of Country
 *
 * @author bertmaurau
 */
class Country extends BaseModel
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
        'table'             => 'countries',
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
         * List of properties that are allowed to be filtered on
         */
        'filterable'        => [
            'iso'  => ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'iso', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            'name' => ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ],
        /**
         * List of properties that are allowed to be ordered on
         */
        'orderable'         => [
            'id', 'iso', 'name', 'created_at', 'updated_at'
        ],
        /**
         * If the model contains an image, return the paths to the base image
         * directory
         */
        'hasImageReference' => false,
        /**
         * Directory for the images
         */
        'imageDirectory'    => 'countries',
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
                'countries' => 'id',
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
     * ISO
     * @var string
     */
    public $iso;

    /**
     * Get ISO
     *
     * @return string
     */
    public function getIso(): string
    {
        return $this -> iso;
    }

    /**
     * Set ISO
     *
     * @param string $iso
     *
     * @return $this
     */
    public function setIso(string $iso)
    {
        $this -> iso = $iso;
        return $this;
    }

    /**
     * ISO 3
     * @var string
     */
    public $iso_3;

    /**
     * Get ISO 3
     *
     * @return string|null
     */
    public function getIso3(): ?string
    {
        return $this -> iso_3;
    }

    /**
     * Set ISO 3
     *
     *
     * @param string $iso3
     * @return $this
     */
    public function setIso3(string $iso3 = null)
    {
        $this -> iso_3 = $iso3;
        return $this;
    }

    /**
     * ISO Num
     * @var int
     */
    public $iso_num;

    /**
     * Get ISO Num
     *
     * @return int
     */
    public function getIsoNum(): int
    {
        return $this -> iso_num;
    }

    /**
     * Set ISO Num
     *
     * @param int $isoNum
     *
     * @return $this
     */
    public function setIsoNum(int $isoNum)
    {
        $this -> iso_num = $isoNum;
        return $this;
    }

    /**
     * Name
     * @var string
     */
    public $name;

    /**
     * Get Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this -> name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this -> name = $name;
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */
}
