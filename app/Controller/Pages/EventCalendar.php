<?php

/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\Forum as EntityForum;
use App\Model\Functions\Calendar;
use \App\Utils\View;
use App\Model\Functions\Server;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonPeriodImmutable;

class EventCalendar extends Base
{

    public static function viewEventCalendar($request)
    {
        $queryParams = $request->getQueryParams();
        $now = Carbon::now()->startOfMonth();
        if (!empty($queryParams)) {
            if (isset($queryParams['calendarmonth'])) {
                $calendarmonth = $queryParams['calendarmonth'];
                $now->setMonth($calendarmonth);
            }

            if (isset($queryParams['calendaryear'])) {
                $calendaryear = $queryParams['calendaryear'];
                $now->setYear($calendaryear);
            }
        }

        $start = $now->copy()->startOfMonth();
        if ($start->dayOfWeek == Carbon::MONDAY) {
            $start->subWeek();
        } else {
            $start->startOfWeek(Carbon::MONDAY);
        }

        $end = $now->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendar = new Calendar(new CarbonPeriodImmutable($start, $end));
        $calendar->loadEventsFromServer();
        $events = $calendar->getEvents();
        $calendar = [];
        $i = 0;
        $j = 0;
        while ($start->lte($end)) {
            $day = $start->day;

            if ($i == 7) {
                $j++;
                $i = 0;
            }

            if (!isset($calendar[$j])) {
                $calendar[$j] = [];
            }

            $raw_events = '';
            if (isset($events[$start->format('Y-m-d')]) && count($events[$start->format('Y-m-d')])) {
                $description = '';
                $html_events = '';
                foreach ($events[$start->format('Y-m-d')] as $event) {
                    $description .= '<strong>' . $event[0] . ':</strong><br />&bull; ' . $event[3] . '<br /><br />';
                    $html_events .= '<div style="background:' . $event[2] . '; color:white; width: 100%; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 1%; padding-left: 3px; margin-bottom:2px">' . $event[0] . '</div>';
                }

                $html = '<span style="width: 120px;" class="HelperDivIndicator" onmouseover="ActivateHelperDiv($(this), \'\', \'' . $description . '\', \'\');" onmouseout="$(\'#HelperDivContainer\').hide();">';
                $html .= $html_events;
                $html .= '</span>';

                $raw_events .= $html;
            }

            $color = '#E7D1AF';
            if ($start->isSameDay(Carbon::now())) {
                $color = '#F3E5D0';
            }

            if ($start->lt($now) || $start->gt($now->copy()->endOfMonth())) {
                $color = '#D4C0A1';
            }

            $calendar[$j][] = [
                'day' => $day,
                'color' => $color,
                'html' => $raw_events,
            ];

            $start->addDay();
            $i++;
        }

        $prev = $now->copy()->subMonth();
        $next = $now->copy()->addMonth();
        $content = View::render('pages/eventcalendar', [
            'calendar' => $calendar,
            'current_date' => $now->monthName . ' ' . $now->year,
            'prev' => $now->gt(Carbon::now()),
            'prev_month' => $prev->month,
            'prev_year' => $prev->year,
            'next_month' => $next->month,
            'next_year' => $next->year,
            'now' => Carbon::now()->format('Y-m-d H:i T')
        ]);
        return parent::getBase('Event Schedule', $content, 'eventcalendar');
    }
}
