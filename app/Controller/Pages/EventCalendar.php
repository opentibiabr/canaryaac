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

class EventCalendar extends Base{

    public static function viewEventCalendar($request)
    {
        $queryParams = $request->getQueryParams();
        if (!empty($queryParams)) {
            $calendarmonth = $queryParams['calendarmonth'];
            $calendaryear = $queryParams['calendaryear'];
        }
        $current_day = date('d');
        $calendarmonth = date('m');
        $calendaryear = date('Y');
        $total_days_month = cal_days_in_month(CAL_GREGORIAN, $calendarmonth, $calendaryear);
        for($i = 0; $i < $total_days_month; $i++){
            $month[] = [
                'day' => $i,
                'currentday' => $current_day,
            ];
        }

        $calendar = new Calendar(date($calendaryear . '-' . $calendarmonth . '-' . $current_day));
        $calendar->loadEventsFromServer();
        
        $content = View::render('pages/eventcalendar', [
            'currentday' => $current_day,
            'day' => $calendar ?? null,
        ]);
        return parent::getBase('Event Schedule', $content, 'eventcalendar');
    }
}