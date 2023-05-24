<?php
/**
 * NotifyMercadoPago Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\MercadoPago;

use App\Payment\Payments;
use MercadoPago\Payment;
use MercadoPago\SDK;
use App\Model\Entity\PaymentStatus;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\Account as EntityAccount;

class NotifyMercadoPago {

    public static function ReturnMercadoPago($request)
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            array('status_code' => 405, 'message' => "method not allowed");
        }

        $postVars = $request->getPostVars();
        
        if(array_key_exists('topic', $postVars) and $postVars['topic'] == 'merchant_order'){
            return array('status_code' => 200, 'message' => "ok");
        }

        if(!isset($postVars['type'])){
            return array('status_code' => 422, 'message' => "empty notification type");
        }

        SDK::setAccessToken($_ENV['MERCADOPAGO_TOKEN']);
        switch ($postVars["type"]) {
            case "payment":
                $payment = Payment::find_by_id($postVars['data']['id']);
                if(is_null($payment)) {
                    return array('status_code' => 404, 'message' => "payment not found");
                }
                self::updatePayment($payment);
                return array('status_code' => 200, 'message' => "ok");
        default:
                return array('status_code' => 418, 'message' => "unkown notification type");
        }
    }

    static function updatePayment($payment)
    {   
        switch ($payment->status) {
            case 'pending':
                Payments::setPaymentStatus($payment->external_reference, PaymentStatus::Pending);
                break;
            case 'in_process':
                Payments::setPaymentStatus($payment->external_reference, PaymentStatus::UnderAnalisys);
                break;
            case 'authorized':
                Payments::setPaymentStatus($payment->external_reference, PaymentStatus::Processing);
                break;
            case 'approved':
                Payments::ApprovePayment($payment->external_reference, $payment->transaction_details->net_received_amount);
                break;
            case 'rejected':
                Payments::setPaymentStatus($payment->external_reference, PaymentStatus::Rejected);
                break;
            case 'cancelled':
                Payments::setPaymentStatus($payment->external_reference, PaymentStatus::Canceled);
                break;
            case 'refunded':
                Payments::RefundPayment($payment->external_reference);
                break;
            case 'charged_back':
                Payments::RefundPayment($payment->external_reference);
                break;   
            default:
                Payments::setPaymentStatus($payment->external_reference, PaymentStatus::Unknown);
                break;
        }
    }

}