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
 * Description of Timezone
 *
 * @author bertmaurau
 */
class Timezone extends BaseModel
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
        'table'             => 'timezones',
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
            'country_code', 'timezone'
        ],
        /**
         * List of properties that are allowed to be filtered on
         */
        'filterable'        => [
            'country_code' => ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'country_code', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ],
        /**
         * List of properties that are allowed to be ordered on
         */
        'orderable'         => [
            'id', 'country_code', 'timezone', 'created_at', 'updated_at'
        ],
        /**
         * If the model contains an image, return the paths to the base image
         * directory
         */
        'hasImageReference' => false,
        /**
         * Directory for the images
         */
        'imageDirectory'    => 'timezones',
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
                'timezones' => 'id',
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
     * Country Code
     * @var string
     */
    public $country_code;

    /**
     * Get Country Code
     *
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this -> country_code;
    }

    /**
     * Set Country Code
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode(string $countryCode)
    {
        $this -> country_code = $countryCode;
        return $this;
    }

    /**
     * Timezone
     * @var string
     */
    public $timezone;

    /**
     * Get Timezone
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this -> timezone;
    }

    /**
     * Set Timezone
     *
     * @param string $timezone
     *
     * @return $this
     */
    public function setTimezone(string $timezone)
    {
        $this -> timezone = $timezone;
        return $this;
    }

    /**
     * GMT Offset
     * @var float
     */
    public $gmt_offset;

    /**
     * Get GMT Offset
     *
     * @return float
     */
    public function getGmtOffset(): float
    {
        return $this -> gmt_offset;
    }

    /**
     * Set GMT Offset
     *
     * @param float $gmtOffset
     *
     * @return $this
     */
    public function setGmtOffset(float $gmtOffset)
    {
        $this -> gmt_offset = (float) $gmtOffset;
        return $this;
    }

    /**
     * DST Offset
     * @var float
     */
    public $dst_offset;

    /**
     * Get DST Offset
     *
     * @return float
     */
    public function getDstOffset(): float
    {
        return $this -> dst_offset;
    }

    /**
     * Set DST Offset
     *
     * @param float $dstOffset
     *
     * @return $this
     */
    public function setDstOffset(float $dstOffset)
    {
        $this -> dst_offset = (float) $dstOffset;
        return $this;
    }

    /**
     * RAW Offset
     * @var float
     */
    public $raw_offset;

    /**
     * Get RAW Offset
     *
     * @return float
     */
    public function getRawOffset(): float
    {
        return $this -> raw_offset;
    }

    /**
     * Set RAW Offset
     *
     * @param float $rawOffset
     *
     * @return $this
     */
    public function setRawOffset(float $rawOffset)
    {
        $this -> raw_offset = (float) $rawOffset;
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */
}
