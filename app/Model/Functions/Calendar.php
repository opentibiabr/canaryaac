<?php

/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTime;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class Calendar
{

    // private $active_year;
    // private $active_month;
    // private $active_day;
    private $date = null;
    private $events = [];

    public function __construct(CarbonPeriod $date)
    {
        $this->date = $date;
    }

    public function addEvent($txt, $date, $days = 1, $colorDark = '', $colorLight = '', $description = '')
    {
        $color = $colorDark ? ' ' . $colorDark : $colorDark;
        $colorLight = $colorLight ? ' ' . $colorLight : $colorLight;

        $key = $date->format('Y-m-d');
        if (!isset($this->events[$key])) {
            $this->events[$key] = [];
        }

        $this->events[$key][] = [$txt, $date, $color, $colorLight, $description];
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function loadEventsFromServer()
    {
        // Retrieve the events from the server
        $eventSchedule = new EventSchedule();
        $serverEvents = $eventSchedule->getServerEvents();

        // Check if events are retrieved successfully
        if (!empty($serverEvents) && !empty($serverEvents['eventlist'])) {
            // Loop through each event
            foreach ($serverEvents['eventlist'] as $serverEvent) {
                $startDate = Carbon::createFromTimestamp($serverEvent['startdate']);
                $endDate = Carbon::createFromTimestamp($serverEvent['enddate']);

                $period = new CarbonPeriod($startDate, $endDate);
                if ($this->date->overlaps($period) || $this->date->contains($period)) {
                    // Calculate the number of days for the event
                    $days = $endDate->diffInDays($startDate);
                    // Add the event to the calendar events

                    $date = $startDate->copy();
                    for ($i = 0; $i < $days; $i++) {
                        $date->addDay();
                        if ($this->date->contains($date)) {
                            $this->addEvent(
                                $serverEvent['name'],
                                $date,
                                $serverEvent['colordark'],
                                $serverEvent['colorlight'],
                                $serverEvent['description']  // Add the event description
                            );
                        }
                    }
                }
            }
        }
    }
}
