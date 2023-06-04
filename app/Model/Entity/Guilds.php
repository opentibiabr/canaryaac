<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

class Guilds
{
    public static function getGuilds($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guilds'))->select($where, $order, $limit, $fields);
    }

    public static function deleteGuild($where = null)
    {
        return (new Database('guilds'))->delete($where);
    }

    public static function updateGuild($where = null, $values = null)
    {
        return (new Database('guilds'))->update($where, $values);
    }

    public static function insertGuild($values = null)
    {
        return (new Database('guilds'))->insert($values);
    }

    public static function getKills($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guildwar_kills'))->select($where, $order, $limit, $fields);
    }

    public static function getInvites($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_invites'))->select($where, $order, $limit, $fields);
    }

    public static function insertInvite($values = null)
    {
        return (new Database('guild_invites'))->insert($values);
    }

    public static function deleteInvite($where = null)
    {
        return (new Database('guild_invites'))->delete($where);
    }

    public static function getApplications($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_applications'))->select($where, $order, $limit, $fields);
    }

    public static function insertMyApplication($values = null)
    {
        return (new Database('guild_applications'))->insert($values);
    }

    public static function deleteMyApplication($where = null)
    {
        return (new Database('guild_applications'))->delete($where);
    }

    public static function updateApplication($where = null, $values = null)
    {
        return (new Database('guild_applications'))->update($where, $values);
    }

    public static function getMembership($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_membership'))->select($where, $order, $limit, $fields);
    }

    public static function insertJoinMember($values = null)
    {
        return (new Database('guild_membership'))->insert($values);
    }

    public static function deleteMember($where = null)
    {
        return (new Database('guild_membership'))->delete($where);
    }

    public static function updateRankOnMember($where = null, $values = null)
    {
        return (new Database('guild_membership'))->update($where, $values);
    }

    public static function updateMember($where = null, $values = null)
    {
        return (new Database('guild_membership'))->update($where, $values);
    }

    public static function getRanks($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_ranks'))->select($where, $order, $limit, $fields);
    }

    public static function updateRank($where = null, $values = null)
    {
        return (new Database('guild_ranks'))->update($where, $values);
    }

    public static function getWars($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_wars'))->select($where, $order, $limit, $fields);
    }

    public static function insertWar($values = null)
    {
        return (new Database('guild_wars'))->insert($values);
    }

    public static function updateWar($where = null, $values = null)
    {
        return (new Database('guild_wars'))->update($where, $values);
    }

    public static function deleteWar($where = null)
    {
        return (new Database('guild_wars'))->delete($where);
    }

    public static function insertEvents($values = null)
    {
        return (new Database('guild_events'))->insert($values);
    }

    public static function getEvents($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_events'))->select($where, $order, $limit, $fields);
    }
}
