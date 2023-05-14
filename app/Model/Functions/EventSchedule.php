<?php
/**
 * EventSchedule Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use DOMDocument;

class EventSchedule{

    public static function getEventDetails($tagnme, $return_detail)
    {
        foreach ($tagnme as $table) {
            return $table->getAttribute($return_detail) ?? '';
        }
    }

    public static function getBoolean($value)
    {
        if ($value == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function getServerEvents()
    {
        $server_path = $_ENV['SERVER_PATH'];
        $xml_events = $server_path . 'data/XML/events.xml';
        if (!file_exists($xml_events)) {
            return [];
        }
        $loadxml_events = new DOMDocument;
        $loadxml_events->load($xml_events);
        $events = $loadxml_events->getElementsByTagName('event');
        $arrayEvents = [];
        foreach ($events as $event) {
            if ($event) {
                $arrayEvents[] = [
                    "colorlight" => self::getEventDetails($event->getElementsByTagName('colors'), 'colorlight'),
                    "colordark" => self::getEventDetails($event->getElementsByTagName('colors'), 'colordark'),
                    "description" => self::getEventDetails($event->getElementsByTagName('description'), 'description'),
                    "displaypriority" => (int)self::getEventDetails($event->getElementsByTagName('details'), 'displaypriority'),
                    "enddate" => (int)date_create("{$event->getAttribute('enddate')}")->format('U'),
                    "isseasonal" => self::getBoolean((int)self::getEventDetails($event->getElementsByTagName('details'), 'isseasonal')),
                    "name" => $event->getAttribute('name'),
                    "startdate" => (int)date_create("{$event->getAttribute('startdate')}")->format('U'),
                    "specialevent" => (int)self::getEventDetails($event->getElementsByTagName('details'), 'specialevent'),
                ];
            }
        }
        return ['eventlist' => $arrayEvents, 'lastupdatetimestamp' => time()];
    }
}