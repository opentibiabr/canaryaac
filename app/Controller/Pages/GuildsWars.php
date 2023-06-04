<?php
/**
 * GuildsWars Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use \App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Model\Functions\Guilds;

class GuildsWars extends Base{

    public static function getGuildWars($war_status)
    {
        $select_wars = EntityGuilds::getWars([ 'status' => $war_status]);
        while ($war = $select_wars->fetchObject()) {

            $select_guild1 = Guilds::getGuildbyId($war->guild1);
            $select_guild2 = Guilds::getGuildbyId($war->guild2);

            $arrayWars[] = [
                'guild1' => $war->guild1,
                'guild2' => $war->guild2,
                'name1' => $war->name1,
                'logo1' => $select_guild1,
                'name2' => $war->name2,
                'logo2' => $select_guild2,
                'started' => $war->started,
                'ended' => $war->ended,
                'price1' => $war->price1,
                'price2' => $war->price2,
                'frags' => $war->frags,
            ];
        }
        return $arrayWars ?? [];
    }

    public static function viewActiveWars($request)
    {
        $content = View::render('pages/guildwars/active', [
            'wars_active' => self::getGuildWars(1),
        ]);
        return parent::getBase('Guild Wars', $content, $currentPage = 'activewars');
    }

    public static function viewPendingWars($request)
    {
        $content = View::render('pages/guildwars/pending', [
            'wars_pending' => self::getGuildWars(2),
        ]);
        return parent::getBase('Guild Wars', $content, $currentPage = 'pendingwars');
    }

    public static function viewSurrenderWars($request)
    {
        $content = View::render('pages/guildwars/surrender', [
            'wars_surrender' => self::getGuildWars(3),
        ]);
        return parent::getBase('Guild Wars', $content, $currentPage = 'surrenderwars');
    }

    public static function viewEndedWars($request)
    {
        $content = View::render('pages/guildwars/ended', [
            'wars_surrender' => self::getGuildWars(4),
        ]);
        return parent::getBase('Guild Wars', $content, $currentPage = 'endedwars');
    }

}