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

use \Psr\Http\Message\ResponseInterface;

/**
 * Description of Output
 *
 * Handles everything concerning the actual data output.
 *
 * @author Bert Maurau
 */
class Output
{

    // HTTP Codes
    const CODE_NOT_FOUND = 404;
    const CODE_CONFLICT = 406;
    const CODE_MISSING_PARAMETER = 406;
    const CODE_VALIDATION_FAILED = 406;
    const CODE_DISABLED = 409;
    const CODE_OK = 200;
    const CODE_NOT_AUTHORIZED = 401;
    const CODE_UNPROCESSABLE = 422;
    const CODE_ERROR = 500;
    const CODE_NO_CONTENT = 204;
    const CODE_LOCKED = 423;
    const CODE_BAD_REQUEST = 400;

    /**
     * JSON Output
     * @param Response $response
     * @param integer $code
     * @param any $data
     * @return Response
     */
    public static function JSON($response, $code, $data): ResponseInterface
    {
        // maybe add some stuffs here
        // like extra validation, parameters, attributes, ..
        // actual output
        $response -> getBody()
                // write the output
                -> write(json_encode($data));
        // set the HTTP_CODE
        return $response -> withStatus($code);
    }

    /**
     * Init a new response object
     * @return \Zend\Diactoros\Response
     */
    public static function Clear(): ResponseInterface
    {
        return new \Zend\Diactoros\Response();
    }

    /**
     * Return OK status
     * @param Response $response
     * @param any $data
     * @return JSON Output
     */
    public static function OK(ResponseInterface $response, $data): ResponseInterface
    {
        return self::JSON($response, self::CODE_OK, $data);
    }

    /**
     * Return Model Not Found status
     * @param Response $response
     * @param string $modelName
     * @param any $modelId
     * @return JSON Output
     */
    public static function ModelNotFound(ResponseInterface $response, string $modelName, $modelId, string $parameter = 'id'): ResponseInterface
    {
        return self::JSON($response, self::CODE_NOT_FOUND, array(
                    'code'    => self::CODE_NOT_FOUND,
                    'message' => $modelName . ' with `' . $parameter . '` ' . $modelId . ' not found!'));
    }

    /**
     * Return Not Found status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function NotFound(ResponseInterface $response, string $message, string $field = null, $value = null): ResponseInterface
    {
        // return custom message
        if ($message && !$field && !$value) {
            return self::JSON($response, self::CODE_NOT_FOUND, array(
                        'code'    => self::CODE_NOT_FOUND,
                        'message' => $message));
        } else {
            return self::JSON($response, self::CODE_NOT_FOUND, array(
                        'code'    => self::CODE_NOT_FOUND,
                        'message' => "Model `$message` with `$value` as `$field` not found."));
        }
    }

    /**
     * Return Error status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function ServerError(ResponseInterface $response, string $message): ResponseInterface
    {
        return self::JSON($response, self::CODE_ERROR, array(
                    'code'    => self::CODE_ERROR,
                    'message' => $message));
    }

    /**
     * Return Missing Parameter status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function MissingParameter($response, $message): ResponseInterface
    {
        return self::JSON($response, self::CODE_MISSING_PARAMETER, array(
                    'code'    => self::CODE_MISSING_PARAMETER,
                    'message' => "Missing payload property: `$message`."));
    }

    public static function Locked(ResponseInterface $response, string $message): ResponseInterface
    {
        return self::JSON($response, self::CODE_LOCKED, array(
                    'code'    => self::CODE_LOCKED,
                    'message' => $message));
    }

    /**
     * Return Conflict status (should be  not-acceptable)
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function Conflict(ResponseInterface $response, string $message): ResponseInterface
    {
        return self::JSON($response, self::CODE_CONFLICT, array(
                    'code'    => self::CODE_CONFLICT,
                    'message' => $message));
    }

    /**
     * Return No Content response
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function NoContent(ResponseInterface $response): ResponseInterface
    {
        return self::JSON($response, self::CODE_NO_CONTENT, null);
    }

    /**
     * Return Disabled resource
     * @param type $response
     * @param type $message
     * @return type
     */
    public static function DisabledResource(ResponseInterface $response, string $message): ResponseInterface
    {
        return self::JSON($response, self::CODE_DISABLED, array(
                    'code'    => self::CODE_DISABLED,
                    'message' => $message));
    }

    /**
     * Return Unprocessable Entity status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function InvalidParameter(ResponseInterface $response, string $parameter): ResponseInterface
    {
        return self::JSON($response, self::CODE_UNPROCESSABLE, array(
                    'code'    => self::CODE_UNPROCESSABLE,
                    'message' => "Incorrect value/type for parameter `$parameter`."));
    }

    /**
     * Return Conflict status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function ValidationFailed(ResponseInterface $response, string $message): ResponseInterface
    {
        return self::JSON($response, self::CODE_VALIDATION_FAILED, array(
                    'code'    => self::CODE_VALIDATION_FAILED,
                    'message' => $message));
    }

    /**
     * Return Missing Model Id status
     * @param Response $response
     * @return JSON Output
     */
    public static function MissingModelId(ResponseInterface $response): ResponseInterface
    {
        return self::JSON($response, self::CODE_MISSING_PARAMETER, array(
                    'code'    => self::CODE_MISSING_PARAMETER,
                    'message' => "Missing ModelID {id} parameter!"));
    }

    /**
     * Return Not Authorized status
     * @param Response $response
     * @return JSON Output
     */
    public static function NotAuthorized(ResponseInterface $response, string $message = null): ResponseInterface
    {
        return self::JSON($response, self::CODE_NOT_AUTHORIZED, array(
                    'code'    => self::CODE_NOT_AUTHORIZED,
                    'message' => ($message) ?: 'You are not authorized to make this request.'));
    }

    /**
     * Return Bad Request status
     * @param Response $response
     * @return JSON Output
     */
    public static function BadRequest(ResponseInterface$response, string $message = null): ResponseInterface
    {
        return self::JSON($response, self::CODE_BAD_REQUEST, array(
                    'code'    => self::CODE_BAD_REQUEST,
                    'message' => ($message) ?: 'Bad request.'));
    }

}
