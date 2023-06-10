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

class Items
{
    public static function getItems($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_items'))->select($where, $order, $limit, $fields);
    }

    public static function insertItems($values = null)
    {
        return (new Database('canary_items'))->insertMany($values);
    }

    public static function updateItems($where = null, $values = null)
    {
        return (new Database('canary_items'))->update($where, $values);
    }

    public static function deleteItems($where = null)
    {
        return (new Database('canary_items'))->delete($where);
    }

}
