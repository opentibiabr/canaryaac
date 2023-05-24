<?php
/**
 * Payment Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\Account as EntityAccount;
use App\Payment\PagSeguro\ApiPagSeguro;
use App\Payment\MercadoPago\ApiMercadoPago;
use App\Payment\PayPal\ApiPayPal;
use \App\Utils\View;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class Payment extends Base{
    
    public static function viewPayment()
    {
        $idLogged = SessionAdminLogin::idLogged();
        $dbAccount = EntityAccount::getAccount('id = "'.$idLogged.'"')->fetchObject();
        $donateConfigs = EntityServerConfig::getInfoWebsite('id = "1"')->fetchObject();

        $select_products = EntityServerConfig::getProducts(null, 'id ASC');
        $product_web_id = 192;
        while ($product = $select_products->fetchObject()) {
            $product_web_id++;
            $gross_payment = $donateConfigs->coin_price * $product->coins;
            $arrayProducts[] = [
                'id' => $product->id,
                'coins' => $product->coins,
                'web_id' => $product_web_id,
                'gross_payment' => $gross_payment
            ];
        }

        $content = View::render('pages/shop/payment', [
            'email' => $dbAccount->email ?? null,
            'products' => $arrayProducts,
            'active_mercadopago' => $donateConfigs->mercadopago,
            'active_pagseguro' => $donateConfigs->pagseguro,
            'active_paypal' => $donateConfigs->paypal,
        ]);
        return parent::getBase('Webshop', $content, 'donate');
    }

    public static function viewPaymentData($request)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $dbAccount = EntityAccount::getAccount('id = "'.$idLogged.'"')->fetchObject();
        $postVars = $request->getPostVars();

        if(!isset($postVars['payment_country'])){
            $request->getRouter()->redirect('/payment');
        }
        if(!isset($postVars['payment_method'])){
            $request->getRouter()->redirect('/payment');
        }
        if(!isset($postVars['payment_coins'])){
            $request->getRouter()->redirect('/payment');
        }

        $content = View::render('pages/shop/paymentdata', [
            'country' => $postVars['payment_country'],
            'coins' => $postVars['payment_coins'],
            'method' => $postVars['payment_method'],
            'email' => $dbAccount->email ?? null,
        ]);
        return parent::getBase('Webshop', $content, 'donate');
    }

    public static function viewPaymentConfirm($request)
    {
        $donateConfigs = EntityServerConfig::getInfoWebsite('id = "1"')->fetchObject();
        $postVars = $request->getPostVars();

        if(!isset($postVars['payment_email'])){
            $request->getRouter()->redirect('/payment');
        }
        if(!filter_var($postVars['payment_email'], FILTER_VALIDATE_EMAIL)){
            $request->getRouter()->redirect('/payment');
        }

        $filter_coins = filter_var($postVars['payment_coins'], FILTER_SANITIZE_NUMBER_INT);
        $gross_payment = $filter_coins * $donateConfigs->coin_price;
        
        $content = View::render('pages/shop/paymentconfirm', [
            'method' => $postVars['payment_method'],
            'coins' => $filter_coins,
            'country' => $postVars['payment_country'],
            'email' => $postVars['payment_email'],
            'price' => $gross_payment,
        ]);
        return parent::getBase('Webshop', $content, 'donate');
    }

    public static function viewPaymentSummary($request)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $dbAccount = EntityAccount::getAccount('id = "'.$idLogged.'"')->fetchObject();
        $donateConfigs = EntityServerConfig::getInfoWebsite('id = "1"')->fetchObject();
        $postVars = $request->getPostVars();

        if($postVars['TermsOfService'] != 1){
            $request->getRouter()->redirect('/payment');
        }
        if(!isset($postVars['payment_coins'])){
            $request->getRouter()->redirect('/payment');
        }
        if(!isset($postVars['payment_method'])){
            $request->getRouter()->redirect('/payment');
        }
        if(!isset($postVars['payment_country'])){
            $request->getRouter()->redirect('/payment');
        }
        if(!isset($postVars['payment_email'])){
            $request->getRouter()->redirect('/payment');
        }

        if(!filter_var($postVars['payment_email'], FILTER_VALIDATE_EMAIL)){
            $request->getRouter()->redirect('/payment');
        }
        $filter_email = filter_var($postVars['payment_email'], FILTER_SANITIZE_EMAIL);

        if(!filter_var($postVars['payment_coins'], FILTER_VALIDATE_INT)){
            $request->getRouter()->redirect('/payment');
        }
        $filter_coins = filter_var($postVars['payment_coins'], FILTER_SANITIZE_NUMBER_INT);
        $gross_payment = $donateConfigs->coin_price;
        if($gross_payment == 0){
            $request->getRouter()->redirect('/payment');
        }
        $price = $gross_payment * $filter_coins;
        
        $payment_method = filter_var($postVars['payment_method'], FILTER_SANITIZE_SPECIAL_CHARS);
        $code_payment = null;
        switch($payment_method)
        {
            case 'paypal':
                if($donateConfigs->paypal == 0){
                    $request->getRouter()->redirect('/payment');
                }
                $reference = uniqid();
                $checkout = [
                    'reference' => $reference,
                    'item' => [
                        'id' => '0001',
                        'title' => $filter_coins.' Coins',
                        'amount' => $gross_payment,
                        'quantity' => $filter_coins,
                    ],
                ];
                $code_payment = ApiPayPal::createPayment($checkout, $filter_email);
                break;
            case 'pagseguro':
                if($donateConfigs->pagseguro <= 0){
                    $request->getRouter()->redirect('/payment');
                }
                $reference = uniqid();
                $checkout = [
                    'reference' => $reference,
                    'item' => [
                        'id' => '0001',
                        'title' => $filter_coins.' Coins',
                        'amount' => $gross_payment,
                        'quantity' => $filter_coins,
                    ],
                ];
                $code_payment = 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=' . ApiPagSeguro::createPayment($checkout, $filter_email);
                break;
            case 'pix':
                // Not implemented Yet
                $request->getRouter()->redirect('/payment');
                break;
            case 'mercadopago':
                if($donateConfigs->mercadopago <= 0){
                    $request->getRouter()->redirect('/payment');
                }
                $reference = uniqid();
                $checkout = [
                    'reference' => $reference,
                    'item' => [
                        'id' => '0001',
                        'title' => $filter_coins.' Coins',
                        'amount' => $gross_payment,
                        'quantity' => $filter_coins,
                    ],
                ];
                $code_payment = ApiMercadoPago::createPayment($reference, $checkout, $filter_email, $_ENV['MERCADOPAGO_PAYMENT']);
                break;
            default:
                $request->getRouter()->redirect('/payment');
                break;
        }

        $order = [
            'account_id' => $idLogged,
            'method' => $payment_method,
            'reference' => $reference,
            'total_coins' => $filter_coins,
            'gross_payment' => $price,
            // TODO: Status should be set to \App\Model\Entity\PaymentStatus::Pending->Value, for some reason i don´t understand I get an error when I do this
            'status' => 0,
            'date' => strtotime(date('Y-m-d H:i:s')),
        ];
        EntityPayments::insertPayment($order);

        $content = View::render('pages/shop/paymentsummary', [
            'email' => $filter_email,
            'method' => $payment_method,
            'code_payment' => $code_payment ?? null,
        ]);
        return parent::getBase('Webshop', $content, 'donate');
    }

}