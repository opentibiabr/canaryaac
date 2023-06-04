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

class Highscores
{
    public static function getHighscoresEntity($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('players'))->select($where, $order, $limit, $fields);
    }
}
