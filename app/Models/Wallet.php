<?php namespace iJobDesk\Models;


class Wallet extends Model {

	protected $table = 'wallets';

	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    const MIN_HOURLY_LIMIT = 0;

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    function __construct() {
		parent::__construct();
	}

	/**
	* Get the user.
	*
	* @return mixed
	*/
	public function user()
	{
		return $this->belongsTo('iJobDesk\Models\User', 'user_id');
	}

	public static function account($user_id) {
		$account = self::where('user_id', $user_id)->first();

		if ( !$account ) {
			$account = new Wallet;
			$account->user_id = $user_id;
			$account->save();
		}

		return $account;
	}

	public static function updateAmount($user_id, $amount) {
		$wallet = self::where('user_id', $user_id)->first();

		if ( !$wallet ) {
			$wallet = new Wallet;
			$wallet->user_id = $user_id;
		}

		$wallet->amount += $amount;
		$wallet->save();
    }

    public static function totalWallet() {
    	return self::where('amount', '>', 0)
    				->sum('amount');
    }

}