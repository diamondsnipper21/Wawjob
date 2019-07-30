<?php namespace iJobDesk\Models;


class Country extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'countries';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = false;

	function __construct() {
        parent::__construct();
    }

	public static function getCountryByCode($code) {
		$country = self::where('charcode', $code)->first();

		if ( $country ) {
			return $country;
		}

		return false;
	}

	public static function getCountryNameByCode($code) {
		$country = self::getCountryByCode($code);

		if ( $country ) {
			return $country->name;
		}

		return '';
	}

    public static function getOptions() {
		return [
			1 => 'Enabled',
			0 => 'Disabled',
		];
	}

    public static function enableStatusChanged($c) {
        $attributes = '';

		if ($c->paypal_enabled == 1) {
			$attributes .= ' data-status-DISABLE_PAYPAL=true';
		} else {
			$attributes .= ' data-status-ENABLE_PAYPAL=true';
		}

		if ($c->payoneer_enabled == 1) {
			$attributes .= ' data-status-DISABLE_PAYONEER=true';
		} else {
			$attributes .= ' data-status-ENABLE_PAYONEER=true';
		}

		if ($c->skrill_enabled == 1) {
			$attributes .= ' data-status-DISABLE_SKRILL=true';
		} else {
			$attributes .= ' data-status-ENABLE_SKRILL=true';
		}

		if ($c->wechat_enabled == 1) {
			$attributes .= ' data-status-DISABLE_WECHAT=true';
		} else {
			$attributes .= ' data-status-ENABLE_WECHAT=true';
		}

		if ($c->bank_enabled == 1) {
			$attributes .= ' data-status-DISABLE_BANK=true';
		} else {
			$attributes .= ' data-status-ENABLE_BANK=true';
		}

        return $attributes;
    }
}