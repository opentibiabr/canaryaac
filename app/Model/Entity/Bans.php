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

class Bans
{
    public static function getAccountBans($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('account_bans'))->select($where, $order, $limit, $fields);
    }

    public static function insertAccountBan($values)
    {
        return (new Database('account_bans'))->insert($values);
    }

    public static function deleteAccountBan($where)
    {
        return (new Database('account_bans'))->delete($where);
    }

    public static function getAccountBansHistory($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('account_ban_history'))->select($where, $order, $limit, $fields);
    }

    public static function getIpBans($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('ip_bans'))->select($where, $order, $limit, $fields);
    }
}
