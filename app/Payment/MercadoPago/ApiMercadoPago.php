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

class ApiMercadoPago
{
    public static function createPayment($products = [], $email = null)
    {
        $final_price = $products['item']['amount'] * $products['item']['quantity'];
        $mercadopago_status = 'sandbox';

        if($mercadopago_status == 'sandbox') {
            SDK::setAccessToken($_ENV['MERCADOPAGO_TOKEN']);
            SDK::setPublicKey($_ENV['MERCADOPAGO_TOKEN']);
        }
        if($mercadopago_status == 'production') {
            SDK::setClientId($_ENV['MERCADOPAGO_CLIENTID']);
            SDK::setClientSecret($_ENV['MERCADOPAGO_SECRET']);
        }

        $preference = new Preference();
        $item = new Item();
        $item->title = $products['item']['title'];
        $item->description = $products['item']['title'];
        $item->quantity = $products['item']['quantity'];
        $item->currency_id = "BRL";
        $item->unit_price = $products['item']['amount'];

        $preference->items = array($item);
        $preference->save();

        $response = array(
            'status' => $preference->status,
            'status_detail' => $preference->status_detail,
            'id' => $preference->id
        );

        return $response;
    }

    public static function createPaymentSandbox($products = [], $email = null)
    {
        $final_price = $products['item']['amount'] * $products['item']['quantity'];
        $mercadopago_status = 'production';

        if($mercadopago_status == 'sandbox') {
            SDK::setAccessToken($_ENV['MERCADOPAGO_TOKEN']);
            SDK::setPublicKey($_ENV['MERCADOPAGO_TOKEN']);
        }
        if($mercadopago_status == 'production') {
            SDK::setClientId($_ENV['MERCADOPAGO_CLIENTID']);
            SDK::setClientSecret($_ENV['MERCADOPAGO_SECRET']);
        }

        $preference = new Preference();
        $item = new Item();
        $item->title = $products['item']['title'];
        $item->description = $products['item']['title'];
        $item->quantity = $products['item']['quantity'];
        $item->currency_id = "BRL";
        $item->unit_price = $products['item']['amount'];

        $preference->items = array($item);
        $preference->save();

        return $preference->sandbox_init_point;
    }

    public function testFindPreferenceById($preference_id)
    {
        $preference = Preference::find_by_id($preference_id);
        return $preference->id;
    }
}
