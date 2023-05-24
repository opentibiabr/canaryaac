<?php
/**
 * ApiPayPal Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\PayPal;

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class ApiPayPal{

    public static function apiContext()
    {
        date_default_timezone_set(@date_default_timezone_get());

        $apiContext = new ApiContext(new OAuthTokenCredential($_ENV['PAYPAL_CLIENTID'], $_ENV['PAYPAL_SECRET']));
        $apiContext->setConfig(
            [
                'mode' => 'sandbox',
                'log.LogEnabled' => false,
                'log.FileName' => 'paypal.log',
                'log.LogLevel' => 'FINE',
                'cache.enabled' => false,
            ]
        );
        return $apiContext;
    }

    public static function createPayment($products = [], $email = null)
    {
        $gross_payment = $products['item']['amount'] * $products['item']['quantity'];

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item1 = new Item();
        $item1->setName($products['item']['title'])->setCurrency('BRL')->setQuantity($products['item']['quantity'])->setSku('23123465')->setPrice($products['item']['amount']);

        $itemList = new ItemList();
        $itemList->setItems([$item1]);

        $amount = new Amount();
        $amount->setCurrency('BRL')->setTotal($gross_payment);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription($products['item']['title'])->setInvoiceNumber($products['reference']);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(URL.'/payment/paypal/return')->setCancelUrl(URL.'/payment');

        $payment = new Payment();
        $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));

        $request = clone $payment;
        $payment->create(self::apiContext());
        $approvalUrl = $payment->getApprovalLink();

        return $approvalUrl;
    }
}