<?php namespace iJobDesk\Models;


class SiteWallet extends Model {

	protected $table = 'site_wallets';

    const TYPE_HOLDING = 1;
    const TYPE_EARNING = 2;

	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    function __construct() {
		parent::__construct();
	}

	public static function holding() {
		$holding = self::where('type', self::TYPE_HOLDING)->first();

		if ( !$holding ) {
			$holding = new SiteWallet;
			$holding->type = self::TYPE_HOLDING;
			$holding->save();
		}

		return $holding;
	}

	public static function earning() {
		$earning = self::where('type', self::TYPE_EARNING)->first();

		if ( !$earning ) {
			$earning = new SiteWallet;
			$earning->type = self::TYPE_EARNING;
			$earning->save();
		}

		return $earning;
	}

	public static function updateAmount($type, $amount) {
		$wallet = self::where('type', $type)->first();

		if ( !$wallet ) {
			$wallet = new SiteWallet;
			$wallet->type = $type;
		}

		$wallet->amount += $amount;
		$wallet->save();
    }

}