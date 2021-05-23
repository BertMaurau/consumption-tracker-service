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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ValidatedRequest
 *
 * @author bertmaurau
 */
class ValidatedRequest
{

    // variable types
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_URL = 'url';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_EMAIL = 'email';
    const TYPE_MIXED = 'mixed';
    const TYPE_JSON = 'json';
    const TYPE_HTML = 'html';
    const TYPE_TIMESTAMP = 'timestamp';
    // variable input methods
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_ARG = 'ARG';

    /**
     * Instance
     * @var ValidatedRequest
     */
    private $instance;

    /**
     * Filtered Input
     * @var array
     */
    private $filteredInput = [];

    /**
     * Is Valid
     * @var bool
     */
    private $isValid = true;

    /**
     * Output
     * @var Output
     */
    private $output;

    /**
     * Validate Request
     * @param ServerRequestInterface $request The RequestInterface
     * @param ResponseInterface $response The ResponseInterface
     * @param array $validationFields List of fields needed
     * @param array $input List of fields provided
     * @return ValidatedRequest
     */
    public static function validate(ServerRequestInterface $request, ResponseInterface $response, array $validationFields = [], array $input = []): ValidatedRequest
    {
        $instance = (new self);

        // Get the POST body
        $payload = json_decode($request -> getBody(), true);

        // iterate the validation fields
        foreach ($validationFields as $key => $validationField) {

            $value = null;
            $addToPayload = false;

            switch ($validationField['method']) {
                case self::METHOD_GET:
                    if (!isset($_GET[$validationField['field']]) && $validationField['required']) {
                        $instance -> isValid = false;
                        $instance -> output = Output::MissingParameter($response, $validationField['field']);
                        return $instance;
                    } else if (isset($_GET[$validationField['field']])) {
                        if ($validationField['type'] === self::TYPE_JSON) {
                            $value = $_GET[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_HTML) {
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_TIMESTAMP) {
                            try {
                                $dt = new \DateTime($_GET[$validationField['field']]);
                            } catch (\Exception $ex) {
                                $instance -> isValid = false;
                                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                                return $instance;
                            }
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_EMAIL) {
                            $value = self::filterInput(INPUT_GET, $validationField['field'], self::getFilterSanitizationType($validationField['type']));
                            if ($_GET[$validationField['field']] != $value || !filter_var($_GET[$validationField['field']], FILTER_VALIDATE_EMAIL)) {
                                $instance -> isValid = false;
                                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                                return $instance;
                            }
                        } else {
                            $value = self::filterInput(INPUT_GET, $validationField['field'], self::getFilterSanitizationType($validationField['type']));
                        }
                        $addToPayload = true;
                    }
                    break;
                case self::METHOD_POST:
                    if (!isset($payload[$validationField['field']]) && $validationField['required']) {
                        $instance -> isValid = false;
                        $instance -> output = Output::MissingParameter($response, $validationField['field']);
                        return $instance;
                    } else if (isset($payload[$validationField['field']])) {
                        if ($validationField['type'] === self::TYPE_JSON) {
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_HTML) {
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_TIMESTAMP) {
                            try {
                                $dt = new \DateTime($payload[$validationField['field']]);
                            } catch (\Exception $ex) {
                                $instance -> isValid = false;
                                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                                return $instance;
                            }
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_EMAIL) {
                            $value = self::filterVar($payload[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                            if ($payload[$validationField['field']] != $value || !filter_var($payload[$validationField['field']], FILTER_VALIDATE_EMAIL)) {
                                $instance -> isValid = false;
                                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                                return $instance;
                            }
                        } else {
                            $value = self::filterVar($payload[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                        }

                        $addToPayload = true;
                    }
                    break;
                case self::METHOD_ARG:
                    if (!isset($input[$validationField['field']]) && $validationField['required']) {
                        $instance -> isValid = false;
                        $instance -> output = Output::MissingParameter($response, $validationField['field']);
                        return $instance;
                    } else if (isset($input[$validationField['field']])) {
                        if ($validationField['type'] === self::TYPE_JSON) {
                            $value = $input[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_HTML) {
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_TIMESTAMP) {
                            try {
                                $dt = new \DateTime($input[$validationField['field']]);
                            } catch (\Exception $ex) {
                                $instance -> isValid = false;
                                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                                return $instance;
                            }
                            $value = $payload[$validationField['field']];
                        } else if ($validationField['type'] === self::TYPE_EMAIL) {
                            $value = self::filterVar($input[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                            if ($input[$validationField['field']] != $value || !filter_var($input[$validationField['field']], FILTER_VALIDATE_EMAIL)) {
                                $instance -> isValid = false;
                                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                                return $instance;
                            }
                        } else {
                            $value = self::filterVar(urldecode($input[$validationField['field']]), self::getFilterSanitizationType($validationField['type']));
                        }
                        $addToPayload = true;
                    }
                    break;
                default:
                    break;
            }

            if (!$value && $validationField['required']) {
                $instance -> isValid = false;
                Output::Clear();
                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                return $instance;
            } else if ($addToPayload) {
                $instance -> filteredInput[$validationField['field']] = (is_string($value) && $validationField['field'] != 'body' ? html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $value);
            }
        }

        return $instance;
    }

    /**
     * Custom function for filter_input to fix issues with FastCGI and server/environment vars
     *
     * @source https://stackoverflow.com/a/36205923/3347968
     *
     * @param int $type
     * @param string $variable_name
     * @param int $filter
     * @param int $options
     *
     * @return mixed
     */
    public static function filterInput(int $type, string $variable_name, int $filter = FILTER_DEFAULT, int $options = NULL)
    {
        $checkTypes = [
            INPUT_GET,
            INPUT_POST,
            INPUT_COOKIE
        ];

        if ($options === NULL) {
            // No idea if this should be here or not
            // Maybe someone could let me know if this should be removed?
            $options = FILTER_NULL_ON_FAILURE;
        }

        $filteredVal = null;
        if (in_array($type, $checkTypes) || filter_has_var($type, $variable_name)) {
            $filteredVal = filter_input($type, $variable_name, $filter, $options);
        } else if ($type == INPUT_SERVER && isset($_SERVER[$variable_name])) {
            $filteredVal = filter_var($_SERVER[$variable_name], $filter, $options);
        } else if ($type == INPUT_ENV && isset($_ENV[$variable_name])) {
            $filteredVal = filter_var($_ENV[$variable_name], $filter, $options);
        }

        return $filteredVal;
    }

    /**
     * Alias function for filter_var (to have some "consistency")
     *
     * @param mixed $variable
     * @param int $filter
     * @param int $options
     *
     * @return mixed
     */
    public static function filterVar($variable, int $filter = FILTER_DEFAULT, int $options = NULL)
    {
        return filter_var($variable, $filter, $options);
    }

    /**
     * Return Is valid
     *
     * @return bool Valid
     */
    public function isValid(): bool
    {
        return $this -> isValid;
    }

    /**
     * Get Filtered Input
     *
     * @return array The filtered Input
     */
    public function getFilteredInput(): array
    {
        return $this -> filteredInput;
    }

    /**
     * Get Output
     *
     * @return ResponseInterface The Output response
     */
    public function getOutput(): ResponseInterface
    {
        return $this -> output;
    }

    /**
     * Get the Sanitizing Type for given variable type
     *
     * @param string $type The type of variable
     *
     * @return int The number of the sanitizing type
     */
    public static function getFilterSanitizationType(string $type): int
    {

        $filterVal = FILTER_DEFAULT;
        switch ($type) {
            case self::TYPE_STRING:
                $filterVal = FILTER_SANITIZE_STRING;
                break;
            case self::TYPE_INTEGER:
                $filterVal = FILTER_SANITIZE_NUMBER_INT;
                break;
            case self::TYPE_FLOAT:
                $filterVal = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
            case self::TYPE_EMAIL:
                $filterVal = FILTER_SANITIZE_EMAIL;
                break;
            case self::TYPE_BOOLEAN:
                $filterVal = FILTER_VALIDATE_BOOLEAN;
                break;
            case self::TYPE_URL:
                $filterVal = FILTER_VALIDATE_URL;
                break;
            case self::TYPE_MIXED:
                $filterVal = FILTER_DEFAULT;
                break;
            default:
                $filterVal = FILTER_DEFAULT;
                break;
        }

        return $filterVal;
    }

}
