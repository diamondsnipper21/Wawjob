<?php namespace iJobDesk\Models;

use DB;
use Log;

use iJobDesk\Models\Contract;

class HourlyLogMap extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'hourly_log_maps';

	protected $fillable = ['contract_id', 'date'];

	const STATUS_ENABLE = 1;
	const STATUS_DISABLE = 2;
	const STATUS_DONE = 3;

    /**
    * Get the contract
    */
    public function contract() {
    	return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
    }

	/**
	* @author paulz
	* @created Mar 31, 2016
	* @param string $week: this | last
	* @return array of ids of contracts which have activity during this or last week
	*/
	public static function getActiveHourlyContractIds($week = 'this') {
		if ($week == 'this') {
			$range = weekRange();
		} else if ($week == 'last') {
			$range = weekRange('-1 weeks');
		} else {
			return false;
		}

		list($from, $to) = $range;

		$cids = self::whereBetween('date', [$from, $to])
					->where('status', self::STATUS_ENABLE)
					->selectRaw('DISTINCT(contract_id) as cid')
					->pluck('cid')
					->toArray();

		return $cids;
	}

	/**
	* Get the hourly log data for the user
	* @param $params [buyer_id, contractor_id, from, to]
	*/
	public static function getUserLogMap($params = []) {
		$maps = HourlyLogMap::leftJoin('contracts', 'contract_id', '=', 'contracts.id');

		if ( isset($params['buyer_id']) ) {
			$maps = $maps->where('contracts.buyer_id', $params['buyer_id']);
		} else if ( isset($params['contractor_id']) ) {
			$maps = $maps->where('contracts.contractor_id', $params['contractor_id']);
		}
		
		$maps = $maps->where('hourly_log_maps.date', '>=', $params['from'])
					->where('hourly_log_maps.date', '<=', $params['to'])
					->where('hourly_log_maps.status', self::STATUS_ENABLE)
					->select([
						'hourly_log_maps.date', 
						'hourly_log_maps.contract_id', 
						'hourly_log_maps.act', 
						'hourly_log_maps.mins'
					])
					->orderBy('hourly_log_maps.date', 'asc')
					->get();

		return $maps;
	}

    /**
    * Get the total minutes worked
    * @param string $when: this (week), last (week), all (the weeks), custom date (the week of this date)
    * @param $contract
    */
    public static function getContractTotalMins($contract, $when = 'this') {
        $logs = self::where('contract_id', $contract->id);

        if ( $when != 'all' ) {
            if ($when == 'this') {
                $date = 'now';
            } else if ($when == 'last') {
                $date = '-1 weeks';
            } else {
                $date = $when;
            }

            list($from, $to) = weekRange($date);

            $logs = $logs->whereBetween('date', [$from, $to]);   
        }

        return $logs->sum('mins');
    }

	/**
	* Get the total amount of the hourly log data under review
	* @param $contract
	* @return int
	*/
	public static function getContractTotalMinsUnderReview($contract) {
		list($from, $to) = weekRange('-1 weeks');

		$total_mins = 0;

		try {
			/*
			$log_maps = self::where('contract_id', $contract->id)
							->where('date', '>=', $from)
							->where('date', '<=', $to)
							->orderBy('date', 'asc')
							->get();

			foreach ( $log_maps as $map ) {
	            $acts = json_decode($map->act, true);
	            if ( $acts ) {
	            	foreach ( $acts as $a ) {
	            		$total_mins += $a['r'];

	            		if ( isset($a['m']) ) {
	            			$total_mins += $a['m'];
	            		}
	            	}
	            }
			}
			*/

			$total_mins = self::where('contract_id', $contract->id)
								->where('date', '>=', $from)
								->where('date', '<=', $to)
								->where('status', self::STATUS_ENABLE)
								->sum('mins');

			self::where('contract_id', $contract->id)
				->where('date', '>=', $from)
				->where('date', '<=', $to)
				->update([
					'status' => self::STATUS_DONE
				]);

		} catch ( Exception $e ) {
			Log::error('[HourlyLogMap::getContractTotalAmountUnderReview()] ' . $e->getMessage());
			
			return 0;
		}

		return $total_mins;
	}

	/**
	* Get the total amount of the hourly log data for current week
	* @param $buyer_id
	* @return double
	*/
	public static function getUserTotalAmount($buyer_id) {
		list($from, $to) = weekRange();

		$total = self::leftJoin('contracts', 'contract_id', '=', 'contracts.id')
					 ->where('contracts.buyer_id', $buyer_id)
					 ->where('hourly_log_maps.date', '>=', $from)
					 ->where('hourly_log_maps.date', '<=', $to)
					 ->where('hourly_log_maps.status', self::STATUS_ENABLE)
					 ->selectRaw('SUM((hourly_log_maps.mins / 60) * contracts.price) AS total')
					 ->pluck('total');

		return doubleval($total[0]);
	}

	/**
	* Get the total amount of the hourly log data under working
	* @param $contract
	* @return double
	*/
	public static function getContractTotalAmount($contract) {
		list($from, $to) = weekRange();

		$total = self::where('contract_id', $contract->id)
					 ->where('date', '>=', $from)
					 ->where('date', '<=', $to)
					 ->where('status', self::STATUS_ENABLE)
					 ->selectRaw('SUM((mins / 60) * ' . $contract->price . ') AS total')
					 ->pluck('total');

		return doubleval($total[0]);
	}

	public static function disableInThisWeek($contract) {
		$week_range = weekRange();

		$week_start_date = date('Y-m-d', strtotime($week_range[0]));
		$week_end_date = date('Y-m-d', strtotime($week_range[1]));

		self::where('contract_id', $contract->id)
			->where('date', '>=', $week_start_date)
			->where('date', '<=', $week_end_date)
			->update(['status' => self::STATUS_DISABLE]);

		return self::where('contract_id', $contract->id)
				   ->where('date', '>=', $week_start_date)
				   ->where('date', '<=', $week_end_date)
				   ->sum('mins') / 60 * $contract->price;
	}

	public static function getLastUpdated() {
		$updated = self::orderBy('created_at', 'DESC')
					   ->pluck('created_at')
					   ->toArray();

		if (!$updated)
			return null;

		$time = strtotime($updated[0]);

		return date('g:i', $time) . ' ' . trans('common.' . date('a', $time));
	}
}