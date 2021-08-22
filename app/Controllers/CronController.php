<?php

namespace ConsumptionTracker\Controllers;

use ConsumptionTracker\Core AS Core;
use ConsumptionTracker\Config AS Config;
use ConsumptionTracker\Models AS Models;
use ConsumptionTracker\Modules AS Modules;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GO\Scheduler;

class CronController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = '\ConsumptionTracker\\Models\\' . "Cron";

    /**
     * Handle incoming recurring request for CRON handling
     * (Currently the CRON is scheduled to run every hour at the 30-minute mark
     * using the UTC timezone)
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function scheduler(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // Create a new scheduler
        $scheduler = new Scheduler();

        // ex. for a daily script
        // $scheduler -> php('script.php') -> daily($whichHourOfTheDay, 30);
        // ..
        //
        // ex. for a weekly script
        // $scheduler -> php('script.php') -> sunday($whichHourOfTheDay, 30);
        // ..
        //

        /**
         * Weekly Summaries
         *
         * process the weekly summaries (send to users on the monday for the
         * week that has passed. So we need to check every hour, compare timezones,
         * check if it's monday 6 in the morning for that user and process it)
         */
        $scheduler -> call(function () {

            $this -> runWeeklySummary(1, '04');
            /**
              SELECT
              CONVERT_TZ(NOW(), '+00:00', timezones.raw_offset) AS local_time
              FROM users
              LEFT JOIN timezones ON timezones.timezone = users.timezone
              WHERE DATE_FORMAT(CONVERT_TZ(NOW(), '+00:00', timezones.raw_offset), "%w%H") = '104'

             */
//            $users = (new Models\User) -> getActiveUsers();
//            foreach ($users as $user) {
//                $localTimeUser = new \DateTime("now", new \DateTimeZone($user -> getTimezone()));
//                if ($localTimeUser -> format('NH') === '104') {
//                    // execute
//                }
//            }

            return true;
        }) -> hourly(30);

        // Let the scheduler execute jobs which are due.
        $scheduler -> run();
    }

    private function runWeeklySummary(int $dayToExport = 1, string $hourToExport)
    {

        // get all the users with a consumption this month
        $users = (new Models\User) -> getActiveWhereLocalTime($dayToExport . $hourToExport);
        foreach ($users as $user) {
            $userConsumption = (new Models\User\UserConsumption) -> getSummaryFromLastWeek($user -> getId(), $user -> getTimezone());
        }
    }

}
