<?php
/**
 * Donates Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Account as EntityAccount;
use App\Utils\View;
use App\Model\Entity\PaymentStatus;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\Payments as PaymentsFunctions;

class Donates extends Base{

    public static function getPaymentByReference($request, $reference)
    {
        if (empty($reference)) {
            return self::viewDonates($request);
        }
        $filter_reference = filter_var($reference, FILTER_SANITIZE_SPECIAL_CHARS);
        $payment = EntityPayments::getPayment('reference = "'.$filter_reference.'"')->fetchObject();
        if (empty($payment)) {
            return self::viewDonates($request);
        }
        $arrayPayment = [
            'id' => $payment->id,
            'account_id' => $payment->account_id,
            'method' => $payment->method,
            'method_img' => PaymentsFunctions::convertMethodImage($payment->method),
            'reference' => $payment->reference,
            'total_coins' => $payment->total_coins,
            'gross_payment' => $payment->gross_payment,
            'status' => PaymentStatus::from($payment->status),
            'status_badge' => PaymentsFunctions::convertStatus($payment->status),
            'date' => date('d/m/Y H:i:s', $payment->date),
        ];
        $select_account = EntityAccount::getAccount('id = "'.$payment->account_id.'"')->fetchObject();
        $arrayAccount = [
            'email' => $select_account->email,
        ];
        $arrayReturn = [
            'donate' => $arrayPayment,
            'account' => $arrayAccount,
        ];
        return $arrayReturn;
    }

    public static function viewPaymentByReference($request, $reference)
    {
        $paymentByReference = self::getPaymentByReference($request, $reference);
        $content = View::render('admin/modules/donates/view', [
            'donate' => $paymentByReference['donate'],
            'account' => $paymentByReference['account'],
        ]);
        return parent::getPanel('Donates', $content, 'donates');
    }

    public static function updateDonates($request)
    {
        $postVars = $request->getPostVars();

        if (isset($postVars['edit_donate'])) {

            if (empty($postVars['coin_price'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_coin_price = filter_var($postVars['coin_price'], FILTER_SANITIZE_SPECIAL_CHARS);

            if(empty($postVars['checkboxDonates'])){
                $filter_Donates = 0;
            }else{
                $filter_Donates = 1;
            }
            if($filter_Donates > 1){
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }

            if(empty($postVars['checkboxMercadoPago'])){
                $filter_MercadoPago = 0;
            }else{
                $filter_MercadoPago = 1;
            }
            if($filter_MercadoPago > 1){
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            if(empty($postVars['checkboxPayPal'])){
                $filter_PayPal = 0;
            }else{
                $filter_PayPal = 1;
            }
            if($filter_PayPal > 1){
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            if(empty($postVars['checkboxPagSeguro'])){
                $filter_PagSeguro = 0;
            }else{
                $filter_PagSeguro = 1;
            }
            if($filter_PagSeguro > 1){
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }

            EntityServerConfig::updateInfoWebsite('id = "1"', [
                'donates' => $filter_Donates,
                'coin_price' => $filter_coin_price,
                'mercadopago' => $filter_MercadoPago,
                'paypal' => $filter_PayPal,
                'pagseguro' => $filter_PagSeguro
            ]);

            $status = SweetAlert::Types('Success!', 'Updated successfully.', 'success', 'btn btn-success');
            return self::viewDonates($request, $status);
        }

        if (isset($postVars['edit_products'])) {

            if (empty($postVars['product_1'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_product_1 = filter_var($postVars['product_1'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($postVars['product_2'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_product_2 = filter_var($postVars['product_2'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($postVars['product_3'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_product_3 = filter_var($postVars['product_3'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($postVars['product_4'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_product_4 = filter_var($postVars['product_4'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($postVars['product_5'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_product_5 = filter_var($postVars['product_5'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($postVars['product_6'])) {
                $status = SweetAlert::Types('Error!', 'Something went wrong.', 'error', 'btn btn-danger');
                return self::viewDonates($request, $status);
            }
            $filter_product_6 = filter_var($postVars['product_6'], FILTER_SANITIZE_SPECIAL_CHARS);

            EntityServerConfig::updateProducts('id = "1"', [
                'coins' => $filter_product_1
            ]);
            EntityServerConfig::updateProducts('id = "2"', [
                'coins' => $filter_product_2
            ]);
            EntityServerConfig::updateProducts('id = "3"', [
                'coins' => $filter_product_3
            ]);
            EntityServerConfig::updateProducts('id = "4"', [
                'coins' => $filter_product_4
            ]);
            EntityServerConfig::updateProducts('id = "5"', [
                'coins' => $filter_product_5
            ]);
            EntityServerConfig::updateProducts('id = "6"', [
                'coins' => $filter_product_6
            ]);
            $status = SweetAlert::Types('Success!', 'Updated successfully.', 'success', 'btn btn-success');
            return self::viewDonates($request, $status);

        }
    }

    public static function viewDonates($request, $status = null)
    {
        $dbServer = EntityServerConfig::getInfoWebsite()->fetchObject();
        $content = View::render('admin/modules/donates/index', [
            'status' => $status,
            'payments' => PaymentsFunctions::getPayments(),
            'stats' => PaymentsFunctions::statisticsPaytments(),
            'products' => PaymentsFunctions::getProducts(),
            'active_donates' => $dbServer->donates,
            'active_mercadopago' => $dbServer->mercadopago,
            'active_paypal' => $dbServer->paypal,
            'active_pagseguro' => $dbServer->pagseguro,
            'coin_price' => $dbServer->coin_price
        ]);
        return parent::getPanel('Donates', $content, 'donates');
    }

}