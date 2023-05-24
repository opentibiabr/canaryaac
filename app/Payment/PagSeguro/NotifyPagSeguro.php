<?php
/**
 * NotifyPagSeguro Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\PagSeguro;

use App\Payment\Payments;
use App\Model\Entity\PaymentStatus;
use PagSeguro\Configuration\Configure;
use PagSeguro\Services\Transactions\Notification;

class NotifyPagSeguro {

    public static function ReturnPagSeguro($request)
    {   
        ApiPagSeguro::initialize();
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            array('status_code' => 405, 'message' => "method not allowed");
        }

        $postVars = $request->getPostVars();
        if(!isset($postVars['notificationType'])){
            return array('status_code' => 422, 'message' => "empty notification type");
        }

        if($postVars['notificationType'] != 'transaction'){
            return array('status_code' => 500, 'message' => "non transaction notification type ".file_get_contents('php://input'));
        }
        $credentials = Configure::getAccountCredentials();
        $transaction = Notification::check($credentials);
        
        $reference = $transaction->getReference();
        $transaction_status = $transaction->getStatus();
        self::updatePayment($transaction_status, $reference);

        return array('status_code' => 200, 'message' => "ok");
    }

    static function updatePayment($transaction_status, $reference)
    {   
        switch ($transaction_status) {
            case '1': // Pending Payment
                Payments::setPaymentStatus($reference, PaymentStatus::Pending);
                break;
            case '2': // Under Analisys
                Payments::setPaymentStatus($reference, PaymentStatus::UnderAnalisys);
                break;
            case '3': // Paid
            Payments::ApprovePayment($reference, 0);
                break;
            case '7': // Canceled or Rejected
                Payments::setPaymentStatus($reference, PaymentStatus::Canceled);
                break;
            case '6': // Refunded
                Payments::RefundPayment($reference);
                break;
            case '8': // Charged Back (Debitado)
                Payments::RefundPayment($reference);
                break;
            default:
            Payments::setPaymentStatus($reference, PaymentStatus::Unknown);

                break;
        }
    }
}