<?php
/**
 * Countdowns Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

class Countdowns
{
    public static function getCountdowns($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_countdowns'))->select($where, $order, $limit, $fields);
    }

    public static function insertCountdowns($values = null)
    {
        return (new Database('canary_countdowns'))->insert($values);
    }

    public static function updateCountdowns($where = null, $values = null)
    {
        return (new Database('canary_countdowns'))->update($where, $values);
    }

    public static function deleteCountdowns($where = null)
    {
        return (new Database('canary_countdowns'))->delete($where);
    }
}
