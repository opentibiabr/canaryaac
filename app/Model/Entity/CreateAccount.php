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

class CreateAccount
{
    public static function createAccount($account)
    {
        $created = (new Database('accounts'))->insert($account);
        return $created;
    }

    public static function createCharacter($character)
    {
        return (new Database('players'))->insert($character);
    }

    public static function getPlayerSamples($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_samples'))->select($where, $order, $limit, $fields);
    }
}
