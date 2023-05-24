<?php
/**
 * NotifyPagSeguro Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment;

use App\Model\Entity\PaymentStatus;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\Account as EntityAccount;
use App\Model\Entity\Store as StoreHistory;
use App\Model\Entity\StoreHistoryEventType;
use App\Model\Entity\StoreHistoryCoinType;

class Payments {
    public static function ApprovePayment($reference, $net_payment) {
        $dbPayment = EntityPayments::getPayment('reference = "'.$reference.'"')->fetchObject();
        $dbAccount = EntityAccount::getAccount('id = "'.$dbPayment->account_id.'"')->fetchObject();
        $finalcoins = $dbAccount->coins + $dbPayment->total_coins;
        EntityPayments::updatePayment('reference = "'.$reference.'"', [
            'status' => PaymentStatus::Approved->value,
            'net_payment' => $net_payment,
        ]);
        EntityAccount::updateAccount('id = "'.$dbPayment->account_id.'"', ['coins' => $finalcoins,]);
        StoreHistory::insertHistoryEvent([
            'account_id' => $dbAccount->id,
            'mode' => StoreHistoryEventType::Transaction->value,
            'description' => 'Purchased via site',
            'coin_type' => StoreHistoryCoinType::TransferableTibiaCoin->value,
            'coin_amount' => $dbPayment->total_coins,
            'time' => $dbPayment->date
        ]);
    }

    public static function RefundPayment($reference) {
        $dbPayment = EntityPayments::getPayment('reference = "'.$reference.'"')->fetchObject();
        $dbAccount = EntityAccount::getAccount('id = "'.$dbPayment->account_id.'"')->fetchObject();
        
        EntityPayments::updatePayment('reference = "'.$reference.'"', [
            'status' => PaymentStatus::Refunded->value,
            'net_payment' => 0,
        ]);

        if ($dbPayment->status != PaymentStatus::Approved->value)
            return;

        $finalcoins = $dbAccount->coins - $dbPayment->total_coins;
        EntityAccount::updateAccount('id = "'.$dbPayment->account_id.'"', ['coins' => $finalcoins,]);
        StoreHistory::insertHistoryEvent([
            'account_id' => $dbAccount->id,
            'mode' => StoreHistoryEventType::Transaction->value,
            'description' => 'Refunded or Charged Back payment',
            'coin_type' => StoreHistoryCoinType::TransferableTibiaCoin->value,
            'coin_amount' => -$dbPayment->total_coins,
            'time' => $dbPayment->date
        ]);
    }
    
    public static function SetPaymentStatus($reference, PaymentStatus $status) {
        EntityPayments::updatePayment('reference = "'.$reference.'"', ['status' => $status->value,]);
    }
}
