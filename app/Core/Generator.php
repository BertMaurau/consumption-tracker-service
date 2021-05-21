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
 * Description of Generator
 *
 * Handles everything concerning the generation of random strings etc.
 *
 * @author Bert Maurau
 */
class Generator
{

    /**
     * Generate a random string with requested length
     * @param int $length
     * @return string
     */
    private static function Generate(int $length)
    {
        return substr(md5(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32)))), 0, $length);
    }

    public static function Slug(string $text, $inTable = null)
    {

        $text = str_replace("'", '', $text);
        $text = str_replace('"', '', $text);

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if ($inTable) {

            $parts = explode(':', $inTable);
            $table = $parts[0];
            $column = count($parts) === 2 ? $parts[1] : 'slug';

            $slugToCheck = $text;
            $increment = 0;

            do {
                $sql = "SELECT * FROM $table WHERE $column = '" . Database::escape($slugToCheck) . "' LIMIT 1;";
                $result = Database::query($sql);
                if ($result -> num_rows === 1) {
                    // duplicate.. what are the odds..
                    $exists = true;
                    $increment += 1;
                    $slugToCheck = $text . '-' . $increment;
                } else {
                    $exists = false;
                    $text = $slugToCheck;
                }
            } while ($exists);
        }

        return $text;
    }

    /**
     * Generate a UID
     * @return string
     */
    public static function Uid($inTable = null)
    {
        if ($inTable) {
            $parts = explode(':', $inTable);
            $table = $parts[0];
            $column = count($parts) === 2 ? $parts[1] : 'uid';

            // check if uid already exists
            $exists = true;
            $UIDToCheck = null;

            do {
                $UIDToCheck = self::Generate(32);

                $sql = "SELECT * FROM $table WHERE $column = '$UIDToCheck' LIMIT 1;";
                $result = Database::query($sql);
                if ($result -> num_rows === 1) {
                    // duplicate.. what are the odds..
                    $exists = true;
                } else {
                    $exists = false;
                }
            } while ($exists);

            return $UIDToCheck;
        } else {
            return self::Generate(32);
        }
    }

    /**
     * Generate a Confirmation Code
     * @return string
     */
    public static function ConfirmationCode()
    {
        return strtoupper(self::Generate(8));
    }

    /**
     * Generate a CollectionCode
     * @return string
     */
    public static function CollectionCode()
    {
        return strtoupper(self::Generate(8));
    }

    public static function Code($inTable = null)
    {
        if ($inTable) {
            $parts = explode(':', $inTable);
            $table = $parts[0];
            $column = count($parts) === 2 ? $parts[1] : 'code';

            // check if uid already exists
            $exists = true;
            $CodeToCheck = null;

            do {
                $CodeToCheck = self::Generate(8);

                $sql = "SELECT * FROM $table WHERE $column = '$CodeToCheck' LIMIT 1;";
                $result = Database::query($sql);
                if ($result -> num_rows === 1) {
                    // duplicate.. what are the odds..
                    $exists = true;
                } else {
                    $exists = false;
                }
            } while ($exists);

            return $CodeToCheck;
        } else {
            return self::Generate(8);
        }
    }

    /**
     * Generate an Invite Code
     * @return string
     */
    public static function InviteCode()
    {
        return self::Generate(8);
    }

    /**
     * Generate a GUID v4
     *
     * @param mixed $data data
     *
     * @return string|null
     */
    public static function GUIDv4($inTable = null)
    {

        if ($inTable) {
            $parts = explode(':', $inTable);
            $table = $parts[0];
            $column = count($parts) === 2 ? $parts[1] : 'guid';

            // check if uid already exists
            $exists = true;
            $UIDToCheck = null;

            do {
                $data = random_bytes(16);

                assert(strlen($data) == 16);

                $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
                $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

                $UIDToCheck = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

                $sql = "SELECT * FROM $table WHERE $column = '$UIDToCheck' LIMIT 1;";
                $result = Database::query($sql);
                if ($result -> num_rows === 1) {
                    // duplicate.. what are the odds..
                    $exists = true;
                } else {
                    $exists = false;
                }
            } while ($exists);

            return $UIDToCheck;
        } else {
            $data = random_bytes(16);

            assert(strlen($data) == 16);

            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
    }

}
