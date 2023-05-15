<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class Calendar {

    private $active_year;
    private $active_month;
    private $active_day;
    private $events = [];

    public function __construct($date = null)
    {
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
    }

    public function addEvent($txt, $date, $days = 1, $colorDark = '', $colorLight = '', $description = '') {
        $color = $colorDark ? ' ' . $colorDark : $colorDark;
        $colorLight = $colorLight ? ' ' . $colorLight : $colorLight;
        $this->events[] = [$txt, $date, $days, $color, $colorLight, $description];  // Add the event description
    }
    

    public function loadEventsFromServer() {
        // Retrieve the events from the server
        $eventSchedule = new EventSchedule();
        $serverEvents = $eventSchedule->getServerEvents();
    
        // Check if events are retrieved successfully
        if(!empty($serverEvents) && !empty($serverEvents['eventlist'])) {
            // Loop through each event
            foreach($serverEvents['eventlist'] as $serverEvent) {
                // Convert the event start and end dates to 'Y-m-d' format
                $startDate = date('Y-m-d', $serverEvent['startdate']);
                $endDate = date('Y-m-d', $serverEvent['enddate']);
    
                // Calculate the number of days for the event
                $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
    
                // Add the event to the calendar events
                $this->addEvent(
                    $serverEvent['name'],
                    $startDate,
                    $days,
                    $serverEvent['colordark'],
                    $serverEvent['colorlight'],
                    $serverEvent['description']  // Add the event description
                );
            }
        }
    }

    public function __toString()
    {
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);

        $html = '<div style="display: flex; flex-wrap: wrap;">';
        foreach ($days as $day) {
            $html .= '<div style="display: flex; align-items: center; justify-content: center; width:120px; height: 24px; color: #fff; background-color:#5f4d41; border: 1px solid #FAF0D7;"><b>' . $day . '</b></div>';
        }
        $html .= '</div><div style="display: flex; flex-wrap: wrap;">';
        
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '<div style="height:82px; background-clip: padding-box; overflow: hidden; vertical-align:top; background-color:#D4C0A1;">
                    <div style="font-weight: bold; margin-left: 3px; margin-bottom: 2px;">
                    <span style="vertical-align: text-bottom;">
                    ' . ($num_days_last_month-$i+1) . '
                    </span>
                    </div>
                    </div>
                    ';
        }        
        
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = 'E7D1AF';
            if ($i == $this->active_day) {
                $selected = 'f3e5d0';
            }
            $html .= '<div style="height:82px; width:120px; border: 1px solid #FAF0D7; background-clip: padding-box; overflow: hidden; vertical-align:top; background-color:#'. $selected .';">';
            $html .= '<div style="font-weight: bold; margin-left: 3px; margin-bottom: 2px;">';
            $html .= '<span style="vertical-align: text-bottom;">' . $i . '</span></div>';
    
            // Initialize an empty string for all descriptions
            $allDescriptions = '';
            $eventExists = false;
    
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event[2]-1); $d++) {
                    if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('y-m-d', strtotime($event[1]))) {
                        // Append the event description to the all descriptions string
                        $allDescriptions .= '<strong>' . $event[0] . ':</strong><br />&bull; ' . $event[5] . '<br /><br />';
                        $eventExists = true;
                    }
                }
            }
    
            if ($eventExists) {
                $html .= '<span style="width: 120px;" class="HelperDivIndicator" onmouseover="ActivateHelperDiv($(this), \'\', \'' . $allDescriptions . '\', \'\');" onmouseout="$(\'#HelperDivContainer\').hide();">';
                foreach ($this->events as $event) {
                    for ($d = 0; $d <= ($event[2]-1); $d++) {
                        if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('y-m-d', strtotime($event[1]))) {
                            $html .= '<div style="background:' . $event[3] . '; color:white; width: 100%; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 1%; padding-left: 3px; margin-bottom:2px">' . $event[0] . '</div>';
                        }
                    }
                }
                $html .= '</span>';
            }
            $html .= '</div>';
        }
        
        
        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            $html .= '<div style="height:82px; width:120px; border: 1px solid #FAF0D7; background-clip: padding-box; overflow: hidden; vertical-align:top; background-color:#D4C0A1;">
            <div style="font-weight: bold; margin-left: 3px; margin-bottom: 2px;">';
            $html .= '<span style="vertical-align: text-bottom;">' . $i . '</span></div></div>';
        }
        $html .= '</div>';
        return $html;
    }
}