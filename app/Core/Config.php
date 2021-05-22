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

namespace ConsumptionTracker\Core;

/**
 * Description of Config
 *
 * This handles everything concerning the configuration.
 *
 * @author Bert Maurau
 */
class Config
{

    /**
     * The Instance
     * @var Config
     */
    private static $instance;

    /**
     * Database
     * @var stdClass
     */
    private $database;

    /**
     * API
     * @var stdClass
     */
    private $api;

    /**
     * SMTP
     * @var stdClass
     */
    private $smtp;

    /**
     * Salts
     * @var stdClass
     */
    private $salts;

    /**
     * Paths
     * @var stdClass
     */
    private $paths;

    /**
     * API Tokens
     * @var stdClass
     */
    private $apiTokens;

    public function __construct()
    {
        // load the .env
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../env/');
        $dotenv -> load();

        // load the api-config
        $this -> api = (object) [
                    'root'    => getenv('API_ROOT'),
                    'baseUrl' => getenv('BASE_URL'),
                    'domain'  => getenv('DOMAIN'),
                    'webApp'  => getenv('WEB_APP'),
                    'env'     => getenv('ENV'),
        ];

        // load the databse-config
        $this -> database = (object) [
                    'host'    => getenv('DATABASE_HOST'),
                    'user'    => getenv('DATABASE_USER'),
                    'pass'    => getenv('DATABASE_PASS'),
                    'name'    => getenv('DATABASE_NAME'),
                    'charset' => getenv('DATABASE_CHARSET'),
        ];

        // load the databse-config
        $this -> smtp = (object) [
                    'host'   => getenv('SMTP_HOST'),
                    'user'   => getenv('SMTP_USER'),
                    'pass'   => getenv('SMTP_PASS'),
                    'secure' => getenv('SMTP_SECURE'),
                    'port'   => getenv('SMTP_PORT'),
        ];

        // load the salts-config
        $this -> salts = (object) [
                    'token'    => getenv('JWT_SECRET'),
                    'password' => getenv('PASSWORD_SALT'),
        ];

        // load the paths-config
        $this -> paths = (object) [
                    'statics'           => __DIR__ . '/..' . getenv('PATH_STATICS'),
                    'statics_url'       => getenv('BASE_URL') . getenv('PATH_STATICS'),
                    'images'            => __DIR__ . '/../..' . getenv('PATH_STATICS_IMAGES'),
                    'imagesUrl'         => getenv('BASE_URL') . getenv('PATH_STATICS_IMAGES'),
                    'mailTemplates'     => __DIR__ . '/../..' . getenv('PATH_STATICS_MAIL_TEMPLATES'),
                    'mailTemplates_url' => getenv('BASE_URL') . getenv('PATH_STATICS_MAIL_TEMPLATES'),
                    'baseUrl'           => getenv('BASE_URL'),
        ];

        // load the apiTokens-config
        $this -> apiTokens = (object) [
                    'omdbApi' => getenv('TOKEN_OMDB_API'),
        ];
    }

    /**
     * Create a new instance
     * @return instance
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Return the database-config
     * @return object
     */
    public function Database()
    {
        return $this -> database;
    }

    /**
     * Return the api-config
     * @return object
     */
    public function API()
    {
        return $this -> api;
    }

    /**
     * Return the smtp-config
     * @return object
     */
    public function SMTP()
    {
        return $this -> smtp;
    }

    /**
     * Return the salts-config
     * @return object
     */
    public function Salts()
    {
        return $this -> salts;
    }

    /**
     * Return the paths-config
     * @return object
     */
    public function Paths()
    {
        return $this -> paths;
    }

    /**
     * Return the apiTokens-config
     * @return object
     */
    public function APITokens()
    {
        return $this -> apiTokens;
    }

    /**
     * Get the protocol
     * @return string
     */
    private function getServerProtocol()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    }

    /**
     * Get the current domain
     * @return string
     */
    private function getServerDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

}
