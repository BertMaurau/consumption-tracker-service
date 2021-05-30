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
        'updatable'         => [
            'volume'      => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'volume', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            'consumed_at' => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'consumed_at', 'type' => Core\ValidatedRequest::TYPE_TIMESTAMP, 'required' => false,],
        ],
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
            'item_id' => ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'item_id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
        ],
        /**
         * List of properties that are allowed to be ordered on
         */
        'orderable'         => [
            'id', 'item_id', 'volume', 'consumed_at', 'created_at', 'updated_at'
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
     * @param string $consumedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setConsumedAt($consumedAt)
    {
        $this -> consumed_at = $consumedAt;
        if ($consumedAt && is_string($consumedAt)) {
            try {
                $dt = new \DateTime($consumedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (UserConsumption::consumedAt).");
            }
            $this -> consumed_at = $dt;
        }

        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */

    /**
     * Register a new Consumption
     *
     * @param int $userId
     * @param array $properties
     *
     * @return type
     */
    public static function create(int $userId, array $properties = [])
    {

        // create the record
        $userConsumption = (new self)
                -> setUserId($userId)
                -> setItemId($properties['item_id'] ?? null)
                -> setVolume($properties['volume'] ?? null)
                -> setNotes($properties['notes'] ?? null)
                -> setConsumedAt($properties['consumed_at'] ?? null)
                -> insert();

        return $userConsumption;
    }

    /**
     * Get the data for a Chart representation
     *
     * @param int $userId
     * @param array $config
     * @param array $filter
     * @param string $timezone
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getChartData(int $userId, array $config = [], array $filter = [], string $timezone = 'UTC')
    {

        $rawOffset = '+00:00';
        if ($timezone != 'UTC') {
            $dt = new \DateTime("now", new \DateTimeZone($timezone));
            $rawOffset = $dt -> format('P');
        }

        $groupBy = $config['group'] ?? 'day';
        $period = $config['period'] ?? 'last-7-days';
        $from = $config['from'] ?? null;
        $until = $config['until'] ?? null;

        // defaults
        if (!in_array($groupBy, ['day', 'week', 'month'])) {
            throw new \Exception('Invalid `' . $groupBy . '` is not a valid group-type (day|week|month).');
        }

        // check period
        if ($period && $period == 'custom') {
            if (!$from) {
                throw new \Exception('Custom period requires `from` as argument.');
            } else {
                // validate date
                try {
                    $dtFrom = new \DateTime($from);
                } catch (\Exception $ex) {
                    throw new \Exception('Given `from` value is not a valid date or timestamp.');
                }
            }

            if (!$until) {
                throw new \Exception('Custom period requires `until` as argument.');
            } else {
                // validate date
                try {
                    $dtUntil = new \DateTime($until);
                } catch (\Exception $ex) {
                    throw new \Exception('Given `until` value is not a valid date or timestamp.');
                }
            }

            if ($dtUntil < $dtFrom) {
                throw new \Exception('The `until`-date must be higher than the `from`-date.');
            }
        } else {

            // check period and build the two dates
            switch ($period) {
                case 'this-week':
                    $dtFrom = new \DateTime('monday this week', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime('sunday this week', new \DateTimeZone($timezone));
                    break;
                case 'this-month':
                    $dtFrom = new \DateTime('first day of this month', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime('last day of this month', new \DateTimeZone($timezone));
                    break;
                case 'this-year':
                    $dtFrom = new \DateTime('first day of this year', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime('last day of this year', new \DateTimeZone($timezone));
                    break;

                case 'last-7-days':
                    $dtFrom = new \DateTime('7 days ago', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime("now", new \DateTimeZone($timezone));
                    break;
                case 'last-30-days':
                    $dtFrom = new \DateTime('30 days ago', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime("now", new \DateTimeZone($timezone));
                    break;
                case 'last-3-months':
                    $dtFrom = new \DateTime('3 months ago', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime("now", new \DateTimeZone($timezone));
                    break;
                case 'last-12-months':
                    $dtFrom = new \DateTime('12 months ago', new \DateTimeZone($timezone));
                    $dtUntil = new \DateTime("now", new \DateTimeZone($timezone));
                    break;
                default:
                    throw new \Exception('Given `period` value is not a recognized period.');
            }
        }

        // get the data
        $datasets = [];

        // build group-by statement
        switch ($groupBy) {
            case 'week':
                $groupByLabel = 'YEARWEEK(_calendar.date, 3)';
                $selectLabel = 'CONCAT(YEAR(_calendar.date), \'-W\', LPAD(WEEK(_calendar.date), 2, 0))';
                $groupByDataset = 'YEARWEEK(dataset.day_reference, 3)';
                $selectDataset = 'CONCAT(YEAR(dataset.day_reference), \'-W\', LPAD(WEEK(dataset.day_reference), 2, 0))';
                break;
            case 'month':
                $groupByLabel = 'YEAR(_calendar.date), MONTH(_calendar.date)';
                $selectLabel = 'CONCAT(YEAR(_calendar.date), \'-\', LPAD(MONTH(_calendar.date), 2, 0))';
                $groupByDataset = 'YEAR(dataset.day_reference), MONTH(dataset.day_reference)';
                $selectDataset = 'CONCAT(YEAR(dataset.day_reference), \'-\', LPAD(MONTH(dataset.day_reference), 2, 0))';
                break;
            default:
                $groupByLabel = 'DATE(_calendar.date)';
                $selectLabel = $groupByLabel;
                $groupByDataset = 'DATE(dataset.day_reference)';
                $selectDataset = $groupByDataset;
                break;
        }

        // build date/reference list for labels + empty placeholders
        $dateRange = [];
        $query = "
            SELECT
                $selectLabel AS reference
            FROM _calendar
            WHERE   _calendar.date  >= '" . $dtFrom -> format('Y-m-d') . "'
                AND _calendar.date  <= '" . $dtUntil -> format('Y-m-d') . "'
            GROUP BY $groupByLabel
            ;";
        $result = Core\Database::query($query);
        while ($row = $result -> fetch_assoc()) {
            $reference = $row['reference'];

            // use the labels to generate date range
            $dateRange[$reference] = ['name' => $reference, 'value' => 0];
        }

        // so at this point we have a list of labels (count of items) we expect
        // to have and can use to populate the empty range.

        $query = "

            SELECT
                items.description       AS item_description,
                dataset.item_id         AS item_id,
                SUM(dataset.volume)     AS volume,
                dataset.day_reference   AS day_reference,
                $selectDataset          AS reference
            FROM (
                SELECT
                  item_id, SUM(volume) AS volume, DATE(CONVERT_TZ(user_consumptions.consumed_at, '+00:00', '$rawOffset')) AS day_reference
                FROM user_consumptions
                WHERE user_consumptions.user_id = " . Core\Database::escape($userId) . "
                    AND user_consumptions.deleted_at IS NULL
                    AND DATE(CONVERT_TZ(user_consumptions.consumed_at, '+00:00', '$rawOffset')) >= '" . $dtFrom -> format('Y-m-d') . "'
                    AND DATE(CONVERT_TZ(user_consumptions.consumed_at, '+00:00', '$rawOffset')) <= '" . $dtUntil -> format('Y-m-d') . "'
                GROUP BY item_id, DATE(CONVERT_TZ(user_consumptions.consumed_at, '+00:00', '$rawOffset'))
            ) AS dataset
            LEFT JOIN items ON items.id = dataset.item_id

            GROUP BY dataset.item_id, $groupByDataset
            ORDER BY item_id;
            ";
        $result = Core\Database::query($query);

        while ($row = $result -> fetch_assoc()) {

            $itemId = (int) $row['item_id'];
            $itemDescription = $row['item_description'];
            $volume = (int) $row['volume'];
            $reference = $row['reference'];

            // create dataset per item
            if (!isset($datasets[$itemId])) {
                $datasets[$itemId] = [
                    'name'    => $itemDescription,
                    'item_id' => $itemId,
                    'series'  => $dateRange, // use the daterange as empty list
                ];
            }

            // set the current reference
            $datasets[$itemId]['series'][$reference] = ['name' => $reference, 'value' => (int) round(($volume ?? null) ?: 0)];
        }

        // remove the key-value association
        foreach ($datasets as $keyItemId => $valueItemData) {
            // strip keys from label-value array
            $datasets[$keyItemId]['series'] = array_values($valueItemData['series']);
        }

        // strip keys from lead-dataset array
        $datasets = array_values($datasets);

        return $datasets;
    }

    public function getSummary(int $userId, string $timezone = 'UTC')
    {

        $rawOffset = '+00:00';
        if ($timezone != 'UTC') {
            $dt = new \DateTime("now", new \DateTimeZone($timezone));
            $rawOffset = $dt -> format('P');
        }

        // get the data
        $dataset = [];
        $totalVolume = 0;

        $query = "
            SELECT
                items_categories.description AS category_description,
                items.description AS item_description,
                items.id AS item_id,
                items_categories.id AS category_id,
                SUM(_dataset.volume) AS total_consumption,
                AVG(_dataset.volume) AS avg_volume
            FROM (
                SELECT SUM(volume) AS volume, item_id
                FROM user_consumptions WHERE user_id = " . Core\Database::escape($userId) . " AND deleted_at IS NULL
                GROUP BY item_id, DATE(CONVERT_TZ(user_consumptions.consumed_at, '+00:00', '$rawOffset'))
            ) AS _dataset
            LEFT JOIN items ON items.id = _dataset.item_id
            LEFT JOIN items_categories ON items_categories.id = items.item_category_id
            GROUP BY _dataset.item_id;
            ";

        $result = Core\Database::query($query);

        while ($row = $result -> fetch_assoc()) {

            $itemDescription = $row['item_description'];
            $itemId = (int) $row['item_id'];
            $categoryDescription = $row['category_description'];
            $categoryId = (int) $row['category_id'];

            $volumeTotal = (double) $row['total_consumption'];
            $volumeAvg = (double) $row['avg_volume'];

            // create dataset per category
            if (!isset($dataset[$categoryDescription])) {
                $dataset[$categoryDescription] = [
                    'id'           => $categoryId,
                    'description'  => $categoryDescription,
                    'total_volume' => 0,
                    'items'        => [],
                ];
            }

            // set the current reference
            $dataset[$categoryDescription]['items'][] = [
                'id'           => $itemId,
                'description'  => $itemDescription,
                'total_volume' => $volumeTotal,
                'avg_volume'   => round($volumeAvg),
            ];

            $dataset[$categoryDescription]['total_volume'] += $volumeTotal;

            $totalVolume += $volumeTotal;
        }

        // strip keys from lead-dataset array
        $dataset = array_values($dataset);

        // sort categories by volume
        usort($dataset, function($a, $b) {
            return $b['total_volume'] - $a['total_volume'];
        });

        return [
            'total'      => $totalVolume,
            'categories' => $dataset,
        ];
    }

}
