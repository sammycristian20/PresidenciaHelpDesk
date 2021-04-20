<?php

namespace App\Bill;

use Omnipay\Omnipay;
use App\Bill\Models\PaymentGateway;

/**
 * Class PayPal
 * @package App
 */
class OmnipayHelper
{
    /**
     * @return mixed
     */
    public function gateway(String $gateway)
    {
        $p = PaymentGateway::where('gateway_name', $gateway)->pluck('value', 'key')->toArray();
        $gateway = Omnipay::create($gateway);
        foreach ($p as $key => $value) {
            $method = "set".$key;
            $gateway->$method($value);
        }

        return $gateway;
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function purchase(String $gateway, array $parameters)
    {
        $response = $this->gateway($gateway)
            ->purchase($parameters)
            ->send();

        return $response;
    }

    /**
     * @param array $parameters
     */
    public function complete(String $gateway, array $parameters)
    {
        $response = $this->gateway($gateway)
            ->completePurchase($parameters)
            ->send();

        return $response;
    }

    /**
     * @param $amount
     */
    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * @param $order
     */
    public function getCancelUrl($gateway, $invoice)
    {
        return route('checkout.cancelled', [$gateway, $invoice]);
    }

    /**
     * @param $order
     */
    public function getReturnUrl($gateway, $invoice)
    {
        return route('checkout.completed', [$gateway, $invoice]);
    }

    /**
     * @param $order
     */
    public function getNotifyUrl($order)
    {
        $env = config('credentials.sandbox') ? "sandbox" : "live";
        //route('webhook.paypal.ipn', [$order->id, $env]);
        return '';
    }
}
