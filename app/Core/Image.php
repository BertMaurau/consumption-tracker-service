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
 * Description of Image
 *
 * @author bertmaurau
 */
class Image
{

    const SIZES = [64, 128, 256, 512, 1024];

    /**
     * Get the image contents from the url
     *
     * @param string $imageUrl
     * @param string $imageDirectory
     * @param string $guid
     * @param int $maxSize
     *
     * @return boolean|string
     */
    public static function getFromUrl(string $imageUrl, string $imageDirectory, string $guid, int $maxSize = 512)
    {


        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $imageData = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $ex) {
            return false;
        }

        if ($imageData) {
            return self::saveImage($imageData, $imageDirectory, $guid, $maxSize);
        }
    }

    /**
     * Get the image from given base64 string
     *
     * @param string $imageData
     * @param string $imageDirectory
     * @param string $guid
     *
     * @return boolean|string
     */
    public static function getFromBase64($imageData, string $imageDirectory, string $guid, int $maxSize = 512)
    {
        // split the string on commas
        $data = explode(',', $imageData);
        if (count($data) > 1) {
            $image = base64_decode($data[1]);
        } else {
            $image = base64_decode($data[0]);
        }

        if ($image) {
            self::saveImage($image, $imageDirectory, $guid, $maxSize);
        }
    }

    /**
     * Save the image data as file
     *
     * @param type $imageData
     * @param string $imageDirectory
     * @param string $guid
     * @param int $maxSize
     *
     * @return string
     */
    public static function saveImage($imageData, string $imageDirectory, string $guid, int $maxSize = null)
    {

        $_filepath = rtrim(Config::getInstance() -> Paths() -> images, '/') . '/' . $imageDirectory . '/';

        if ($maxSize) {

            // resize the image
            $_filenameOrig = $guid . '-original.jpg';
            $_filenameNew = $guid . '.jpg';

            if (file_exists($_filepath . $_filenameOrig)) {
                // delete
                unlink($_filepath . $_filenameOrig);
            }

            if (@file_put_contents($_filepath . $_filenameOrig, $imageData)) {
                // resize the image
                $state = self::resize($_filepath . $_filenameOrig, $_filepath . $_filenameNew, $maxSize);
                if ($state) {
                    unlink($_filepath . $_filenameOrig);
                }
            }
        } else {

            $_filenameNew = $guid . '.jpg';

            // just save
            if (file_exists($_filepath . $_filenameNew)) {
                // delete
                unlink($_filepath . $_filenameNew);
            }

            @file_put_contents($_filepath . $_filenameNew, $imageData);
        }

        return $_filenameNew;
    }

    /**
     * Resize image - preserve ratio of width and height.
     *
     * @return void
     */
    public static function resize($sourceImage, $targetImage, $maxSize, $quality = 100)
    {

        if (!file_exists($sourceImage)) {
            return;
        }

        // Get dimensions of source image.
        $origWidth = null;
        $origHeight = null;
        $type = null;
        $state = false;
        try {
            list($origWidth, $origHeight, $type) = @getimagesize($sourceImage);
        } catch (\Exception $ex) {

        }

        if (!$origWidth || $origWidth == 0 || !$origHeight || $origHeight == 0) {
            return;
        }

        if ($type == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($sourceImage);
        } elseif ($type == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($sourceImage);
        } elseif ($type == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($sourceImage);
        }

        // Calculate ratio of desired maximum sizes and original sizes.
        $widthRatio = $maxSize / $origWidth;
        $heightRatio = $maxSize / $origHeight;

        // Ratio used for calculating new image dimensions.
        $ratio = min($widthRatio, $heightRatio);

        // Calculate new image dimensions.
        $newWidth = (int) $origWidth * $ratio;
        $newHeight = (int) $origHeight * $ratio;

        // Create final image with new dimensions.
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        if ($type == IMAGETYPE_JPEG) {
            $state = @imagejpeg($newImage, $targetImage, $quality);
        } elseif ($type == IMAGETYPE_PNG) {
            $state = @imagepng($newImage, $targetImage);
        } elseif ($type == IMAGETYPE_GIF) {
            $state = @imagegif($newImage, $targetImage);
        }

        // Free up the memory.
        if ($image) {
            imagedestroy($image);
        }
        if ($newImage) {
            imagedestroy($newImage);
        }

        return $state;
    }

}
