<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\Guilds as EntityGuild;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class Guilds extends Base
{
    public static function getAllGuilds()
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $select = EntityGuild::getGuilds();
        while ($obAllGuilds = $select->fetchObject()) {
            $dbPlayerNameAllGuilds = EntityPlayer::getPlayer('id = "' . $obAllGuilds->ownerid . '"')->fetchObject();
            $allGuilds[] = [
                'id' => (int) $obAllGuilds->id,
                'level' => (int) $obAllGuilds->level,
                'name' => $obAllGuilds->name,
                'ownerid' => (int) $obAllGuilds->ownerid,
                'owner' => $dbPlayerNameAllGuilds->name,
                'creationdata' => date('d M Y H:i', strtotime($obAllGuilds->creationdata)),
                'motd' => $obAllGuilds->motd,
                'residence' => $obAllGuilds->residence,
                'balance' => (int) $obAllGuilds->balance,
                'points' => (int) $obAllGuilds->points,
                'description' => $obAllGuilds->description,
                'logo_name' => $obAllGuilds->logo_name,
            ];
        }
        return $allGuilds ?? false;
    }

    public static function updateGuild($request)
    {
        $postVars = $request->getPostVars();
        $guild_id = $postVars['guildid'];
        $guild_level = $postVars['level'];
        $guild_name = $postVars['name'];
        $guild_ownerid = $postVars['ownerid'];
        $guild_creationdata = $postVars['creationdata'];
        $guild_motd = $postVars['motd'];
        $guild_residence = $postVars['residence'];
        $guild_balance = $postVars['balance'];
        $guild_points = $postVars['points'];

        $update = [
            'level' => $postVars['level'],
            'name' => $postVars['name'],
            'ownerid' => $postVars['ownerid'],
            'creationdata' => $postVars['creationdata'],
            'motd' => $postVars['motd'],
            'residence' => $postVars['residence'],
            'balance' => $postVars['balance'],
            'points' => $postVars['points'],
        ];

        EntityGuild::updateGuild('id = "' . $guild_id . '"', $update);

        $status = Alert::getSuccess('Guild editada com sucesso!') ?? null;
        return self::viewGuilds($request, $status);
    }

    public static function deleteGuild($request)
    {
        $postVars = $request->getPostVars();
        $guild_id = $postVars['guildid'];
        EntityGuild::deleteGuild('id = "' . $guild_id . '"');

        $status = Alert::getSuccess('Guild deletada com sucesso!') ?? null;
        return self::viewGuilds($request, $status);
    }

    public static function viewEditGuild($request, $id, $errorMessage = null)
    {
        $dbGuild = EntityGuild::getGuilds('id = "' . $id . '"')->fetchObject();
        $dbPlayerGuild = EntityPlayer::getPlayer('id = "' . $dbGuild->ownerid . '"')->fetchObject();
        $guild = [
            'name' => $dbGuild->name,
            'owner' => $dbPlayerGuild->name,
            'level' => $dbGuild->level,
            'motd' => $dbGuild->motd,
            'balance' => $dbGuild->balance,
            'points' => $dbGuild->points,
            'logo_name' => $dbGuild->logo_name,
        ];


        $dbMembers = EntityGuild::getMembership('guild_id = "' . $dbGuild->id . '"');
        while ($member = $dbMembers->fetchObject()) {
            $dbNameMember = EntityPlayer::getPlayer('id = "' . $member->player_id . '"')->fetchObject();
            $members[] = [
                'player_name' => $dbNameMember->name,
                'player_id' => $member->player_id,
                'guild_id' => $member->guild_id,
                'rank_id' => $member->rank_id,
                'nick' => $member->nick,
            ];
        }

        $dbInvites = EntityGuild::getInvites('guild_id = "' . $dbGuild->id . '"');
        while ($invited = $dbInvites->fetchObject()) {
            $dbNameInvites = EntityPlayer::getPlayer('id = "' . $invited->player_id . '"')->fetchObject();
            $invites[] = [
                'player_name' => $dbNameInvites->name,
                'player_id' => $invited->player_id,
                'guild_id' => $invited->guild_id,
                'date' => date('d/m/Y H:i', strtotime($invited->date)),
            ];
        }

        $content = View::render('admin/modules/guilds/view', [
            'status' => $errorMessage,
            'guild' => $guild ?? null,
            'members' => $members ?? null,
            'invites' => $invites ?? null,
        ]);
        return parent::getPanel('Edit Guild', $content, 'guilds');
    }

    public static function viewGuilds($request, $errorMessage = null)
    {
        $content = View::render('admin/modules/guilds/index', [
            'status' => $errorMessage,
            'guilds' => self::getAllGuilds(),
            'total_guilds' => (int) EntityGuild::getGuilds(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
            'total_guildmembership' => (int) EntityGuild::getMembership(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
            'total_guildwarskills' => (int) EntityGuild::getWars(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
            'total_guildwars' => (int) EntityGuild::getWars(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
        ]);

        return parent::getPanel('Guilds', $content, 'guilds');
    }
}
