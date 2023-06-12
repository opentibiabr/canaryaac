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

class Player
{
    public static function getPlayer($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('players'))->select($where, $order, $limit, $fields);
    }

    public static function getPlayerLike($where = null, $like = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('players'))->selectLike($where, $like, $order, $limit, $fields);
    }

    public static function updatePlayer($where = null, $values = null)
    {
        return (new Database('players'))->update($where, $values);
    }

    public static function deletePlayer($where = null)
    {
        return (new Database('players'))->delete($where);
    }

    public static function getCharms($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_charms'))->select($where, $order, $limit, $fields);
    }

    public static function getDeaths($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_deaths'))->select($where, $order, $limit, $fields);
    }

    public static function getDepotItems($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_depotitems'))->select($where, $order, $limit, $fields);
    }

    public static function getHirelings($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_hirelings'))->select($where, $order, $limit, $fields);
    }

    public static function getInboxItems($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_inboxitems'))->select($where, $order, $limit, $fields);
    }

    public static function getEquipaments($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_items'))->select($where, $order, $limit, $fields);
    }

    public static function getKills($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_kills'))->select($where, $order, $limit, $fields);
    }

    public static function getMisc($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_misc'))->select($where, $order, $limit, $fields);
    }

    public static function getNameLocks($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_namelocks'))->select($where, $order, $limit, $fields);
    }

    public static function getPrey($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_prey'))->select($where, $order, $limit, $fields);
    }

    public static function getRewards($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_rewards'))->select($where, $order, $limit, $fields);
    }

    public static function getStash($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_stash'))->select($where, $order, $limit, $fields);
    }

    public static function getStorage($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_storage'))->select($where, $order, $limit, $fields);
    }

    public static function getTaskHunt($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_taskhunt'))->select($where, $order, $limit, $fields);
    }

    public static function getDisplay($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_display'))->select($where, $order, $limit, $fields);
    }

    public static function updateDisplay($where = null, $values = null)
    {
        return (new Database('player_display'))->update($where, $values);
    }

    public static function insertDisplay($values = null)
    {
        return (new Database('player_display'))->insert($values);
    }

    public static function getGuildOwner($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guilds'))->select($where, $order, $limit, $fields);
    }

    public static function getGuildMember($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('guild_membership'))->select($where, $order, $limit, $fields);
    }

    public static function getAccount($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('accounts'))->select($where, $order, $limit, $fields);
    }

    public static function getOnline($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('players_online'))->select($where, $order, $limit, $fields);
    }

    public static function getHouse($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('houses'))->select($where, $order, $limit, $fields);
    }

    public static function getAllItems($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_items'))->select($where, $order, $limit, $fields);
    }

}
