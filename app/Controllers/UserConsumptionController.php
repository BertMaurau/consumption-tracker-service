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
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'item_id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'volume', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'notes', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'consumed_at', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        if (isset($filteredInput['userId']) && $filteredInput['userId'] != $userId) {
            return Core\Output::NotAuthorized($response);
        }

        // check if item exts
        $item = (new Models\Item) -> getById($filteredInput['item_id']);
        if (!$item) {
            return Core\Output::ModelNotFound($response, 'Item', $filteredInput['item_id']);
        }

        try {
            $userConsumption = Models\User\UserConsumption::create($userId, [
                        'item_id'     => $item -> getId(),
                        'volume'      => $filteredInput['volume'],
                        'consumed_at' => $filteredInput['consumed_at'] ?? null,
                        'notes'       => $filteredInput['notes'] ?? null,
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
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'orderBy', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'take', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'skip', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'display', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'group', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'period', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'from', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'until', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validationFields = array_merge($validationFields, Models\User\UserConsumption::getConfig('filterable'));

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        if (isset($filteredInput['userId']) && $filteredInput['userId'] != $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $filterable = Models\User\UserConsumption::getConfig('filterable') ?? null;
        if ($filterable) {
            $filter = array_intersect_key($filteredInput, Models\User\UserConsumption::getConfig('filterable'));
        } else {
            $filter = [];
        }
        $filter['user_id'] = $userId;

        $user = (new Models\User) -> getById($userId);
        $timezone = $user ? $user -> getTimezone() : 'UTC';

        $displayAs = isset($filteredInput['display']) && $filteredInput['display'] && in_array($filteredInput['display'], ['chart', 'default', 'summary']) ? $filteredInput['display'] : 'default';
        if ($displayAs == 'chart') {
            $userConsumptions = (new Models\User\UserConsumption()) -> getChartData($userId, $filteredInput, $filter, $timezone);
        } else if ($displayAs == 'summary') {
            $userConsumptions = (new Models\User\UserConsumption()) -> getSummary($userId, $timezone);
        } else {
            $userConsumptions = (new Models\User\UserConsumption()) -> findBy($filter, $filteredInput['take'] ?? 50, $filteredInput['skip'] ?? 0, $filteredInput['orderBy'] ?? 'id');
        }


        return Core\Output::OK($response, $userConsumptions);
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
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        if (isset($filteredInput['userId']) && $filteredInput['userId'] != $userId) {
            return Core\Output::NotAuthorized($response);
        }

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
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validationFields = array_merge($validationFields, Models\User\UserConsumption::getConfig('updatable'));

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        if (isset($filteredInput['userId']) && $filteredInput['userId'] != $userId) {
            return Core\Output::NotAuthorized($response);
        }

        // Get the POST body and filter out the non-updatables
        $postdata = array_intersect_key($filteredInput, Models\User\UserConsumption::getConfig('updatable'));

        $userConsumption = (new Models\User\UserConsumption) -> findBy(['id' => $filteredInput['id']], $take = 1);
        if (!$userConsumption) {
            return Core\Output::ModelNotFound($response, 'UserConsumption', $filteredInput['id']);
        }

        if ($userConsumption -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $userConsumption -> map($postdata) -> update();

        // output the item with its updated values
        return Core\Output::OK($response, $userConsumption);
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
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        if (isset($filteredInput['userId']) && $filteredInput['userId'] != $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $userConsumption = (new Models\User\UserConsumption) -> findBy(['id' => $filteredInput['id']], $take = 1);
        if (!$userConsumption) {
            return Core\Output::ModelNotFound($response, 'UserConsumption', $filteredInput['id']);
        }

        if ($userConsumption -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $userConsumption -> delete();

        return Core\Output::OK($response, ['deleted_at' => $userConsumption -> getDeletedAt()]);
    }

}
