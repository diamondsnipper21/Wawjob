<?php namespace iJobDesk\Models;

use iJobDesk\Models\User;

/**
* @author Ro Un Nam
*/
class UserDeposit extends Model {

    protected $table = 'user_deposits';

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

    public static function getAmount($user_id, $gateway_type, $real_id) {
    	$existed = UserDeposit::where('user_id', $user_id)
								->where('gateway', $gateway_type)
								->where('real_id', $real_id)
								->first();

		if ( $existed ) {
			return $existed->amount;
		}

		return 0;
    }

    public static function updateAmount($user_id, $gateway_type, $real_id, $amount) {
    	$existed = UserDeposit::where('user_id', $user_id)
								->where('gateway', $gateway_type)
								->where('real_id', $real_id)
								->first();

		if ( $existed ) {
			$existed->amount = $existed->amount + $amount;
			$result = $existed->save();
		} else {
			$userDeposit = new UserDeposit;
			$userDeposit->user_id = $user_id;
			$userDeposit->gateway = $gateway_type;
			$userDeposit->real_id = $real_id;
			$userDeposit->amount = $amount;

			$result = $userDeposit->save();
		}

		return $result;
    }
}