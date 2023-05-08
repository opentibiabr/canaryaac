<?php
/**
 * ApiMercadoPago Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Payment\MercadoPago;

use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;

class ApiMercadoPago {

    public static function createPayment($reference_id, $products = [], $email = null, $type='sandbox')
    {
        if($type == 'sandbox'){
            SDK::setAccessToken($_ENV['MERCADOPAGO_TOKEN']);
            SDK::setPublicKey($_ENV['MERCADOPAGO_KEY']);
        }
        else if($type == 'production'){
            SDK::setClientId($_ENV['MERCADOPAGO_CLIENTID']);
            SDK::setClientSecret($_ENV['MERCADOPAGO_SECRET']);
        }
        else {
            return;
        }

        $preference = new Preference();
        $item = new Item();
        $item->title = $products['item']['title'];
        $item->description = $products['item']['title'];
        $item->quantity = $products['item']['quantity'];
        $item->currency_id = "BRL";
        $item->unit_price = (double)$products['item']['amount']; 

        $preference->items = array($item);

        $preference->notification_url = URL. "/payment/mercadopago/return";
        $preference->external_reference = $reference_id;

        $preference->payment_methods = array(
        "excluded_payment_types" => array (
            array("id" => "ticket"),
        ),
        "installments" => intval($_ENV['DONATE_MAX_INSTALLMENT'])
        );

        $preference->save();
        
        if($type == 'sandbox'){
            return $preference->sandbox_init_point;
        }
        else {
            return $preference->init_point;
        }
    }
}