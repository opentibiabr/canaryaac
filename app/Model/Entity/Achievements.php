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

class Achievements
{
    public static function getAchievements($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_achievements'))->select($where, $order, $limit, $fields);
    }

    public static function insertAchievements($values = null)
    {
        return (new Database('canary_achievements'))->insert($values);
    }

    public static function deleteAchievements($where = null)
    {
        return (new Database('canary_achievements'))->delete($where);
    }

    public static function updateAchievements($where = null, $values = null)
    {
        return (new Database('canary_achievements'))->update($where, $values);
    }
}
