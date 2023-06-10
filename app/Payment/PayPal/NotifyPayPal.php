<?php
/**
 * NotifyPayPal Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\PayPal;

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\Account as EntityAccount;

class NotifyPayPal {

    public static function ReturnPayPal()
    {
        $payerId = filter_input(INPUT_GET, 'PayerID', FILTER_SANITIZE_STRING);
        $paymentId = filter_input(INPUT_GET, 'paymentId', FILTER_SANITIZE_STRING);

        $payment = Payment::get($paymentId, ApiPayPal::apiContext());

        $execution = new PaymentExecution();
        $response = $payment->execute($execution, ApiPayPal::apiContext());
        $arrayResponse = $response->toArray();

        if($arrayResponse['status'] == 'PAID'){

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
        return $arrayResponse;
    }
}