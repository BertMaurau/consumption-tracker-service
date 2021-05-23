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
 * Description of BaseModel
 *
 * @author bertmaurau
 */
class BaseModel
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
        'table'             => '',
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
        'searchable'        => [],
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
        'expandable'        => [],
        /**
         * Resource URI
         */
        'resourceUri'       => [],
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
     * ID
     * @var int
     */
    public $id;

    /**
     * Get ID
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this -> id;
    }

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this -> id = (int) $id;
        return $this;
    }

    /**
     * Created At
     * @var \DateTime
     */
    public $created_at;

    /**
     * Get Created At
     *
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this -> created_at;
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setCreatedAt($createdAt)
    {
        $this -> created_at = $createdAt;
        if ($createdAt && is_string($createdAt)) {
            try {
                $dt = new \DateTime($createdAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (BaseModel::createdAt).");
            }
            $this -> created_at = $dt;
        }
        return $this;
    }

    /**
     * Updated At
     * @var \DateTime
     */
    public $updated_at;

    /**
     * Get Updated At
     *
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this -> updated_at;
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setUpdatedAt($updatedAt = null)
    {
        $this -> updated_at = $updatedAt;
        if ($updatedAt && is_string($updatedAt)) {
            try {
                $dt = new \DateTime($updatedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (BaseModel::updatedAt).");
            }
            $this -> updated_at = $dt;
        }
        return $this;
    }

    /**
     * Deleted At
     * @var \DateTime
     */
    protected $deleted_at;

    /**
     * Get Deleted At
     *
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this -> deleted_at;
    }

    /**
     * Set Deleted At
     *
     * @param string $deletedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setDeletedAt($deletedAt = null)
    {
        $this -> deleted_at = $deletedAt;
        if ($deletedAt && is_string($deletedAt)) {
            try {
                $dt = new \DateTime($deletedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (BaseModel::deletedAt).");
            }
            $this -> deleted_at = $dt;
        }

        return $this;
    }

    /**
     * Attributes
     * @var array
     */
    public $attributes = [];

    /**
     * Get Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this -> attributes;
    }

    /**
     * Set Attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        return $this -> attributes = $attributes;
    }

    /**
     * Relations
     * @var array
     */
    public $relations = [];

    /**
     * Get Relations
     *
     * @return array
     */
    public function getRelations()
    {
        return $this -> relations;
    }

    /**
     * Set Relations
     *
     * @param array $relations
      $
     * @return $this
     */
    public function setRelations(array $relations = [])
    {
        return $this -> relations = $relations;
    }

    /**
     * Resources
     * @var array
     */
    public $resources = [];

    /**
     * Get Resources
     *
     * @return array
     */
    public function getResources()
    {
        return $this -> resources;
    }

    /**
     * Set Resources
     *
     * @param array $resources
      $
     * @return $this
     */
    public function setResources(array $resources = [])
    {
        return $this -> resources = $resources;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */

    /**
     * Construct the model with optional properties
     *
     * @param array $properties
     *
     * @return $this
     */
    public function __construct(array $properties = [])
    {
        // map any property to self
        if ($properties && count($properties) > 0) {
            $this -> map($properties);
        }

        return $this;
    }

    /**
     * Get an attribute
     *
     * @param string $property The name of the property
     *
     * @return mixed
     */
    public function getAttribute(string $property)
    {
        return $this -> attributes[$property] ?? null;
    }

    /**
     * Add item as attribute
     *
     * @param string $property The name of the property
     * @param any $value The value
     *
     * @return $this
     */
    public function addAttribute(string $property, $value)
    {
        $this -> attributes[$property] = $value;

        return $this;
    }

    /**
     * Get a relation
     *
     * @param string $realtion The name of the relation-object
     *
     * @return mixed
     */
    public function getRelation(string $relation)
    {
        return $this -> relations[$relation] ?? null;
    }

    /**
     * Add relation
     *
     * @param string $relation The name of the relation
     * @param any $relationObject The relation
     *
     * @return $this
     */
    public function addRelation(string $relation, $relationObject)
    {
        $this -> relations[$relation] = $relationObject;

        return $this;
    }

    /**
     * Get a resource
     *
     * @param string $resourceType The name of the resource-object
     *
     * @return mixed
     */
    public function getResource(string $resourceType)
    {
        return $this -> resources[$resourceType] ?? null;
    }

    /**
     * Add resource
     *
     * @param string $resourceType The name of the resource
     * @param any $resourceUri The uri of the resource
     *
     * @return $this
     */
    public function addResource(string $resourceType, $resourceUri)
    {
        $this -> resources[$resourceType] = $resourceUri;

        return $this;
    }

    /**
     * Get Model Configuration option
     *
     * @param string $configOption
     *
     * @return mixed
     */
    public static function getConfig(string $configOption)
    {
        return static::MODEL_CONFIG[$configOption] ?? false;
    }

    /**
     * Map the given properties to self, calling the setters.
     *
     * @param object $properties The list of properties to assign
     *
     * @return $this
     */
    public function map(array $properties = [])
    {
        if (self::getConfig('hasImageReference')) {
            // avatar base url
            $this -> addAttribute('images_url', Core\Config::getInstance() -> Paths() -> imagesUrl . self::getConfig('imageDirectory'));
        }

        if (isset($properties)) {
            // loop properties and attempt to call the setter
            foreach ($properties as $key => $value) {
                $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                // check if the setter exists and is callable
                if (is_callable(array($this, $setter))) {
                    // execute the setter
                    call_user_func(array($this, $setter), $value);
                } else {
                    // not a property, add to the attributes list
                    $this -> addAttribute($key, $value);
                }
            }
        }

        // add resource url
        if ($this -> getId()) {
            foreach (self::getConfig('resourceUris') as $resourceType => $parts) {

                if ($resourceType == 'reference') {
                    $referenceList = [];
                    foreach ($parts as $reference) {
                        $uri = Core\Config::getInstance() -> API() -> baseUrl;
                        foreach ($reference as $uriPart => $uriValue) {

                            if ($uriValue) {
                                $getter = 'get' . str_replace('_', '', ucwords($uriValue, '_'));
                                if (is_callable(array($this, $getter))) {
                                    // execute the setter
                                    $uri .= '/' . $uriPart . '/' . call_user_func(array($this, $getter));
                                }
                            } else {
                                $uri .= '/' . $uriPart;
                            }
                        }
                        $referenceList[] = $uri;
                    }
                    $this -> addResource('reference', $referenceList);
                } else {
                    $uri = Core\Config::getInstance() -> API() -> baseUrl;
                    foreach ($parts as $uriPart => $uriValue) {
                        $getter = 'get' . str_replace('_', '', ucwords($uriValue, '_'));
                        if (is_callable(array($this, $getter))) {
                            // execute the setter
                            $uri .= '/' . $uriPart . '/' . call_user_func(array($this, $getter));
                        }
                    }
                    $this -> addResource($resourceType, $uri);
                }
            }
        }

        return $this;
    }

    /**
     * Expand the object with other related items
     *
     * @param array $items List of items to expand
     *
     * @return $this
     */
    public function expand(array $items = [])
    {

        if (!$this -> getId()) {
            throw new \Exception('Model not initialized.');
        }

        if (count($items) === 0) {
            // all items
            $items = self::getConfig('expandable');
        }

        foreach ($items as $item) {
            $className = str_replace('_', '', ucwords($item, '_'));
            $getter = 'get' . $className . 'Id';
            if (is_callable(array($this, $getter))) {
                $class = '\ConsumptionTracker\\Models\\' . $className;
                $this -> addRelation($item, (new $class) -> getById(call_user_func(array($this, $getter))));
            }
        }
        return $this;
    }

    /**
     * Get model by ID
     *
     * @param int $id The ID
     *
     * @return $this
     */
    public function getById(int $id)
    {
        $query = " SELECT * "
                . "FROM " . self::getConfig('table') . " "
                . "WHERE `id` = " . Core\Database::escape($id) . " "
                . ((self::getConfig('softDelete')) ? " AND " . self::getConfig('table') . ".deleted_at IS NULL " : "")
                . "LIMIT 1;";
        $result = Core\Database::query($query);
        if ($result -> num_rows < 1) {
            return null;
        } else {
            // create an object from the result
            return $this -> map($result -> fetch_assoc());
        }
    }

    /**
     * Shortcut function for findBy title_id
     *
     * @param int $titleId
     *
     * @return array
     */
    public function getByTitleId(int $titleId)
    {
        return $this -> findBy(['title_id' => $titleId], 9999, 0);
    }

    /**
     * Get model by specific fields
     *
     * @param array $filter List of fields to filter on
     * @param int $take Pagination take
     * @param int $skip Pagination skip
     * @param string $orderBy Order the results on given field
     *
     * @return $this
     */
    public function findBy(array $filter = array(), int $take = 120, int $skip = 0, string $orderBy = 'id')
    {

        $conditions = [];

        // check if the requested field exists for this model
        foreach ($filter as $field => $value) {
            if (!in_array($field, ['query']) && !array_key_exists($field, get_object_vars($this))) {
                throw new \Exception("`" . $field . "` is not a recognized property.");
            } else if ($value !== null) {
                if ($field == 'query') {
                    $q = Core\Database::escape(trim($value));
                    $searchable = [];
                    foreach (self::getConfig('searchable') as $fieldName) {
                        $searchable[] = "$fieldName LIKE '%$q%'";
                    }
                    if (count($searchable) > 0) {
                        $conditions[] = "(" . implode(' OR ', $searchable) . ")";
                    }
                } else {
                    $conditions[] = "`" . $field . "` = '" . Core\Database::escape($value) . "'";
                }
            }
        }

        // check for orderBy
        $orderBy = $this -> parseOrderBy($orderBy);

        $query = " SELECT * "
                . "FROM " . self::getConfig('table') . " "
                . "WHERE 1=1 " . ((count($conditions)) ? ' AND ' . implode(' AND ', $conditions) : "") . " "
                . ((self::getConfig('softDelete')) ? " AND " . self::getConfig('table') . ".deleted_at IS NULL " : " ")
                . "ORDER BY {$orderBy -> field} {$orderBy -> direction} "
                . "LIMIT $take OFFSET $skip;";

        $result = Core\Database::query($query);
        if ($take && $take === 1) {
            if ($result -> num_rows < 1) {
                return null;
            } else {
                return $this -> map($result -> fetch_assoc());
            }
        } else {
            $response = [];
            while ($row = $result -> fetch_assoc()) {
                $response[] = (new $this($row));
            }
            return $response;
        }
    }

    /**
     * Find or create an item based on a specific field and value
     *
     * @param string $field
     * @param mised $value
     *
     * @return type
     *
     * @throws \Exception
     */
    public function findOrCreate(string $field, $value)
    {
        // check if already exists
        $item = (new static) -> findBy([$field => $value], $take = 1);
        if (!$item) {

            $item = (new static);

            $fieldSetter = 'set' . str_replace('_', '', ucwords($field, '_'));
            // check if the setter exists and is callable
            if (!is_callable(array($item, $fieldSetter))) {
                throw new \Exception("$fieldSetter is not a known setter.");
            }

            // execute the setter
            call_user_func(array($item, $fieldSetter), $value);

            $item -> insert();
        }
        return $item;
    }

    /**
     * Get the requested order and field from the given orderBy string
     *
     * @param string $orderBy
     *
     * @return stdClass
     */
    public function parseOrderBy(string $orderBy)
    {
        // check for direction
        switch (substr($orderBy, 0, 1)) {
            case '+':
                $dir = 'ASC';
                $field = substr($orderBy, 1);
                break;
            case '-':
                $dir = 'DESC';
                $field = substr($orderBy, 1);
                break;
            default:
                $dir = 'ASC';
                $field = substr($orderBy, 0);
                break;
        }

        if (!in_array($field, self::getConfig('orderable'))) {
            throw new \Exception("`" . $field . "` is not listed as orderable property.");
        }

        return (object) [
                    'field'     => $field,
                    'direction' => $dir,
        ];
    }

    /**
     * Insert Model
     *
     * @return $this
     */
    public function insert()
    {

        // set timestamps
        if (self::getConfig('timestamps')) {
            $this
                    -> setCreatedAt(date('Y-m-d H:i:s'))
                    -> setUpdatedAt(date('Y-m-d H:i:s'));
        }

        // This should be modified to be a bit more secure, but normally public
        // properties will be filtered out, as well as the attributes property.
        foreach (get_object_vars($this) as $key => $value) {
            if (!in_array($key, ['attributes', 'relations', 'resources']) && isset($value) && is_callable(array($this, 'get' . str_replace('_', '', ucwords($key, '_'))))) {
                $keys[] = '`' . Core\Database::escape($key) . '`';

                if ($value instanceof \DateTime) {
                    // convert to string
                    $value = $value -> format('Y-m-d H:i:s');
                }
                $values[] = trim(Core\Database::escape($value));
            }
        }

        // Do more checks here for security..

        $query = " INSERT "
                . "INTO " . self::getConfig('table') . " (" . implode(",", $keys) . ") "
                . "VALUES ('" . implode("','", $values) . "');";

        // replace nulls with real nulls (for ex. deleted_at)
        $query = str_replace("'(null)'", "NULL", $query);

        Core\Database::query($query);

        // Get the ID and add it to the model response
        $this -> setId(Core\Database::getId());

        $this -> addAttribute('is_new', true);


        return $this;
    }

    /**
     * Update Model
     *
     * @return $this
     */
    public function update()
    {
        // set timestamps
        if (self::getConfig('timestamps')) {
            $this -> setUpdatedAt(date('Y-m-d H:i:s'));
        }

        // This should be modified to be a bit more secure, but normally public
        // properties will be filtered out, as well as the attributes property.
        foreach (get_object_vars($this) as $key => $value) {
            if (!in_array($key, ['attributes', 'relations', 'resources']) && (!empty($value) || is_numeric($value) || is_bool($value)) && isset($value) && is_callable(array($this, 'get' . str_replace('_', '', ucwords($key, '_'))))) {
                $update[] = '`' . Core\Database::escape($key) . '`' . " = '" . trim(Core\Database::escape($value)) . "'";
            }
        }

        $query = " UPDATE " . self::getConfig('table') . " "
                . "SET " . implode(",", $update) . " "
                . "WHERE `" . self::getConfig('primaryKey') . "` = " . Core\Database::escape($this -> getId()) . ";";

        // replace nulls with real nulls (for ex. deleted_at)
        $query = str_replace("'(null)'", "NULL", $query);

        Core\Database::query($query);

        return $this;
    }

    /**
     * Delete Model
     *
     * $param boolean $hardDelete Force hard-delete
     *
     * @return $this
     */
    public function delete($hardDelete = false)
    {

        // set timestamps
        if (self::getConfig('timestamps') && self::getConfig('softDelete') && !$hardDelete) {
            $this -> setDeletedAt(date('Y-m-d H:i:s')) -> update();
        } else {
            $query = "  DELETE "
                    . " FROM " . self::getConfig('table')
                    . " WHERE `" . self::getConfig('primaryKey') . "` = " . Core\Database::escape($this -> getId()) . ";";

            Core\Database::query($query);
        }

        $this -> addAttribute('is_deleted', true);

        return $this;
    }

    /**
     * Link source and target ID's based on linkable fields
     *
     * @param int $sourceId
     * @param int $targetId
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function link(int $sourceId, int $targetId)
    {
        $linkable = self::getConfig('linkable');
        if (!$linkable) {
            throw new \Exception('Model is not linkable.');
        }

        // check if already linked
        $link = (new static) -> findBy([$linkable[0] => $sourceId, $linkable[1] => $targetId], $take = 1);
        if (!$link) {

            $link = (new static);

            $sourceSetter = 'set' . str_replace('_', '', ucwords($linkable[0], '_'));
            // check if the setter exists and is callable
            if (!is_callable(array($link, $sourceSetter))) {
                throw new \Exception("$sourceSetter is not a known setter.");
            }

            // execute the setter
            call_user_func(array($link, $sourceSetter), $sourceId);

            $targetSetter = 'set' . str_replace('_', '', ucwords($linkable[1], '_'));
            // check if the setter exists and is callable
            if (!is_callable(array($link, $targetSetter))) {
                throw new \Exception("$targetSetter is not a known setter.");
            }

            // execute the setter
            call_user_func(array($link, $targetSetter), $targetId);

            $link -> insert();
        }

        return $link;
    }

}
