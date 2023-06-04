<?php
/**
 * Found Guild Class
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
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Server as FunctionServer;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class Found extends Base
{
    public static function insertFoundGuild($request)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();

        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        if(empty($postVars['password'])) {
            $status = 'You need to enter a password.';
            return self::viewFoundGuild($request, $status);
        }
        $filter_pass = filter_var($postVars['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        $convert_pass = sha1($filter_pass);
        $dbAccountLogged = EntityPlayer::getAccount('id = "'.$idLogged.'"')->fetchObject();
        if($dbAccountLogged->password != $convert_pass) {
            $status = 'Something went wrong with the password.';
            return self::viewFoundGuild($request, $status);
        }
        if(empty($postVars['guild_name'])) {
            $status = 'Enter a name.';
            return self::viewFoundGuild($request, $status);
        }
        $filter_name = filter_var($postVars['guild_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        if (strlen($filter_name) < 5) {
            $status = 'The name must contain at least 5 characters.';
            return self::viewFoundGuild($request, $status);
        }
        $dbGuilds = EntityGuilds::getGuilds('name = "'.$filter_name.'"')->fetchObject();
        if(!empty($dbGuilds)) {
            $status = 'A Guild with this name already exists.';
            return self::viewFoundGuild($request, $status);
        }
        if(empty($postVars['guild_leader'])) {
            $status = 'Select a leader.';
            return self::viewFoundGuild($request, $status);
        }
        $filter_leader = filter_var($postVars['guild_leader'], FILTER_SANITIZE_SPECIAL_CHARS);
        $dbPlayerId = EntityPlayer::getPlayer('name = "'.$filter_leader.'" AND account_id = "'.$idLogged.'"')->fetchObject();
        if($dbPlayerId->deletion == 1) {
            $status = 'You cannot select a character with a deleted status.';
            return self::viewFoundGuild($request, $status);
        }
        $dbPlayerLeader = EntityPlayer::getPlayer('name = "'.$filter_leader.'" AND account_id = "'.$idLogged.'"');
        while ($player = $dbPlayerLeader->fetchObject()) {
            if ($player->level < $websiteInfo->player_guild) {
                $status = 'The minimum level to found a Guild is '.$websiteInfo->player_guild.'.';
                return self::viewFoundGuild($request, $status);
            }
            $dbMembers = EntityGuilds::getMembership('player_id = "'.$player->id.'"')->fetchObject();
            if (!empty($dbMembers)) {
                $status = 'This character is already in a Guild.';
                return self::viewFoundGuild($request, $status);
            }
        }
        if (empty($postVars['guild_world'])) {
            $status = 'You need to select a World.';
            return self::viewFoundGuild($request, $status);
        }
        if (!filter_var($postVars['guild_world'], FILTER_VALIDATE_INT)) {
            $status = 'You have selected an invalid World.';
            return self::viewFoundGuild($request, $status);
        }
        $filter_world = filter_var($postVars['guild_world'], FILTER_SANITIZE_NUMBER_INT);
        if ($dbPlayerId->world != $filter_world) {
            $status = 'The character does not match the selected World.';
            return self::viewFoundGuild($request, $status);
        }
        $insertGuild = EntityGuilds::insertGuild([
            'level' => 1,
            'name' => $filter_name,
            'creationdata' => strtotime(date('Y-m-d H:i:s')),
            'ownerid' => $dbPlayerId->id,
            'motd' => '',
            'residence' => 0,
            'balance' => 0,
            'points' => 0,
            'description' => '',
            'logo_name' => 'default_logo',
            'world_id' => $filter_world
        ]);
        $dbRanks = EntityGuilds::getRanks('guild_id = "'.$insertGuild.'" AND level = 3')->fetchObject();
        self::insertMemberInFound($dbPlayerId->id, $insertGuild, $dbRanks->id);
        $status = 'Guild created successfully.';
        return self::viewFoundGuild($request, $status);
    }

    public static function insertMemberInFound($player_id, $guild_id, $rank_id)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $insertMember = EntityGuilds::insertJoinMember([
            'player_id' => $player_id,
            'guild_id' => $guild_id,
            'rank_id' => $rank_id,
            'nick' => '',
            'date' => strtotime(date('Y-m-d H:i:s')),
        ]);
        return $insertMember;
    }

    public static function viewFoundGuild($request, $status = null)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $dbPlayersAccount = EntityPlayer::getPlayer('account_id = "'.$idLogged.'" AND deletion = "0"');
        while($player = $dbPlayersAccount->fetchObject()) {
            $dbMembers = EntityGuilds::getMembership('player_id = "'.$player->id.'"')->fetchObject();
            if(empty($dbMembers)) {
                $arrayPlayers[] = [
                    'id' => $player->id,
                    'name' => $player->name,
                ];
            }
        }
        $content = View::render('pages/guilds/found', [
            'status' => $status,
            'players' => $arrayPlayers ?? null,
            'worlds' => FunctionServer::getWorlds(),
        ]);
        return parent::getBase('Guilds', $content, 'guilds');
    }
}
