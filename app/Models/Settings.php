<?php namespace iJobDesk\Models;

class Settings extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'settings';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

	function __construct() {
        parent::__construct();
    }

	public static function get($key) {
		$setting = self::where('key', $key)->first();

		if ( !$setting ) {
			return false;
		}

		return $setting->value;
	}

	public static function updateSetting($key, $value) {
		$setting = self::where('key', $key)->first();

		if ( !$setting ) {
			return false;
		}

		$setting->value = $value;

		return $setting->save();
	}

	public static function getRate($is_affiliated = false) {
		if ( $is_affiliated ) {
			$fee_rate = self::get('FEE_RATE_AFFILIATED');
		} else {
			$fee_rate = self::get('FEE_RATE');
		}

		if ( !$fee_rate ) {
			return 0;
		}

		return round((100 - floatval($fee_rate)) / 100, 2);
	}

	public static function getFee($amount = 0, $is_affiliated = false) {
		return round(($amount * (1 - self::getRate($is_affiliated)) * 100) / 100, 2);
	}

	public static function getRateAmount($amount = 0, $is_affiliated = false) {
		return round($amount - self::getFee($amount, $is_affiliated), 2);
	}

	public static function getFeaturedJobFee() {
		$fee = self::get('FEATURED_JOB_FEE');

		return $fee ? doubleval($fee) : 0;
	}

	public static function getWithdrawFee($gateway = 0) {
		$fee = self::get('WITHDRAW_FEE');

		if ( $gateway == 4 ) {
			$fee = self::get('WITHDRAW_BANK_FEE');			
		}

		return $fee ? doubleval($fee) : 0;
	}

	public static function getAffiliateBuyerFee() {
		$fee_rate = self::get('AFFILIATE_BUYER_FEE');

		if ( !$fee_rate ) {
			return 0;
		}

		return round(floatval($fee_rate) / 100, 2);
	}

	public static function getAffiliateChildBuyerFee() {
		$fee_rate = self::get('AFFILIATE_CHILD_BUYER_FEE');

		if ( !$fee_rate ) {
			return 0;
		}

		return round(floatval($fee_rate) / 100, 2);
	}

	public static function getAffiliateFreelancerFeeRate() {
		$fee_rate = self::get('AFFILIATE_FREELANCER_FEE_RATE');

		if ( !$fee_rate ) {
			return 0;
		}

		return round(floatval($fee_rate) / 100, 2);
	}

	public static function getAffiliateChildFreelancerFeeRate() {
		$fee_rate = self::get('AFFILIATE_CHILD_FREELANCER_FEE_RATE');

		if ( !$fee_rate ) {
			return 0;
		}

		return round(floatval($fee_rate) / 100, 2);
	}
}