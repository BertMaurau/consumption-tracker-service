<?php

namespace ConsumptionTracker\Controllers;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of BaseController
 *
 * This handles the basic requests actions like
 *  - index  [GET]    (List all models)
 *  - show   [GET]    (List a specific model)
 *  - create [POST]   (Insert a new model)
 *  - update [PATCH]  (Update a specific model)
 *  - delete [DELETE] (Delete a specific model)
 *
 * @author Bert Maurau
 */
class BaseController
{

    const MODEL_NAME = '\ConsumptionTracker\\Models\\' . "";

    /**
     * Get all resources
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'parentId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'orderBy', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'take', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'skip', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        if ($parentClass = $modelClass::getConfig('parent')) {
            // validate for parent id
            if (!isset($filteredInput['parentId'])) {
                return Core\Output::MissingParameter($response, "Missing parentId for $parentClass.");
            }

            $parent = (new $parentClass()) -> getById($filteredInput['parentId']);
            if (!$parent) {
                return Core\Output::ModelNotFound($response, $parentClass, $filteredInput['parentId']);
            }
        }

        $filterable = $modelClass::getConfig('filterable') ?? null;
        if ($filterable) {
            $filter = array_intersect_key($filteredInput, array_flip([]));
        } else {
            $filter = [];
        }

        $models = (new $modelClass()) -> findBy($filter, $filteredInput['take'] ?? 50, $filteredInput['skip'] ?? 0, $filteredInput['orderBy'] ?? 'id');

        // This will return an array-list of mapped object items.
        return Core\Output::OK($response, $models);
    }

    /**
     * Get a specific resource
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'parentId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'id', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        if ($parentClass = $modelClass::getConfig('parent')) {
            // validate for parent id
            if (!isset($filteredInput['parentId'])) {
                return Core\Output::MissingParameter($response, "Missing parentId for $parentClass.");
            }

            $parent = (new $parentClass()) -> getById($filteredInput['parentId']);
            if (!$parent) {
                return Core\Output::ModelNotFound($response, $parentClass, $filteredInput['parentId']);
            }
        }


        // Init the model and get the resource by the given ID.
        $model = (new $modelClass()) -> getById($filteredInput['id']);
        if (!$model) {
            // Return the defined 404 output.
            return Core\Output::ModelNotFound($response, $modelClass, $filteredInput['id']);
        }

        $model -> expand();

        // Output the object item.
        return Core\Output::OK($response, $model);
    }

    /**
     * Create a new resource
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'parentId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        if ($parentClass = $modelClass::getConfig('parent')) {
            // validate for parent id
            if (!isset($filteredInput['parentId'])) {
                return Core\Output::MissingParameter($response, "Missing parentId for $parentClass.");
            }

            $parent = (new $parentClass()) -> getById($filteredInput['parentId']);
            if (!$parent) {
                return Core\Output::ModelNotFound($response, $parentClass, $filteredInput['parentId']);
            }
        }

        // Get the POST body and filter out the non-updatables
        $postdata = (object) array_intersect_key(json_decode($request -> getBody(), true), array_flip($modelClass::getConfig('updatable')));

        // Map the POST values to the model
        // Non-model properties will be put under `attributes` and will be skipped
        // when inserting the record.
        $model = (new $modelClass($postdata));

        // Insert the model into the DB
        $model -> insert();

        // Return the newly created model with the generated ID
        return Core\Output::OK($response, $model);
    }

    /**
     * Update a specific resource
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        // Check for the given modelId. (validate this more if you'd like)
        if (!isset($args['id'])) {
            // Return the defined output for a missing ID attribute.
            // Defined within the app/Output file.
            return Core\Output::MissingModelId($response);
        }

        // check if integer
        if (filter_var($args['id'], FILTER_VALIDATE_INT) === false) {
            return Core\Output::InvalidParameter($response, 'Value `' . $args['id'] . ' is not a valid Resource-ID (number|integer).');
        }

        // set the ID
        $modelId = $args['id'];

        // Get the POST body and filter out the non-updatables
        $postdata = (object) array_intersect_key(json_decode($request -> getBody(), true), $modelClass::UPDATABLE);

        // Get the model by the ID first to get the current values.
        // This is optional, but will allow to return the entire item with its
        // values instead of empty/null/missing properties.
        // This will also, ofcourse, check if the resource exists.
        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            // Return the defined 404 output.
            return Core\Output::ModelNotFound($response, $modelClass, $modelId);
        }

        // Map the POST values to the model
        $model -> map($postdata);

        // apply validation rules to the mapped object before inserting
        if (!$validator[0] = $model -> validate()) {
            // If any validation rule failed. Return the defined output.
            return Core\Output::ValidationFailed($response, $validator[1]);
        }

        // Update the record
        $model -> update();

        // Output the item with its updated values
        return Core\Output::OK($response, $model);
    }

    /**
     * Delete a specific resource
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // Get the current ModelName to init a class.

        $modelClass = static::MODEL_NAME;

        // Check for the given modelId. (validate this more if you'd like)
        if (!isset($args['id'])) {
            // Return the defined output for a missing ID attribute.
            // Defined within the app/Output file.
            return Core\Output::MissingModelId($response);
        }

        // Set the ID
        $modelId = $args['id'];

        // Get the model by the ID first to check if it exists.
        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            // Return the defined 404 output.
            return Core\Output::ModelNotFound($response, $modelClass, $modelId);
        }

        // Delete the record
        $model -> delete();

        // Output the deleted model (with its values)
        return Core\Output::OK($response, ['deleted_at' => $model -> getDeletedAt()]);
    }

}
