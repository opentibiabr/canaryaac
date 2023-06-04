<?php
/**
 * Groups Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

class Groups
{
    public static function getGroups($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_groups'))->select($where, $order, $limit, $fields);
    }

    public static function deleteGroup($where = null)
    {
        return (new Database('canary_groups'))->delete($where);
    }

    public static function updateGroup($where = null, $values = null)
    {
        return (new Database('canary_groups'))->update($where, $values);
    }

    public static function insertGroup($values = null)
    {
        return (new Database('canary_groups'))->insert($values);
    }
}
