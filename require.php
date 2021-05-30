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

namespace ConsumptionTracker;

use ConsumptionTracker\Modules AS Modules;
use ConsumptionTracker\Core AS Core;

// Set all dates to the UTC (default)
// Set this to your own needs or comment it out.
// You can get the list of all supported timezones here:
//  - http://php.net/manual/en/timezones.php
// -----------------------------------------------------------------------------
date_default_timezone_set('UTC');
//set_time_limit(1800);
// -----------------------------------------------------------------------------
//  Include the Composer autoloader for external dependencies
// -----------------------------------------------------------------------------
require_once __DIR__ . '/vendor/autoload.php';

//  Determine error reporting
// -----------------------------------------------------------------------------
if (Core\Config::getInstance() -> API() -> env == 'dev' || Core\Config::getInstance() -> API() -> env == 'staging') {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set("log_errors", 1);
    ini_set("error_log", __DIR__ . '/logs/error-' . date('Y-m-d') . '.log');

    error_reporting(E_ALL);
} else {

    ini_set("log_errors", 1);
    ini_set("display_errors", 0);
    ini_set("error_log", __DIR__ . '/logs/error-' . date('Y-m-d') . '.log');

    error_reporting(E_ALL);
}


// Connect with the DB
// This could also be within some sort of App class.
// See the DB class to get the list of available functions
// ------------------------------------------------------------------------------
try {
    Core\Database::init();
} catch (\Exception $ex) {
    echo "Failed to connect with the database. Reason: " . $ex -> getMessage();
    // No DB, no API.
    exit;
}
