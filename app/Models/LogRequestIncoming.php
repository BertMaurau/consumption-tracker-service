<?php

namespace ConsumptionTracker\Models;

use ConsumptionTracker\Core as Core;

/**
 * Description of LogRequestIncoming
 *
 * @author Bert Maurau
 */
class LogRequestIncoming extends BaseModel
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
        'table'             => 'log_requests_incoming',
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
     * Verb
     * @var string
     */
    public $verb;

    /**
     * Get Verb
     *
     * @return string
     */
    public function getVerb()
    {
        return $this -> verb;
    }

    /**
     * Set verb
     * @param string $verb
     * @return $this
     */
    public function setVerb($verb)
    {
        $this -> verb = (string) $verb;
        return $this;
    }

    /**
     * URI
     * @var string
     */
    public $uri;

    /**
     * Get Uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this -> uri;
    }

    /**
     * Set uri
     *
     * @param string $uri
     *
     * @return $this
     */
    public function setUri($uri)
    {
        $this -> uri = (string) $uri;
        return $this;
    }

    /**
     * Payload
     * @var array
     */
    public $payload;

    /**
     * Get Payload
     *
     * @return string
     */
    public function getPayload()
    {
        return $this -> payload;
    }

    /**
     * Set payload
     *
     * @param string $payload
     *
     * @return $this
     */
    public function setPayload($payload)
    {
        $this -> payload = (string) $payload;
        return $this;
    }

    /**
     * Headers
     * @var array
     */
    public $headers;

    /**
     * Get Headers
     *
     * @return string
     */
    public function getHeaders()
    {
        return $this -> headers;
    }

    /**
     * Set headers
     *
     * @param string $headers
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this -> headers = (string) $headers;
        return $this;
    }

    /**
     * Remote Address
     * @var string
     */
    public $remote_address;

    /**
     * Get Remote Address
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this -> remote_address;
    }

    /**
     * Set remote_address
     *
     * @param string $remoteAddress
     *
     * @return $this
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this -> remote_address = (string) $remoteAddress;
        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */

    /**
     * Register the incoming request
     *
     * @return void
     */
    public static function create()
    {

        // get the info about the request
        $verb = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if ($verb == 'POST' && (strpos($uri, '/upload') > -1 || strpos($uri, '/logo') > -1 || strpos($uri, '/avatar') > -1 )) {
            // do not try to process input
            $payload = '--CLEANED--';
        } else {
            $payload = file_get_contents('php://input');
        }
        $rawHeaders = self::getAllHeaders();
        $headersJson = json_encode($rawHeaders);
        $remoteAddress = Core\Auth::getRemoteAddress();

        // cleanup excessive payloads
        if (strpos($payload, 'password') !== false) {
            $payload = '--SCREENED--';
        }
        if (strpos($payload, 'base64') !== false) {
            $payload = '--CLEANED--';
        }

        $log = (new self)
                -> setVerb($verb)
                -> setUri($uri)
                -> setPayload($payload)
                -> setHeaders($headersJson)
                -> setRemoteAddress($remoteAddress)
                -> insert();

        Core\Auth::setLogRequestIncoming($log);
        return;
    }

    /**
     * Get all headers
     *
     * @return array
     */
    private static function getAllHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

}
