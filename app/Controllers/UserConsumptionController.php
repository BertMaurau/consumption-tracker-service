<?php

namespace ConsumptionTracker\Controllers;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Config AS Config;
use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Modules AS Modules;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserConsumptionController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = '\ConsumptionTracker\\Models\\User\\' . "UserConsumption";

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

        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'item_id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'volume', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'notes', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        // check if item exts
        $item = (new Models\Item) -> getById($filteredInput['item_id']);
        if (!$item) {
            return Core\Output::ModelNotFound($response, 'Item', $filteredInput['item_id']);
        }

        try {
            $userConsumption = Models\User\UserConsumption::create($userId, [
                        'item_id' => $item -> getId(),
                        'volume'  => $filteredInput['volume'],
                        'notes'   => $filteredInput['notes'] ?? null,
            ]);
        } catch (\Exception $ex) {
            return Core\Output::ServerError($response, $ex -> getMessage());
        }


        return Core\Output::OK($response, $userConsumption);
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

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        $userConsumption = (new Models\User\UserConsumption) -> findBy(['id' => $filteredInput['id']], $take = 1);
        if (!$userConsumption) {
            return Core\Output::ModelNotFound($response, 'UserConsumption', $filteredInput['id']);
        }

        if ($userConsumption -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $userConsumption -> expand(['item']);

        return Core\Output::OK($response, $userConsumption);
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
