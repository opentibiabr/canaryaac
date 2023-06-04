<?php
/**
 * Team Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Support;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Player as FunctionsPlayer;

class Team extends Base
{
    public static function getTeam()
    {
        $select_players = EntityPlayer::getPlayer('group_id >= "2"');
        while ($player = $select_players->fetchObject()) {
            $arrayTeam[] = [
                'name' => $player->name,
                'group' => FunctionsPlayer::convertGroup($player->group_id),
            ];
        }
        return $arrayTeam ?? [];
    }
    public static function viewTeam($request)
    {
        $content = View::render('pages/support/team', [
            'players' => self::getTeam()
        ]);
        return parent::getBase('Team', $content, $currentPage = 'team');
    }
}
