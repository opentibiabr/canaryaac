<?php
/**
 * LeaveGuild Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Guilds;

use App\Controller\Pages\Base;
use App\Utils\View;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Model\Functions\Guilds as FunctionGuilds;
use App\Model\Entity\Player as EntityPlayer;

class LeaveGuild extends Base
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

    public static function deleteLeaveGuild($request, $name)
    {
        $postVars = $request->getPostVars();
        $idLogged = SessionAdminLogin::idLogged();
        $guild_id = self::convertGuildName($name);
        if(empty($postVars['character_leave'])) {
            $status = 'Select a character.';
            return self::viewLeaveGuild($request, $name, $status);
        }
        $input_character = filter_var($postVars['character_leave'], FILTER_SANITIZE_SPECIAL_CHARS);
        $dbPlayer = EntityPlayer::getPlayer('account_id = "'.$idLogged.'" AND name = "'.$input_character.'"')->fetchObject();
        if(empty($dbPlayer)) {
            $status = 'Invalid character.';
            return self::viewLeaveGuild($request, $name, $status);
        }
        $dbMember = EntityGuilds::getMembership('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"')->fetchObject();
        if(empty($dbMember)) {
            $status = 'Invalid character or belongs to another Guild.';
            return self::viewLeaveGuild($request, $name, $status);
        }
        EntityGuilds::deleteMember('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"');
        $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
    }

    public static function viewLeaveGuild($request, $name, $status = null)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $guild_id = self::convertGuildName($name);
        $dbPlayersAccount = EntityPlayer::getPlayer('account_id = "'.$idLogged.'"');
        while($player = $dbPlayersAccount->fetchObject()) {
            $dbMember = EntityGuilds::getMembership('player_id = "'.$player->id.'" AND guild_id = "'.$guild_id.'"');
            while($members = $dbMember->fetchObject()) {
                $convertRank = FunctionGuilds::convertRankGuild($members->rank_id);
                if($convertRank['rank_level'] != 3) {
                    $arrayMembers[] = [
                        'id' => $player->id,
                        'name' => $player->name,
                    ];
                }
            }
        }
        $content = View::render('pages/guilds/leaveguild', [
            'status' => $status,
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
            'members' => $arrayMembers ?? null,
        ]);
        return parent::getBase('Guild', $content, 'guilds');
    }
}
