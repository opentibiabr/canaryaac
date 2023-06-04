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

class Houses
{
    public static function getHouses($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('houses'))->select($where, $order, $limit, $fields);
    }

    public static function insertHouses($values = null)
    {
        return (new Database('houses'))->insert($values);
    }

    public static function deleteHouse($where = null)
    {
        return (new Database('houses'))->delete($where);
    }

    public static function updateHouse($where = null, $values = null)
    {
        return (new Database('houses'))->update($where, $values);
    }

    public static function getHousesList($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('house_lists'))->select($where, $order, $limit, $fields);
    }

    public static function getTowns($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_towns'))->select($where, $order, $limit, $fields);
    }
}
