<?php
/**
 * ApiPagSeguro Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\PagSeguro;

use PagSeguro\Configuration\Configure;
use PagSeguro\Library;
use PagSeguro\Domains\Requests\Payment;
use PagSeguro\Enum\PaymentMethod\Group;
use PagSeguro\Enum\PaymentMethod\Config\Keys;
use PagSeguro\Services\Application\Notification;
use PagSeguro\Services\Transactions\Search\Code;
use PagSeguro\Services\Transactions\Search\Reference;
use PagSeguro\Services\Transactions\Search\Abandoned;
use PagSeguro\Helpers\Xhr;

class ApiPagSeguro{
    public static function initialize() {
        Library::initialize();
        Library::cmsVersion()->setName("CanaryAAC")->setRelease("1.0.0");
        Library::moduleVersion()->setName("CanaryAAC")->setRelease("1.0.0");
    
        $email = $_ENV['PAGSEGURO_EMAIL'];
        $token = $_ENV['PAGSEGURO_TOKEN'];
    
        Configure::setEnvironment('sandbox');
        Configure::setAccountCredentials($email, $token);
    
        Configure::setCharset('UTF-8');
        Configure::setLog(false, '');
    }


    public static function createPayment($products = [], $email = null)
    {
        self::initialize();
        $payment = new Payment();

        $payment->addItems()->withParameters(
            $products['item']['id'],
            $products['item']['title'],
            $products['item']['quantity'],
            $products['item']['amount'],
        );

        $payment->setCurrency("BRL");
        $payment->setReference($products['reference']);

        // Set your customer information.
        $payment->setSender()->setName('Canary AAC');
        $payment->setSender()->setEmail($email);

        $payment->setRedirectUrl(URL.'/payment');
        $payment->setNotificationUrl(URL.'/payment/pagseguro/return');

        // Limit max installments 
        // WARN: Not working on legacy pagseguro-php-sdk
        // $payment->addPaymentMethod()->withParameters(
        //     Group::CREDIT_CARD,
        //     Keys::MAX_INSTALLMENTS_LIMIT,
        //     1 // (int) qty of installment
        // );

        $payment->acceptPaymentMethod()->groups(
            Group::CREDIT_CARD,
            Group::BALANCE,
            Group::BOLETO
        );

        $result = $payment->register(
            Configure::getAccountCredentials()
        );

        $onlyCheckoutCode = true;
        $result = $payment->register(Configure::getAccountCredentials(), $onlyCheckoutCode);

        return $result->getCode();
    }

    public static function createPaymentLightBox($products = [], $email = null)
    {
        self::initialize();
        $payment = new Payment();

        $payment->addItems()->withParameters(
            $products['item']['id'],
            $products['item']['title'],
            $products['item']['quantity'],
            $products['item']['amount'],
        );
        $payment->setCurrency('BRL');
        $payment->setReference($products['reference']);

        $payment->setSender()->setName('Canary AAC');
        $payment->setSender()->setEmail($email);

        $payment->setRedirectUrl(URL.'/payment');
        $payment->setNotificationUrl(URL.'/payment/pagseguro/return');

        $payment->acceptPaymentMethod()->groups(
            Group::CREDIT_CARD,
            Group::BALANCE,
            Group::PAYMENT_SLIP,
        );

        $onlyCheckoutCode = true;
        $result = $payment->register(Configure::getAccountCredentials(), $onlyCheckoutCode);

        return $result->getCode();
    }

    public static function notifyCheckPayment($order_code = null)
    {
        Library::initialize();
        Library::cmsVersion()->setName("CanaryAAC")->setRelease("1.0.0");
        Library::moduleVersion()->setName("CanaryAAC")->setRelease("1.0.0");
        
        if (Xhr::hasPost()) {
            $response = Notification::check(Configure::getApplicationCredentials());
        }
        return $response ?? null;
    }

    public static function verifyPaymentByCode($order_code = null)
    {
        Library::initialize();
        $code = $order_code;
        $response = Code::search(Configure::getAccountCredentials(), $code);
        return $response;
    }

    public static function verifyPaymentByReference($options = null, $reference = null)
    {
        Library::initialize();

        $options = [
            'initial_date' => '2022-04-01T14:55',
            'final_date' => '2022-04-24T09:55', //Optional
            'page' => 1, //Optional
            'max_per_page' => 20, //Optional
        ];
        $reference = "LIBPHP000001";

        $response = Reference::search(Configure::getAccountCredentials(), $reference, $options);
        return $response;
    }

    public static function verifyPaymentAbandoned()
    {
        Library::initialize();

        $options = [
            'initial_date' => '2022-04-01T14:55',
            'final_date' => '2022-04-24T09:55', //Optional
            'page' => 1, //Optional
            'max_per_page' => 20, //Optional
        ];
        $response = Abandoned::search(Configure::getAccountCredentials(), $options);
        return $response;
    }

}