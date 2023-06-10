<?php
/**
 * NotifyMercadoPago Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\MercadoPago;

use MercadoPago\Payment;
use MercadoPago\SDK;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\Account as EntityAccount;

class NotifyMercadoPago {

    public static function ReturnMercadoPago()
    {
        if(isset($_POST['type'])){
            SDK::setAccessToken($_ENV['MERCADOPAGO_TOKEN']);
            if($_POST['type'] == 'payment'){
                $payment = Payment::find_by_id($_POST['data']['id']);
                self::updatePayment($payment);
            }else{
                $payment = null;
            }
        }
    }

    public static function updatePayment($payment)
    {
        if($payment->order_status == 'paid'){

            if($payment->cancelled == 'false'){

                $dbPayment = EntityPayments::getPayment([ 'preference' => $payment->preference_id])->fetchObject();
                $dbAccount = EntityAccount::getAccount([ 'id' => $dbPayment->account_id])->fetchObject();
                $finalcoins = $dbAccount->coins + $dbPayment->total_coins;

                EntityPayments::updatePayment([ 'reference' => $payment->preference_id], [
                    'status' => 4,
                ]);
                EntityAccount::updateAccount([ 'id' => $dbPayment->account_id], [
                    'coins' => $finalcoins,
                ]);

            }

        }
    }

}