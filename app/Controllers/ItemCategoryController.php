<?php

namespace ConsumptionTracker\Controllers;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Config AS Config;
use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Modules AS Modules;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ItemCategoryController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = '\ConsumptionTracker\\Models\\' . "ItemCategory";

}
