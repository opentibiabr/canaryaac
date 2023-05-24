<?php
/**
 * Market Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

enum StoreHistoryEventType: int
{
    case Transaction = 0;
    case Gift = 1;
    case Refunded = 2;
};

enum StoreHistoryCoinType: int
{
    case TibiaCoin = 0;
    case TransferableTibiaCoin = 1;
    case TournamentPoints = 2;
};

class Store{
    public static function insertHistoryEvent($values){
        return (new Database('store_history'))->insert($values);
    }

    public static function getHistoryEvent($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('store_history'))->select($where, $order, $limit, $fields);
    }
}