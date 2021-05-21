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
 * Description of Item
 *
 * @author bertmaurau
 */
class Item extends Models\BaseModel
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
        'table'             => 'items',
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
         * List of properties that are allowed to be ordered on
         */
        'orderable'         => [
            'id', 'description', 'created_at', 'updated_at'
        ],
        /**
         * If the model contains an image, return the paths to the base image
         * directory
         */
        'hasImageReference' => false,
        /**
         * Directory for the images
         */
        'imageDirectory'    => 'items',
        /**
         * Linkable definition
         */
        'linkable'          => [],
        /**
         * Expandable definition
         */
        'expandable'        => ['item_category', 'item_type'],
        /**
         * Resource URI
         */
        'resourceUris'      => [
            'self'      => [
                'items' => 'id',
            ],
            'reference' => [
                ['item-categories' => 'item_category_id',],
                ['item-types' => 'item_type_id',]
            ]
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
     * Item Type ID
     * @var int
     */
    public $item_type_id;

    /**
     * Get Item Type ID
     *
     * @return int
     */
    public function getItemTypeId(): int
    {
        return $this -> item_type_id;
    }

    /**
     * Set Item Type ID
     *
     * @param int $itemTypeId
     *
     * @return $this
     */
    public function setItemTypeId(int $itemTypeId)
    {
        $this -> item_type_id = (int) $itemTypeId;
        return $this;
    }

    /**
     * Item Category ID
     * @var int
     */
    public $item_category_id;

    /**
     * Get Item Category ID
     *
     * @return int
     */
    public function getItemCategoryId(): int
    {
        return $this -> item_category_id;
    }

    /**
     * Set Item Category ID
     *
     * @param int $itemCategoryId
     *
     * @return $this
     */
    public function setItemCategoryId(int $itemCategoryId)
    {
        $this -> item_category_id = (int) $itemCategoryId;
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
}
