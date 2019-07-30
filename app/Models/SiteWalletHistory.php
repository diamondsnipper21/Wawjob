<?php namespace iJobDesk\Models;


class SiteWalletHistory extends Model {
  	
	protected $table = 'site_wallet_history';

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

    public function transaction() {
        return $this->hasOne('iJobDesk\Models\TransactionLocal', 'id', 'transaction_id');
    }

    public static function addHistory($type = 1, $amount, $transaction_id = 0) {
		$walletHistory = new SiteWalletHistory;
		$walletHistory->type = $type;
		$walletHistory->date = date('Y-m-d');
        $walletHistory->transaction_id = $transaction_id;
		$walletHistory->balance = $amount;
		$walletHistory->save();
    }

}