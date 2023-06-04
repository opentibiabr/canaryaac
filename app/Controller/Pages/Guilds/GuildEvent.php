<?php
/**
 * GuildEvent Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Guilds;

use App\Controller\Pages\Base;
use App\Utils\View;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Model\Functions\Guilds as FunctionGuilds;

class GuildEvent extends Base
{
    public static function convertGuildName($guild_name)
    {
        $decodeUrl = urldecode($guild_name);
        $filterName = filter_var($decodeUrl, FILTER_SANITIZE_SPECIAL_CHARS);
        $dbGuild = EntityGuilds::getGuilds('name = "'.$filterName.'"')->fetchObject();
        if($dbGuild == true) {
            $guild_id = $dbGuild->id;
        }
        return $guild_id ?? 0;
    }

    public static function insertGuildEvent($request, $name)
    {
        $postVars = $request->getPostVars();
        $guild_id = self::convertGuildName($name);

        $isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
        if($isLeader == false) {
            $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
        }

        if(empty($postVars['event_name'])) {
            $status = 'Enter a name.';
            return self::viewCreateGuildEvents($request, $name, $status);
        }

        if(empty($postVars['event_text'])) {
            $status = 'You need to write some description.';
            return self::viewCreateGuildEvents($request, $name, $status);
        }

        $event_name = filter_var($postVars['event_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $event_text = filter_var($postVars['event_text'], FILTER_SANITIZE_SPECIAL_CHARS);
        $event_date = filter_var($postVars['event_date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $event_time = filter_var($postVars['event_time'], FILTER_SANITIZE_SPECIAL_CHARS);

        if(empty($postVars['event_private'])) {
            $event_private = 0;
        } else {
            $event_private = filter_var($postVars['event_private'], FILTER_SANITIZE_SPECIAL_CHARS);
            $event_private = 1;
        }

        $format_date = $event_date.' '.$event_time.':00';
        $dateint = strtotime($format_date);

        EntityGuilds::insertEvents([
            'guild_id' => $guild_id,
            'name' => $event_name,
            'description' => $event_text,
            'date' => $dateint,
            'private' => $event_private,
        ]);
        $status = 'Event created successfully!';
        return self::viewCreateGuildEvents($request, $name, $status);

    }

    public static function viewCreateGuildEvents($request, $name, $status = null)
    {
        $content = View::render('pages/guilds/createguildevents', [
            'status' => $status,
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
        ]);
        return parent::getBase('Guilds', $content, 'guilds');
    }

    public static function viewGuildEvents($request, $name)
    {
        $guild_id = self::convertGuildName($name);
        $dbEvents = EntityGuilds::getEvents('guild_id = "'.$guild_id.'"', 'id DESC');
        while($event = $dbEvents->fetchObject()) {
            $events[] = [
                'name' => $event->name,
                'description' => $event->description,
                'date' => date('M d Y, h:i', $event->date),
                'private' => $event->private,
            ];
        }

        $content = View::render('pages/guilds/guildevents', [
            'events' => $events ?? null,
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
            'isleader' => FunctionGuilds::verifyAccountLeader($guild_id),
        ]);
        return parent::getBase('Guilds', $content, 'guilds');
    }
}
