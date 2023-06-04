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

class Creatures
{
    public static function getCreatures($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_creatures'))->select($where, $order, $limit, $fields);
    }

    public static function insertCreature($values = null)
    {
        return (new Database('canary_creatures'))->insert($values);
    }

    public static function deleteCreature($where = null)
    {
        return (new Database('canary_creatures'))->delete($where);
    }

    public static function getBoss($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_boss'))->select($where, $order, $limit, $fields);
    }

    public static function insertBoss($values = null)
    {
        return (new Database('canary_boss'))->insert($values);
    }

    public static function deleteBoss($where = null)
    {
        return (new Database('canary_creatures'))->delete($where);
    }
}
