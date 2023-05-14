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

class EventSchedule {
    const SERVER_PATH = 'SERVER_PATH';
    const XML_PATH = 'data/XML/events.xml';
    const DEFAULT_EVENT_NAME = 'Event';

    public static function getEventDetails($eventTags, $tag) {
        if ($eventTags->length > 0) {
            return $eventTags[0]->getAttribute($tag) ?? '';
        }
        return '';
    }

    public static function formatDateAttribute($event, $attribute) {
        $date = date_create($event->getAttribute($attribute));
        if ($date === false) {
            return null;
        }
        return (int)$date->format('U');
    }

    public static function loadEvents() {
        $server_path = $_ENV[self::SERVER_PATH];
        $xml_events  = $server_path . self::XML_PATH;
        if (!file_exists($xml_events)) {
            return [];
        }
        $loadxml_events = new DOMDocument;
        $loadxml_events->load($xml_events);
        return $loadxml_events->getElementsByTagName('event');
    }

    public static function getServerEvents() {
        $events = self::loadEvents();
        $arrayEvents = [];
        foreach ($events as $event) {
            if ($event) {
                $arrayEvents[] = [
                    "name"            => $event->getAttribute('name') ?? self::DEFAULT_EVENT_NAME,
                    "colorlight"      => self::getEventDetails($event->getElementsByTagName('colors'), 'colorlight'),
                    "colordark"       => self::getEventDetails($event->getElementsByTagName('colors'), 'colordark'),
                    "description"     => self::getEventDetails($event->getElementsByTagName('description'), 'description'),
                    "startdate"       => self::formatDateAttribute($event, 'startdate'),
                    "enddate"         => self::formatDateAttribute($event, 'enddate'),
                    "displaypriority" => (int)self::getEventDetails($event->getElementsByTagName('details'), 'displaypriority'),
                    "isseasonal"      => (int)self::getEventDetails($event->getElementsByTagName('details'), 'isseasonal') == 1,
                    "specialevent"    => (int)self::getEventDetails($event->getElementsByTagName('details'), 'specialevent'),
                ];
            }
        }
        return ['eventlist' => $arrayEvents, 'lastupdatetimestamp' => time()];
    }
}
