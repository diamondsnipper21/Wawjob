<?php namespace iJobDesk\Models;


class WalletHistory extends Model {
  	
	protected $table = 'wallet_history';

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

    public static function addHistory($user_id, $amount, $transaction_id = 0) {
		$walletHistory = new WalletHistory;
		$walletHistory->user_id = $user_id;
		$walletHistory->date = date('Y-m-d');
        $walletHistory->transaction_id = $transaction_id;
		$walletHistory->balance = $amount;
		$walletHistory->save();
    }

}