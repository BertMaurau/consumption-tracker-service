<?php

namespace ConsumptionTracker\Controllers;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Config AS Config;
use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Modules AS Modules;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserPasswordResetController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = '\ConsumptionTracker\\Models\\User\\' . "UserPasswordReset";

    /**
     * Request a new reset
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function request(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        $user = (new Models\User) -> findBy(['email' => $filteredInput['email']], 1);
        if (!$user) {
            return Core\Output::NotFound($response, 'No user found with given email address.');
        }

        try {
            $userPasswordReset = Models\User\UserPasswordReset::create($user -> getId());
        } catch (\Exception $ex) {
            return Core\Output::ServerError($response, $ex -> getMessage());
        }

        // send an email
        $mailer = (new Core\Mailer)
                -> build('password-reset', 'Reset your password', [
                    'URL_RESET' => $userPasswordReset -> getResetLink()
                ])
                -> send($user -> getEmail(), $user -> getDisplayName());

        return Core\Output::OK($response, $userPasswordReset);
    }

    /**
     * Execute the reset
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function reset(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'token', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        $userPasswordReset = (new Models\User\UserPasswordReset) -> findBy(['token' => $filteredInput['token']], 1);
        if (!$userPasswordReset) {
            return Core\Output::NotFound($response, 'No password reset request found for given reset token.');
        }

        if (!$userPasswordReset -> isValid()) {
            if ($userPasswordReset -> isClaimed()) {
                return Core\Output::DisabledResource($response, 'Reset token already claimed.');
            }
            if ($userPasswordReset -> isExpired()) {
                return Core\Output::DisabledResource($response, 'Reset token expired. Please request a new reset.');
            }
        }

        $user = (new Models\User) -> getById($userPasswordReset -> getUserId());
        if (!$user) {
            return Core\Output::NotFound($response, 'The user associated with this password reset could not be found.');
        }

        $user -> updatePassword($filteredInput['password']);

        $userPasswordReset -> setClaimedAt(date('Y-m-d H:i:s')) -> update();

        return Core\Output::OK($response, $userPasswordReset);
    }

    /**
     * Validate the reset (DEPRECATED)
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function validate(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'token', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        $userPasswordReset = (new Models\User\UserPasswordReset) -> findBy(['token' => $filteredInput['token']], 1);
        if (!$userPasswordReset) {
            return Core\Output::NotFound($response, 'No password reset request found for given reset token.');
        }

        if (!$userPasswordReset -> isValid()) {
            if ($userPasswordReset -> isClaimed()) {
                return Core\Output::DisabledResource($response, 'Reset token already claimed.');
            }
            if ($userPasswordReset -> isExpired()) {
                return Core\Output::DisabledResource($response, 'Reset token expired. Please request a new reset.');
            }
        }

        return Core\Output::OK($response, $userPasswordReset);
    }

}
