<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\Guilds as EntityGuild;
use App\Model\Entity\Player as EntityPlayer;
use App\Session\Admin\Login as SessionAdminLogin;

class Guilds
{
    public static function getGuilds()
    {
        $arrayGuilds = [];
        $dbGuilds = EntityGuild::getGuilds();
        while($guild = $dbGuilds->fetchObject()) {
            $arrayGuilds[] = [
                'id' => (int)$guild->id,
                'level' => (int)$guild->level,
                'name' => $guild->name,
                'ownerid' => (int)$guild->ownerid,
                'creationdata' => date('d M Y H:i', $guild->creationdata),
                'motd' => $guild->motd,
                'residence' => $guild->residence,
                'balance' => (int)$guild->balance,
                'points' => (int)$guild->points,
                'description' => $guild->description,
                'logo_name' => $guild->logo_name,
            ];
        }
        return $arrayGuilds;
    }

    public static function getGuildbyId($guild_id)
    {
        $arrayGuild = [];
        $guild = EntityGuild::getGuilds('id = "'.$guild_id.'"')->fetchObject();
        $arrayGuild = [
            'id' => (int)$guild->id,
            'level' => (int)$guild->level,
            'name' => $guild->name,
            'ownerid' => (int)$guild->ownerid,
            'creationdata' => date('M d Y', $guild->creationdata),
            'motd' => $guild->motd,
            'residence' => $guild->residence,
            'balance' => (int)$guild->balance,
            'points' => (int)$guild->points,
            'description' => $guild->description,
            'logo_name' => $guild->logo_name,
        ];
        return $arrayGuild;
    }

    public static function getGuildbyWorldId($world_id)
    {
        $select_guilds = EntityGuild::getGuilds('world_id = "'.$world_id.'"');
        while ($guild = $select_guilds->fetchObject()) {
            $arrayGuild[] = [
                'id' => (int)$guild->id,
                'level' => (int)$guild->level,
                'name' => $guild->name,
                'ownerid' => (int)$guild->ownerid,
                'creationdata' => date('M d Y', $guild->creationdata),
                'motd' => $guild->motd,
                'residence' => $guild->residence,
                'balance' => (int)$guild->balance,
                'points' => (int)$guild->points,
                'description' => $guild->description,
                'logo_name' => $guild->logo_name,
            ];
        }

        return $arrayGuild ?? '';
    }

    public static function convertRankGuild($rank_id)
    {
        $dbRank = EntityGuild::getRanks('id = "'.$rank_id.'"')->fetchObject();
        $arrayPlayerRank = [
            'id' => $dbRank->id,
            'guild_id' => $dbRank->guild_id,
            'name' => $dbRank->name,
            'rank_level' => $dbRank->level,
        ];
        return $arrayPlayerRank ?? null;
    }

    public static function getRanks($guild_id)
    {
        $dbRanks = EntityGuild::getRanks('guild_id = "'.$guild_id.'"');
        while($rank = $dbRanks->fetchObject()) {
            $arrayRank[] = [
                'id' => $rank->id,
                'guild_id' => $rank->guild_id,
                'name' => $rank->name,
                'level' => $rank->level,
            ];
        }
        return $arrayRank ?? null;
    }

    public static function getMembersbyRank($guild_id, $rank_id)
    {
        $dbGuild_Members = EntityGuild::getMembership('guild_id = "'.$guild_id.'" AND rank_id = "'.$rank_id.'"');
        while ($member = $dbGuild_Members->fetchObject()) {
            $dbNameMember = EntityPlayer::getPlayer('id = "'.$member->player_id.'"')->fetchObject();
            $arrayMembers[] = [
                'player_id' => $member->player_id,
                'player_name' => $dbNameMember->name,
                'player_vocation' => Player::convertVocation($dbNameMember->vocation),
                'player_level' => $dbNameMember->level,
                'player_online' => Player::isOnline($dbNameMember->id),
                'guild_id' => $member->guild_id,
                'rank_id' => $member->rank_id,
                'nick' => $member->nick,
                'date' => date('M d Y', $member->date),
            ];
        }
        return $arrayMembers ?? null;
    }

