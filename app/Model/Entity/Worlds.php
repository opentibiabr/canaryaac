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

class Worlds
{
    public static function getPlayersOnline($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('players_online'))->select($where, $order, $limit, $fields);
    }

    public static function getPlayers($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('players'))->select($where, $order, $limit, $fields);
    }

    public static function getWorlds($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_worlds'))->select($where, $order, $limit, $fields);
    }

    public static function getTowns($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_towns'))->select($where, $order, $limit, $fields);
    }

    public static function getServerConfig($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('server_config'))->select($where, $order, $limit, $fields);
    }
}
