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

class Payments
{
    public static function getPayment($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_payments'))->select($where, $order, $limit, $fields);
    }

    public static function insertPayment($values = null)
    {
        return (new Database('canary_payments'))->insert($values);
    }

    public static function updatePayment($where = null, $values = null)
    {
        return (new Database('canary_payments'))->update($where, $values);
    }

    public static function deletePayment($where = null)
    {
        return (new Database('canary_payments'))->delete($where);
    }
}
