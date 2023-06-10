<?php
/**
 * NotifyPagSeguro Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\PagSeguro;

use PagSeguro\Configuration\Configure;
use PagSeguro\Services\Transactions\Notification;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\Account as EntityAccount;

class NotifyPagSeguro {

    public static function ReturnPagSeguro()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $filter_type = filter_input(INPUT_GET, $_POST['notificationType'], FILTER_SANITIZE_STRING);
            $filter_notifycode = filter_input(INPUT_GET, $_POST['notificationCode'], FILTER_SANITIZE_STRING);
            if($filter_type == 'transaction'){
                $credentials = Configure::getAccountCredentials();
                $transaction = Notification::check($credentials);

                $reference = $transaction->getReference();
                $transaction_code = $transaction->getCode();
                $transaction_status = $transaction->getStatus()->getTypeFromValue();
                
                if ($transaction_status == 'PAID') {
                    $dbPayment = EntityPayments::getPayment([ 'preference' => $reference])->fetchObject();
                    $dbAccount = EntityAccount::getAccount([ 'id' => $dbPayment->account_id])->fetchObject();
                    $finalcoins = $dbAccount->coins + $dbPayment->total_coins;

                    EntityPayments::updatePayment([ 'reference' => $reference], [
                        'status' => 4,
                    ]);
                    EntityAccount::updateAccount([ 'id' => $dbPayment->account_id], [
                        'coins' => $finalcoins,
                    ]);
                }
            }
        }
    }

}