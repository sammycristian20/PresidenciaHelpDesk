<?php
namespace App\Bill\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
	protected $table = 'billing_payment_gateways';
    protected $fillable = [
    	/**
         * Name of payment gateway
         */
        'name',

        /**
    	 * Name of sub gateway eg:
         * PayPal_REST in case of PayPal
    	 */
    	'gateway_name',
    	
    	/**
    	 * keys used for setting up gateway instance
    	 * eg. clientId
    	 */
    	'key',
    	
    	/**
    	 * stores value of keys
    	 * eg: value of clientId for gateway API
    	 */
    	'value',
    	
    	/**
    	 * status of payment gateway
    	 */
    	'status',

        /**
         *
         */
        'is_default'
    ];
}