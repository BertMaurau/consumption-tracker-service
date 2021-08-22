<?php

namespace ConsumptionTracker\Models;

use ConsumptionTracker\Core as Core;

/**
 * Description of LogCron
 *
 * @author Bert Maurau
 */
class LogCron extends BaseModel
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
        'table'             => 'log_crons',
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
     * CRON ID
     * @var int
     */
    public $cron_id;

    /**
     * Get CRON ID
     *
     * @return int
     */
    public function getCronId()
    {
        return $this -> cron_id;
    }

    /**
     * Set CRON ID
     * @param int $cronId
     * @return $this
     */
    public function setCronId($cronId)
    {
        $this -> cron_id = (int) $cronId;
        return $this;
    }

    /**
     * Output
     * @var string
     */
    public $output;

    /**
     * Get Output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this -> output;
    }

    /**
     * Set Output
     *
     * @param string $output
     *
     * @return $this
     */
    public function setArguments($output)
    {
        $this -> output = (string) $output;
        return $this;
    }

    /**
     * Started At
     * @var \DateTime
     */
    public $started_at;

    /**
     * Get Started At
     *
     * @return \DateTime
     */
    public function getStartedAt(): \DateTime
    {
        return $this -> started_at;
    }

    /**
     * Set Started At
     *
     * @param \DateTime $startedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setStartedAt($startedAt)
    {
        $this -> started_at = $startedAt;
        if ($startedAt && is_string($startedAt)) {
            try {
                $dt = new \DateTime($startedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (LogCron::startedAt).");
            }
            $this -> started_at = $dt;
        }
        return $this;
    }

    /**
     * Ended At
     * @var \DateTime
     */
    public $ended_at;

    /**
     * Get Ended At
     *
     * @return \DateTime
     */
    public function getEndedAt(): \DateTime
    {
        return $this -> ended_at;
    }

    /**
     * Set Ended At
     *
     * @param \DateTime $endedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setEndedAt($endedAt)
    {
        $this -> ended_at = $endedAt;
        if ($endedAt && is_string($endedAt)) {
            try {
                $dt = new \DateTime($endedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (LogCron::endedAt).");
            }
            $this -> ended_at = $dt;
        }
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */
}
