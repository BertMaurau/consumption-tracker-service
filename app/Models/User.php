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

namespace ConsumptionTracker\Models;

use \ConsumptionTracker\Core AS Core;
use \ConsumptionTracker\Models AS Models;

/**
 * Description of User
 *
 * @author bertmaurau
 */
class User extends Models\BaseModel
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
        'table'             => 'users',
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
        'updatable'         => [
            'first_name'   => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'first_name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            'last_name'    => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'last_name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            'email'        => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => false,],
            'country_code' => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'country_code', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            'units'        => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'units', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            'timezone'     => ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'timezone', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ],
        /**
         * List of properties that are allowed to be searchable
         */
        'searchable'        => [
            'name'
        ],
        /**
         * List of properties that are allowed to be ordered on
         */
        'orderable'         => [
            'id', 'name', 'created_at', 'updated_at'
        ],
        /**
         * If the model contains an image, return the paths to the base image
         * directory
         */
        'hasImageReference' => true,
        /**
         * Directory for the images
         */
        'imageDirectory'    => 'users',
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
        'resourceUris'      => [
            'self' => [
                'users' => 'id',
            ],
        ],
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
     * GUID
     * @var string
     */
    public $guid;

    /**
     * Get GUID
     *
     * @return string
     */
    public function getGuid(): string
    {
        return $this -> guid;
    }

    /**
     * Set GUID
     *
     * @param string $guid
     *
     * @return $this
     */
    public function setGuid(string $guid)
    {
        $this -> guid = $guid;

        // add the avatar
        $this -> addAvatarLink();

        return $this;
    }

    /**
     * First Name
     * @var string
     */
    public $first_name;

    /**
     * Get First Name
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this -> first_name;
    }

    /**
     * Set First Name
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName = null)
    {
        $this -> first_name = $firstName;
        return $this;
    }

    /**
     * Last Name
     * @var string
     */
    public $last_name;

    /**
     * Get Last Name
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this -> last_name;
    }

    /**
     * Set Last Name
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName = null)
    {
        $this -> last_name = $lastName;
        return $this;
    }

    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this -> email;
    }

    /**
     * Set Email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email)
    {
        $this -> email = $email;
        return $this;
    }

    /**
     * Password
     * @var string
     */
    protected $password;

    /**
     * Set Password
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this -> password;
    }

    /**
     * Get Password
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password = null)
    {
        $this -> password = $password;
        return $this;
    }

    /**
     * Country Code
     * @var string
     */
    public $country_code;

    /**
     * Set Country Code
     *
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this -> country_code;
    }

    /**
     * Get Country Code
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode(string $countryCode = null)
    {
        $this -> country_code = $countryCode;
        return $this;
    }

    /**
     * Timezone
     * @var string
     */
    public $timezone;

    /**
     * Set Timezone
     *
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this -> timezone;
    }

    /**
     * Get Timezone
     *
     * @param string $timezone
     *
     * @return $this
     */
    public function setTimezone(string $timezone = null)
    {
        $this -> timezone = $timezone;
        return $this;
    }

    /**
     * Units
     * @var string
     */
    public $units;

    /**
     * Set Units
     *
     * @return string|null
     */
    public function getUnits(): ?string
    {
        return $this -> units;
    }

    /**
     * Get Units
     *
     * @param string $units
     *
     * @return $this
     */
    public function setUnits(string $units = 'Metric')
    {
        $this -> units = $units;
        return $this;
    }

    /**
     * Verified At
     * @var \DateTime
     */
    public $verified_at;

    /**
     * Get Verified At
     *
     * @return \DateTime|null
     */
    public function getVerifiedAt(): ?\DateTime
    {
        return $this -> verified_at;
    }

    /**
     * Set Verified At
     *
     * @param string $verifiedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setVerifiedAt($verifiedAt = null)
    {
        $this -> verified_at = $verifiedAt;
        if ($verifiedAt && is_string($verifiedAt)) {
            try {
                $dt = new \DateTime($verifiedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (User::verifiedAt).");
            }
            $this -> verified_at = $dt;
        }

        return $this;
    }

    /**
     * |======================================================================
     * | Model Functions
     * |======================================================================
     */

    /**
     * Get the display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return trim(($this -> getFirstName() ?: '') . ' ' . ($this -> getLastName() ?: ''));
    }

    /**
     * Add the public link to the avatar image
     *
     * @return User
     */
    public function addAvatarLink()
    {

        $localUrl = Core\Config::getInstance() -> Paths() -> images . self::getConfig('imageDirectory') . '/' . $this -> getGuid() . '.jpg';
        $publicUrl = Core\Config::getInstance() -> Paths() -> imagesUrl . self::getConfig('imageDirectory') . '/' . $this -> getGuid() . '.jpg';

        $modified = filemtime($localUrl);

        // add the avatar link
        $this -> addAttribute('avatar', $publicUrl . '?tsm=' . $modified);

        return $this;
    }

    /**
     * Create a new User
     *
     * @param string $email
     * @param string $password
     * @param array $properties
     *
     * @return User
     */
    public static function create(string $email, string $password, array $properties = [])
    {

        $timezone = 'UTC';

        // fetch the location
        $location = Core\Auth::getLocation();
        // get country code
        $countryCode = trim(explode(',', $location)[1]);
        if ($countryCode !== 'Unknown') {
            $timezoneModel = (new Models\Timezone) -> findBy(['country_code' => $countryCode], $take = 1);
            if ($timezoneModel) {
                $timezone = $timezoneModel -> getTimezone();
            }
        } else {
            $countryCode = null;
        }

        $email = strtolower(trim($email));

        // create the record
        $user = (new self)
                -> setGuid(Core\Generator::GUIDv4('users'))
                -> setEmail($email)
                -> setFirstName($properties['first_name'] ?? null)
                -> setLastName($properties['last_name'] ?? null)
                -> setTimezone($timezone)
                -> setCountryCode($countryCode)
                -> insert();

        // salt and hash the password
        $user -> updatePassword($password);

        // generate an avatar
        $user -> generateAvatar();

        return $user;
    }

    /**
     * Generate a new access token and add it as attribute
     *
     * @param string $device
     * @param string $browser
     *
     * @throws \Exception
     */
    public function addAccessToken(string $device = null, string $browser = null)
    {
        if (!$this -> getId()) {
            throw new \Exception('User not initialized.');
        }

        $userAuthToken = User\UserToken::create($this -> getId(), 30, [
                    'device'  => $device,
                    'browser' => $browser,
                ]) -> getAuthToken();

        // Add token
        $this -> addAttribute('access_token', $userAuthToken);
    }

    /**
     * Update the user's password
     *
     * @param string $password
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function updatePassword(string $password)
    {
        if (!$this -> getId()) {
            throw new \Exception('User not initialized.');
        }

        $passwordSalted = $this -> getGuid() . $password . Core\Config::getInstance() -> Salts() -> password;
        $passwordHash = password_hash($passwordSalted, PASSWORD_DEFAULT, ['cost' => 11]);

        $this -> setPassword($passwordHash) -> update();

        return true;
    }

    /**
     * Validate the given password
     *
     * @param string $password The password hash
     *
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        if (!$this -> getGuid()) {
            throw new \Exception('User not initialized.');
        }

        $passwordSalted = $this -> getGuid() . $password . Core\Config::getInstance() -> Salts() -> password;
        if (!password_verify($passwordSalted, $this -> getPassword())) {
            return false;
        }

        // verify legacy password to new password_hash options
        if (password_needs_rehash($this -> getPassword(), PASSWORD_DEFAULT, ['cost' => 11])) {
            // rehash/store plain-text password using new hash
            $newHash = password_hash($passwordSalted, PASSWORD_DEFAULT, ['cost' => 11]);
            $this -> setPassword($newHash) -> update();
        }

        return true;
    }

    /**
     * Generate Avatar image for current user
     *
     * @return User
     */
    public function generateAvatar()
    {

        $displayName = (trim($this -> getFirstName() . ' ' . $this -> getLastName()) ?: $this -> getEmail());

        try {
            $avatar = new \LasseRafn\InitialAvatarGenerator\InitialAvatar();

            $imageData = $avatar
                    -> name($displayName)
                    -> length(2)
                    -> fontSize(0.5)
                    -> size(256)
                    -> background('#feffff')
                    -> color('#031f4b')
                    -> generate()
                    -> stream('png', 100);

            if ($imageData) {
                Core\Image::saveImage($imageData, self::getConfig('imageDirectory'), $this -> getGuid(), null);
            }
        } catch (\Exception $ex) {

        }

        $this -> addAvatarLink();

        return $this;
    }

}
