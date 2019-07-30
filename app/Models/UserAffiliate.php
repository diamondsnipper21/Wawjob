<?php namespace iJobDesk\Models;


use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\User;
use DB;

class UserAffiliate extends Model {

	protected $table = 'user_affiliates';

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

	public function affiliatedUser() {
		return $this->hasone('iJobDesk\Models\User', 'id', 'affiliate_id');
	}

	public function affiliateUser() {
		return $this->hasone('iJobDesk\Models\User', 'id', 'user_id');
	}

	public function totalEarnedByUser() {
		return $this->hasMany('iJobDesk\Models\TransactionLocal', 'ref_user_id', 'affiliate_id')
					->where('transactions.type', TransactionLocal::TYPE_AFFILIATE)
					->where('transactions.user_id', $this->user_id)
					->sum('amount');
	}

	public static function checkAffiliated($user_id1, $user_id2) {
		return self::where(function($query) use ($user_id1, $user_id2) {
					$query->where('user_id', $user_id1)
							->where('affiliate_id', $user_id2);
				})->orWhere(function($query) use ($user_id1, $user_id2) {
					$query->where('user_id', $user_id2)
							->where('affiliate_id', $user_id1);
				})->exists();
	}
}
