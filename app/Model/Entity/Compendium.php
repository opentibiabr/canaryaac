<?php
/**
 * Compendium Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

class Compendium
{
    public static function getCompendium($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_compendium'))->select($where, $order, $limit, $fields);
    }

    public static function insertCompendium($values = null)
    {
        return (new Database('canary_compendium'))->insert($values);
    }

    public static function deleteCompendium($where = null)
    {
        return (new Database('canary_compendium'))->delete($where);
    }

    public static function updateCompendium($where = null, $values = null)
    {
        return (new Database('canary_compendium'))->update($where, $values);
    }
}
