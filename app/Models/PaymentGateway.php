<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends Model {

	use SoftDeletes;

	/**
 	* The table associated with the model.
 	*
 	* @var string
 	*/
 	protected $table = 'payment_gateways';

	/**
	* The attributes that should be mutated to dates.
	*
	* @var array
	*/
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    const GATEWAY_PAYPAL = 1;
    const GATEWAY_CREDITCARD = 2;
    const GATEWAY_WEIXIN = 3;
    const GATEWAY_WIRETRANSFER = 4;
    const GATEWAY_SKRILL = 5;
    const GATEWAY_PAYONEER = 6;

	/**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

	function __construct() {
        parent::__construct();
    }

    public function isPayPal() {
        return $this->type == self::GATEWAY_PAYPAL;
    }

    public function isCreditCard() {
        return $this->type == self::GATEWAY_CREDITCARD;
    }

    public function isWeixin() {
        return $this->type == self::GATEWAY_WEIXIN;
    }

    public function isWireTransfer() {
        return $this->type == self::GATEWAY_WIRETRANSFER;
    }

    public function isSkrill() {
        return $this->type == self::GATEWAY_SKRILL;
    }

    public function isPayoneer() {
        return $this->type == self::GATEWAY_PAYONEER;
    }

    public function enabledWithdrawal() {
        return $this->enable_withdraw == 1;
    }

    public function enabledDeposit() {
        return $this->enable_deposit == 1;
    }

    public static function getByType($type) {
        return self::where('type', $type)->first();
    }

    public static function getNameByType($type) {
        $gateway = self::getByType($type);

        if ( $gateway ) {
        	return parse_json_multilang($gateway->name);
        }

        return '';
    }

    public static function getAllGateways() {
        return self::orderBy('sort')->get();
    }

    public static function getActiveGateways() {
        return self::where('is_active', 1)->orderBy('sort')->get();
    }

    public static function getOptions() {
		return [
			1 => 'Enabled',
			0 => 'Disabled',
		];
	}

    public static function enableStatusChanged($payment_gateway) {
        $attributes = '';

        if ($payment_gateway->is_active == 1) {
            $attributes .= ' data-status-0=true';
        } else {
            $attributes .= ' data-status-1=true';
        }

        return $attributes;
    }

    public static function enableWithdrawalStatusChanged($payment_gateway) {
        $attributes = '';

        if ($payment_gateway->enable_withdraw == 1) {
            $attributes .= ' data-status-2=true';
        } else {
            $attributes .= ' data-status-3=true';
        }

        if ($payment_gateway->enable_deposit == 1) {
            $attributes .= ' data-status-4=true';
        } else {
            $attributes .= ' data-status-5=true';
        }

        return $attributes;
    }
}