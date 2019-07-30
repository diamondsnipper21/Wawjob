<?php namespace iJobDesk\Models;

use DB;
use Config;
use Auth;
use Log;

use Illuminate\Database\Eloquent\SoftDeletes;

use iJobDesk\Models\User;
use iJobDesk\Models\HourlyLogMap; 
use iJobDesk\Models\Contract;

class HourlyReview extends Model {

	use SoftDeletes;

	protected $table = 'hourly_reviews';

	const STATUS_PENDING = 0; // Under review
	const STATUS_DONE = 1; // Moved to pending
	const STATUS_CANCELLED = 2; // Cancelled

	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    function __construct() {
        parent::__construct();
    }

	public function contract() {
		return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
	}

	public function buyer() {
		return $this->hasOne('iJobDesk\Models\User', 'id', 'buyer_id');
	}

	public function contractor() {
		return $this->hasOne('iJobDesk\Models\User', 'id', 'contractor_id');
	}

	public function isPending() {
		return $this->status == self::STATUS_PENDING;
	}

	public function isDone() {
		return $this->status == self::STATUS_DONE;
	}

	public function isCancelled() {
		return $this->status == self::STATUS_CANCELLED;
	}

	public function isDisputed() {
		return $this->disputed == 1;
	}

	/**
	* Get the hourly log under review for the contract
	* @param $contract_id
	* @return object
	*/
	public static function getContractHourlyReview($contract_id) {
		list($from, $to) = weekRange('-1 weeks');

		return self::where('contract_id', $contract_id)
					->where('hourly_from', $from)
					->where('hourly_to', $to)
					->first();
	}

	/**
	* Get the hourly log under review for the user
	* @param $params [buyer_id, contractor_id, from, to]
	* @return array
	*/
	public static function getHourlyReviews($params = []) {
		if ( !isset($params['from']) && !isset($params['to']) ) {
			list($from, $to) = weekRange('-1 weeks');
		} else {
			$from = $params['from'];
			$to = $params['to'];
		}

        $reviews = self::where('status', self::STATUS_PENDING);

		if ( isset($params['buyer_id']) ) {
			$reviews = $reviews->where('buyer_id', $params['buyer_id']);
		} else if ( isset($params['contractor_id']) ) {
			$reviews = $reviews->where('contractor_id', $params['contractor_id']);
		}

		return $reviews->where('hourly_from', $from)
						->where('hourly_to', $to)
						->orderBy('created_at', 'asc')
						->get();
	}

	/**
	* Get the total amount under review
	* @param $params ['buyer_id', 'contractor_id', 'contract_id', from, to]
	* @return double
	*/
	public static function getTotalHourlyReviewAmount($params = []) {
		if ( !isset($params['from']) && !isset($params['to']) ) {
			list($from, $to) = weekRange('-1 weeks');
		} else {
			$from = $params['from'];
			$to = $params['to'];
		}

		$total = self::where('status', self::STATUS_PENDING);

		if ( isset($params['buyer_id']) ) {
			$total = $total->where('buyer_id', $params['buyer_id']);
		} else if ( isset($params['contractor_id']) ) {
			$total = $total->where('contractor_id', $params['contractor_id']);
		}

		if ( isset($params['contract_id']) ) {
			$total = $total->where('contract_id', $params['contract_id']);
		}

		$total = $total->where('hourly_from', $from)
						->where('hourly_to', $to)
						->sum('amount');

		return doubleval($total);
	}

	/**
	* Get the total amount under review for the contract
	* @param $contract_id
	* @return double
	*/
	public static function getContractTotalAmount($contract_id) {
		list($from, $to) = weekRange('-1 weeks');

		$total = self::where('contract_id', $contract_id)
					->where('hourly_from', $from)
					->where('hourly_to', $to)
					->where('status', self::STATUS_PENDING)
					->sum('amount');

		return doubleval($total);
	}

	/**
	* Disallow manually logged hours in review
	* @param $contract_id
	* @return void
	*/
	public static function disAllowManualHours($contract_id) {
		list($from, $to) = weekRange('-1 weeks');

		try {
			$contract = Contract::findOrFail($contract_id);

			if ( $contract ) {
				$hourly_review = self::getContractHourlyReview($contract_id);
				if ( $hourly_review && !$hourly_review->isPending() ) {
					throw new Exception('Hourly logs has been already paid or cancelled.');
				}

				$log_maps = HourlyLogMap::where('contract_id', $contract_id)
										->where('date', '>=', $from)
										->where('date', '<=', $to)
										->orderBy('date', 'asc')
										->get();

				$total_manual_hours = 0;
				$total_manual_amount = 0;

				foreach ( $log_maps as $map ) {
		            $acts = json_decode($map->act, true);
		            if ( $acts ) {
		            	$has_manual_hours = false;
		            	foreach ( $acts as $k => $a ) {
		            		if ( $a['m'] && (!isset($a['allow_manual']) || !$a['allow_manual']) ) {
		            			$has_manual_hours = true;
		            			$acts[$k]['allow_manual'] = 0;
		            			$map->mins = $map->mins - $a['m'];

		            			$total_manual_hours += $a['m'];
		            		}
		            	}

		            	if ( $has_manual_hours ) {
		            		$map->act = json_encode($acts);
		            		if ( !$map->save() ) {
		            			throw new Exception('An error occured while updating HourlyLogMap.');
		            		}
		            	}
		            }
				}

				if ( $hourly_review && $total_manual_hours ) {
					$hourly_review->hourly_mins = $hourly_review->hourly_mins - $total_manual_hours;

					if ( $hourly_review->hourly_mins <= 0 ) {
						$hourly_review->delete();
					} else {
						$hourly_review->amount = $hourly_review->amount - $hourly_review->contract->buyerPrice($total_manual_hours);

						if ( $hourly_review->save() ) {
							return true;
						}
					}
				}
			} else {
				throw new Exception('Invalid contract requested.');
			}

		} catch ( Exception $e ) {
			Log::error('[HourlyReview::disAllowManualHours()] ' . $e->getMessage());
			return false;
		}
		
		return false;
	}
}