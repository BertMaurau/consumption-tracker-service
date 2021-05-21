<?php

namespace ConsumptionTracker\Controllers;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Config AS Config;
use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Modules AS Modules;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = '\ConsumptionTracker\\Models\\' . "User";

    /**
     * Register a new User
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'device', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'browser', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        $user = (new Models\User) -> findBy(['email' => $filteredInput['email']], 1);
        if ($user) {
            return Core\Output::Conflict($response, 'User already exists with email `' . $filteredInput['email'] . '`');
        }

        try {
            $user = Models\User::create($filteredInput['email'], $filteredInput['password']);
        } catch (\Exception $ex) {
            return Core\Output::ServerError($response, $ex -> getMessage());
        }

        // generate access token
        $user -> addAccessToken($filteredInput['device'] ?? null, $filteredInput['browser'] ?? null);

        return Core\Output::OK($response, $user);
    }

    /**
     * Validate the given login
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'device', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'browser', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $user = (new Models\User) -> findBy(['email' => $filteredInput['email']], 1);
        if (!$user) {
            return Core\Output::NotFound($response, 'User not found for email `' . $filteredInput['email'] . '`');
        }

        if (!$user -> validatePassword($filteredInput['password'])) {
            return Core\Output::NotAuthorized($response, 'Invalid password.');
        }

        // generate access token
        $user -> addAccessToken($filteredInput['device'] ?? null, $filteredInput['browser'] ?? null);

        return Core\Output::OK($response, $user);
    }

    /**
     * Disable the current auth token
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'device', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'browser', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        // get the model and destory it
        $userAuthToken = (new Models\User\UserToken) -> findBy(['user_id' => $userId, 'uid' => Core\Auth::getTokenUid()], $take = 1);
        if (!$userAuthToken) {
            // should..not..happen?
            return Core\Output::ModelNotFound($response, 'UserAuthToken', Core\Auth::getTokenUid());
        }

        $userAuthToken -> setIsActive(false) -> setIsDestroyed(true) -> update();

        return Core\Output::OK($response, $userAuthToken);
    }

    /**
     * Show the currently authenticated user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        return Core\Output::OK($response, $user);
    }

    /**
     * Update the currently authenticated user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'first_name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'last_name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // Get the POST body and filter out the non-updatables
        $postdata = array_intersect_key($filteredInput, array_flip(Models\User::getConfig('updatable')));

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        // map the values to the model and upate
        $user -> map($postdata) -> update();

        // output the item with its updated values
        return Core\Output::OK($response, $user);
    }

    /**
     * Delete the currently authenticated user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        if (!$user -> validatePassword($filteredInput['password'])) {
            return Core\Output::NotAuthorized($response, 'Invalid password.');
        }

        $user -> delete();

        return Core\Output::OK($response, ['deleted_at' => $user -> getDeletedAt()]);
    }

    /**
     * Update the currently authenticated user's avatar
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function avatar(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // check for auth id
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'base64_string', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        try {
            Core\Image::getFromBase64($filteredInput['base64_string'], Models\User::getConfig('imageDirectory'), $user -> getGuid(), 512);
        } catch (\Exception $ex) {
            return Core\Output::ServerError($response, "Something went wrong while processing the Avatar data.");
        }

        // return updated resource
        return Core\Output::NoContent($response);
    }

}
