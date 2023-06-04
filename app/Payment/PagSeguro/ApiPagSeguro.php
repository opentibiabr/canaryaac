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
use PagSeguro\Services\Application\Notification;
use PagSeguro\Services\Transactions\Search\Code;
use PagSeguro\Services\Transactions\Search\Reference;
use PagSeguro\Services\Transactions\Search\Abandoned;
use PagSeguro\Helpers\Xhr;

class ApiPagSeguro{

    public static function createPayment($products = [], $email = null)
    {
        $email = $_ENV['PAGSEGURO_EMAIL'];
        $token = $_ENV['PAGSEGURO_TOKEN'];

        Library::initialize();
        Library::cmsVersion()->setName(SITE_NAME)->setRelease("1.0.0");
        Library::moduleVersion()->setName(SITE_NAME)->setRelease("1.0.0");
        
        Configure::setEnvironment('sandbox');
        Configure::setAccountCredentials($email, $token);

        Configure::setCharset('UTF-8');
        Configure::setLog(false, '');

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
        $payment->setSender()->setName(SITE_NAME);
        $payment->setSender()->setEmail($email);

        $payment->setRedirectUrl(URL.'/payment');
        $payment->setNotificationUrl(URL.'/payment/pagseguro/return');

        $result = $payment->register(
            Configure::getAccountCredentials()
        );

        return $result;
    }

    public static function createPaymentLightBox($products = [], $email = null)
    {
        $email = $_ENV['PAGSEGURO_EMAIL'];
        $token = $_ENV['PAGSEGURO_TOKEN'];

        Library::initialize();
        Library::cmsVersion()->setName(SITE_NAME)->setRelease("1.0.0");
        Library::moduleVersion()->setName(SITE_NAME)->setRelease("1.0.0");

        Configure::setEnvironment('sandbox');
        Configure::setAccountCredentials($email, $token);

        Configure::setCharset('UTF-8');
        Configure::setLog(false, '');

        $payment = new Payment();

        $payment->addItems()->withParameters(
            $products['item']['id'],
            $products['item']['title'],
            $products['item']['quantity'],
            $products['item']['amount'],
        );
        $payment->setCurrency('BRL');
        $payment->setReference($products['reference']);

        $payment->setSender()->setName(SITE_NAME);
        $payment->setSender()->setEmail($email);

        $payment->setRedirectUrl(URL.'/payment');
        $payment->setNotificationUrl(URL.'/payment/pagseguro/return');

        $onlyCheckoutCode = true;
        $result = $payment->register(Configure::getAccountCredentials(), $onlyCheckoutCode);

        return $result->getCode();
    }

    public static function notifyCheckPayment($order_code = null)
    {
        Library::initialize();
        Library::cmsVersion()->setName(SITE_NAME)->setRelease("1.0.0");
        Library::moduleVersion()->setName(SITE_NAME)->setRelease("1.0.0");
        
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