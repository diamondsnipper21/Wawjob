<?php namespace iJobDesk\Models;

use iJobDesk\Models\Contract;

class NotifyInsufficientFund extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'notify_insufficient_fund';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    function __construct() {
        parent::__construct();
    }

    public static function addNew($params = []) {
        $newNotify = new NotifyInsufficientFund;
        
        $newNotify->contract_id = $params['contract_id'];
        $newNotify->user_id = $params['user_id'];

        $newNotify->save();        
    }

    public static function updateClient($user_id = 0) {
    	self::where('user_id', $user_id)->delete();

    	$freelancer_ids = Contract::where('buyer_id', $user_id)
    						->whereIn('status', [
	                            Contract::STATUS_OPEN,
	                            Contract::STATUS_PAUSED,
	                            Contract::STATUS_SUSPENDED,
	                        ])
	                        ->pluck('contractor_id')
							->toArray();

	    if ( $freelancer_ids ) {
	    	self::whereIn('user_id', $freelancer_ids)->delete();
	    }
    }
}