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

class Account
{
    public static function getAccount($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('accounts'))->select($where, $order, $limit, $fields);
    }

    public static function getAccountRegistration($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('account_registration'))->select($where, $order, $limit, $fields);
    }

    public static function insertRegister($values = null)
    {
        return (new Database('account_registration'))->insert($values);
    }

    public static function updateRegister($where = null, $values = null)
    {
        return (new Database('account_registration'))->update($where, $values);
    }

    public static function updateAccount($where = null, $values = null)
    {
        return (new Database('accounts'))->update($where, $values);
    }

    public static function getAuthentication($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('account_authentication'))->select($where, $order, $limit, $fields);
    }

    public static function insertAuthentication($values = null)
    {
        return (new Database('account_authentication'))->insert($values);
    }

    public static function updateAuthentication($where = null, $values = null)
    {
        return (new Database('account_authentication'))->update($where, $values);
    }

    public static function deleteAuthentication($where = null)
    {
        return (new Database('account_authentication'))->delete($where);
    }
}