    public static function getAllMembers($guild_id)
    {
        $dbGuild_Ranks = EntityGuild::getRanks('guild_id = "'.$guild_id.'"', 'level DESC');
        while($guild_ranks = $dbGuild_Ranks->fetchObject()) {
            $arrayMembers = self::getMembersbyRank($guild_id, $guild_ranks->id);
            $arrayRanks[] = [
                'rank_name' => $guild_ranks->name,
                'rank_members' => $arrayMembers,
            ];
        }
        return $arrayRanks;
    }

    public static function getGuildMembership($guild_id)
    {
        $members = [];
        $dbMembers = EntityGuild::getMembership('guild_id = "'.$guild_id.'"');
        while($member = $dbMembers->fetchObject()) {
            $dbNameMember = EntityPlayer::getPlayer('id = "'.$member->player_id.'"')->fetchObject();
            $members[] = [
                'player_name' => $dbNameMember->name,
                'player_id' => $member->player_id,
                'guild_id' => $member->guild_id,
                'rank_id' => $member->rank_id,
                'nick' => $member->nick,
            ];
        }
        return $members;
    }

    public static function getGuildInvites($guild_id)
    {
        $invites = [];
        $dbInvites = EntityGuild::getInvites('guild_id = "'.$guild_id.'"');
        while($invited = $dbInvites->fetchObject()) {
            $dbNameInvites = EntityPlayer::getPlayer('id = "'.$invited->player_id.'"')->fetchObject();
            $invites[] = [
                'player_name' => $dbNameInvites->name,
                'player_id' => $invited->player_id,
                'guild_id' => $invited->guild_id,
                'date' => date('M d Y', $invited->date),
            ];
        }
        return $invites;
    }

    public static function verifyAccountLeader($guild_id)
    {
        $verifyLeader = false;
        if (SessionAdminLogin::isLogged() == true) {
            $LoggedId = SessionAdminLogin::idLogged();

            $dbMembers = EntityGuild::getMembership('guild_id = "' . $guild_id . '"');
            while ($member = $dbMembers->fetchObject()) {

                $rankConverted = self::convertRankGuild($member->rank_id);
                $dbPlayer = EntityPlayer::getPlayer('id = "' . $member->player_id . '" AND account_id = "' . $LoggedId . '"')->fetchObject();

                if ($dbPlayer == true) {
                    if($rankConverted['rank_level'] == 3) {
                        $verifyLeader = true;
                    }
                }
            }
        }
        return $verifyLeader;
    }

    public static function verifyAccountViceLeader($guild_id)
    {
        $verifyViceLeader = false;
        if (SessionAdminLogin::isLogged() == true) {
            $LoggedId = SessionAdminLogin::idLogged();
            $dbMembers = EntityGuild::getMembership('guild_id = "' . $guild_id . '" AND rank_id = 2');
            while ($member = $dbMembers->fetchObject()) {
                $dbPlayer = EntityPlayer::getPlayer('id = "' . $member->player_id . '" AND account_id = "' . $LoggedId . '"')->fetchObject();
                if ($dbPlayer == true) {
                    $verifyViceLeader = true;
                }
            }
        }
        return $verifyViceLeader;
    }

    public static function verifyAccountMember($guild_id)
    {
        $verifyMember = false;
        if (SessionAdminLogin::isLogged() == true) {
            $LoggedId = SessionAdminLogin::idLogged();
            $dbMembers = EntityGuild::getMembership('guild_id = "' . $guild_id . '"');
            while ($member = $dbMembers->fetchObject()) {
                $dbPlayer = EntityPlayer::getPlayer('id = "' . $member->player_id . '" AND account_id = "' . $LoggedId . '"')->fetchObject();
                if ($dbPlayer == true) {
                    $verifyMember = true;
                }
            }
        }
        return $verifyMember;
    }

    public static function verifyAccountInvited($guild_id)
    {
        $verifyInvited = false;
        if (SessionAdminLogin::isLogged() == true) {
            $LoggedId = SessionAdminLogin::idLogged();
            $dbInvited = EntityGuild::getInvites('guild_id = "' . $guild_id . '"');
            while ($invited = $dbInvited->fetchObject()) {
                $dbPlayer = EntityPlayer::getPlayer('id = "' . $invited->player_id . '" AND account_id = "' . $LoggedId . '"')->fetchObject();
                if ($dbPlayer == true) {
                    $verifyInvited = true;
                }
            }
        }
        return $verifyInvited;
    }
}
