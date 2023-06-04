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

class Badges
{
    public static function getPlayerBadges($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('player_badges'))->select($where, $order, $limit, $fields);
    }

    public static function getServerBadges($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_badges'))->select($where, $order, $limit, $fields);
    }
}
