<?php namespace iJobDesk\Models;

use Config;
use DB;
use Log;

use Illuminate\Database\Eloquent\SoftDeletes;

use iJobDesk\Models\Contract;
use iJobDesk\Models\ContractMeter;
use iJobDesk\Models\Cronjob;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\HourlyLogMap;

class HourlyLog extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'hourly_logs';

	/**
	* The attributes that should be mutated to dates.
	*
	* @var array
	*/
	protected $dates = ['taken_at'];

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = false;

	/**
    * Get the contract
    */
    public function contract() {
    	return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
    }	

	public static function getLogUnit()
	{
		return Config::get("settings.hourly_log_unit");
	}

	public static function getMaxSlot()
	{
		return 60 / self::getLogUnit();
	}

	/**
	* Get the total amount of the hourly log data for current week
	* @param $buyer_id
	* @return double
	*/
	public static function getUserTotalAmount($buyer_id) {
		list($from, $to) = weekRange();

		$from .= ' 00:00:00';
		$to .= ' 23:59:59';

		$total = self::leftJoin('contracts', 'contract_id', '=', 'contracts.id')
					 ->where('contracts.buyer_id', $buyer_id)
					 ->where('hourly_logs.taken_at', '>=', $from)
					 ->where('hourly_logs.taken_at', '<=', $to)
					 ->where('hourly_logs.is_deleted', 0)
					 ->selectRaw('SUM(contracts.price / 6) AS total')
					 ->pluck('total');

		return doubleval($total[0]);
	}

	/**
	* Retrieves segments info from screenshots of {contract, date}
	* We should highlight the beginning and the end of each contiguous segment
	*
	* The result of this method will be array of each hour
	* Each hour holds the following
	*  1. List of 6 screen at maximum (screenshot thumbnail URL, score, taken_at)
	*  2. the segments for comments (if hour goes over or memo changes, then it should be a new segment. The beginning and the end of each segment will have its green area rounded.)
	*
	* @author paulz
	* @created Mar 16, 2016
	* @param integer $cid:   Contract ID
	* @param string $wdate: Work Diary date
	* @param string $tz: Timezone string (see `timezones`.name)
	*/
	public static function getDiary($cid, $wdate, $tz = '')
	{
		$logUnit = self::getLogUnit();
		$maxSlot = self::getMaxSlot();

		if ( !$cid || !$wdate ) {
			return false;
		}

		$query = DB::table('hourly_logs')
					->where('contract_id', $cid)
					->where('is_deleted', 0)
					->orderBy('taken_at');

		$sel = "id, comment, score, active_window, is_manual, is_overlimit";

		if ( empty($tz) || $tz == 'Etc/UTC' || $tz == 'UTC') {
			$tz = '';
		}

		if ($tz) {
			$gmt_offset = Timezone::nameToOffset($tz);
			$str_offset = timezoneToString($gmt_offset, false);
			$sel .= ", taken_at as utc_taken_at, CONVERT_TZ(taken_at, '+00:00', '$str_offset') as taken_at";
			$query->havingRaw("DATE(taken_at) = '$wdate'");
		} else {
			$sel .= ", taken_at";
			$query->whereRaw("DATE(taken_at) = '$wdate'");
		}

		$query->selectRaw($sel);

		/* $items may have up to 24 * 6 = 144 screenshots as it is for one day */
		$slots = $query->get();

		// Calculate segments
		$res = [];
		$nAuto = 0;
		$nOverlimit = 0;
		$nManual = 0;
		$nTotal = 0;

		foreach ($slots as $k => $item) {
			if ( !$tz ) {
				$slots[$k]->utc_taken_at = $item->taken_at;
			}

			$dt = date_create($item->taken_at);
			$hr = intval(date_format($dt, "H"));
			$min = intval(date_format($dt, "i"));

			$slots[$k]->hour = $hr;
			$slots[$k]->minute = $min;

	  		// e.g: 9:00 am, 10:20 pm
			$hourLabel = $hr;
			if ($hr < 12) {
				$ampm = "am";

				if ($hr == 0) {
					$hourLabel = 12;
				}
			} else {
				$ampm = "pm";
				if ($hr > 12) {
					$hourLabel = $hr - 12;
				}
			}

			if ( !isset($res[$hr]) ) {
				$res[$hr] = [
					'label' => [
						'hour' => $hourLabel,
						'ampm' => $ampm,
					],
					'seg' => [],
					'slots' => []
				];

				for($si = 0; $si < $maxSlot; $si++) {
					$res[$hr]['slots'][$si] = [
						'is_empty' => true,
						'timeLabel' => sprintf("%d:%02d", $hourLabel, $logUnit * $si) . " $ampm"
					];
				}
			}

	  		// Slot order: 0 ~ 5
			$minuteIndex = floor($item->minute / $logUnit);

			$slot = (array)$item;

			$slot["timeLabel"] = sprintf("%d:%02d", $hourLabel, $item->minute) . " $ampm";
			$slot["is_empty"] = false;

			$utc_dt = date_create($item->utc_taken_at);
			$utc_datetime = date_format($utc_dt, "YmdHi");

			if ( !$slot['is_manual'] ) {
				$slot["link"] = [
					"full" => resourceUrl('ss', $cid, $utc_datetime, 'full'),
					"thumbnail" => resourceUrl('ss', $cid, $utc_datetime, 'thumbnail')
				];
			}

			$res[$hr]['slots'][$minuteIndex] = $slot;

			if ($slot['is_overlimit']) {
				$nOverlimit += 10;
			} else {
				if ($slot['is_manual']) {
					$nManual += 10;
				} else {
					$nAuto += 10;
				}
			}
		}

		$nTotal = $nAuto + $nOverlimit + $nManual;

		$info = [
			'total' => $nTotal,
			'auto' => $nAuto,
			'overlimit' => $nOverlimit,
			'manual' => $nManual
		];

		foreach($res as $hr => $hourGroup) {
			$minuteIndex = 0;
			while ($minuteIndex < $maxSlot) {
				$slot = $hourGroup["slots"][$minuteIndex];

				if ( !isset($slot["comment"]) ) {
					$minuteIndex++;
					continue;
				}

	    		// Check if slot is not the last slot in this hour and has same content with the next one
				$currentComment = $slot["comment"];
				$isManual = $slot["is_manual"];
				$isOverlimit = $slot["is_overlimit"];
				$startIndex = $minuteIndex;
				$endIndex = $minuteIndex;
				if ($endIndex < $maxSlot - 1) {
					do {            
						$nextSlot = $hourGroup["slots"][$endIndex + 1];
						if (isset($nextSlot["comment"]) && $currentComment == $nextSlot["comment"] && $isManual == $nextSlot["is_manual"] && $isOverlimit == $nextSlot["is_overlimit"]) {
							$endIndex++;
						} else {
							break;
						}
					} while ($endIndex < $maxSlot - 1);
				}

				$res[$hr]["seg"][] = [
					'from' => $startIndex,
					'to' => $endIndex,
					'comment' => $currentComment,
					'is_manual' => $isManual,
					'is_overlimit' => $isOverlimit,
		      		'start' => true, // is fixed in the code lines below
		      		'end' => true    // is fixed in the code lines below
	      		];

	      		$minuteIndex = $endIndex + 1;
	  		}
		}

		/*
		* For each hour, check if its first segment is continuity of previous hour's last segment (start = false) or its last segment is continued to next hour's first segment (end = false)
		*/
		$k = 0;
		$hourCount = count($res);

		foreach($res as $hr => $hourGroup) {
			$thisHourSegs = $hourGroup['seg'];

		  	// Check first segment when this is not the first hour group of $res
			$thisHourStartSeg = $thisHourSegs[0];
			if ($k > 0 && isset($res[$hr - 1])) {
				$prevHourLastSeg = end($res[$hr - 1]['seg']);

		    	// If this segment ends at last slot
				if ($prevHourLastSeg['to'] == $maxSlot - 1) {
		      		// If comment is same, too
					if ($prevHourLastSeg['comment'] == $thisHourStartSeg['comment']) {
		        		// Then, mark $startSeg as the continuity of the previous hour's last segment
						$res[$hr]['seg'][0]['start'] = false;
					}
				} 
			}

		  	// Check last segment when this is not the last hour group of $res and this last segment ends at last slot (6th slot in this hour)
			$thisHourEndSeg = end($thisHourSegs);
			if ($k < $hourCount - 1 && isset($res[$hr + 1]) && $thisHourEndSeg['to'] == $maxSlot - 1) {
				$nextHourFirstSeg = $res[$hr + 1]['seg'][0];

		    	// If this segment starts at first slot
				if ($nextHourFirstSeg['from'] == 0) {
		      		// If comment is same, too
					if ($nextHourFirstSeg['comment'] == $thisHourEndSeg['comment']) {
		        		// Then, mark $startSeg as the continuity of the previous hour's last segment
						$thisHourSegCount = count($thisHourSegs);
						$res[$hr]['seg'][$thisHourSegCount - 1]['end'] = false;
					}
				} 
			}

			$k++;
		}

		return [$info, $res];
	}

	/**
	* Returns JSON info for screenshot 
	*
	* @author paulz
	* @created Mar 19, 2016
	* @param integer $id: `hourly_logs`.id
	* @param float $tz: Timezone offset
	*/
	public static function getSlotInfo($id, $tz = '')
	{
		if ( !$id ) {
			return false; 
		}

		$row = self::find($id);
		if ( !$row->activity ) {
			Log::error("[HourlyLog::getSlotInfo] Could not find activity data for screenshot #$id.");
			return false;
		}

		$utc_arr = json_decode($row->activity, true);
		if ( !$utc_arr ) {
			Log::error("[HourlyLog::getSlotInfo] Failed to JSON decode for screenshot #$id.");
			return false;
		}

		// Convert timezone if $tz is not UTC
		// Assume $tz = +6 (UTC +06:00)
		if ($tz) {
			$gmt_offset = Timezone::nameToOffset($tz);

			foreach($utc_arr as $utc_tm => $act) {
	    		// 02:10 => [2, 10]
				list($utc_h, $utc_m) = explode(":", $utc_tm, 2);

	    		// [2, 10] => 130 mins
				$utc_mins = $utc_h * 60 + $utc_m;

	    		// 130 mins + 6 hours => 490 mins
				$mins = $utc_mins + $gmt_offset * 60;

	    		// If it goes to previous date or next date, then add or subtract one day to get valid hour:minute
				if ($mins < 0) {
					$mins += 1440;
				} else if ($mins > 1440) {
					$mins -= 1440;
				}

	    		// 490 => 08:10 (next date)
				$tm = formatMinuteInterval($mins, false);
				$arr[$tm] = $act;
			}
		} else {
			$arr = $utc_arr;
		}

		return $arr;
	}

	/**
	* Add manual time
	*
	* @author paulz
	* @created Mar 23, 2016
	*
	* @param integer $cid: Contract ID
	* @param string $memo: Memo to be filled in the manual slots
	*/
	public static function addManualSlots($cid, $opts) {
		$defaults = [
			'from' => '',
			'to' => '',
			'memo' => '',
			'tz' => ''
		];

		$opts = array_merge($defaults, $opts);
		if ( !$opts['from'] || !$opts['to'] ) {
			Log::error("[HourlyLog::addManualSlots()] Invalid time range given.");
			return false;
		}

		// Swap $from and $to when from > to
		if ($opts['from'] > $opts['to']) {
			$tmp = $opts['from'];
			$opts['from'] = $opts['to'];
			$opts['to'] = $tmp;
		}

		// Check if exceed selected date
		list($to_date, $to_time) = explode(' ', $opts['to']);
		list($to_h, $to_m, $to_s) = explode(':', $to_time);
		if ( $to_h >= 24 && $to_m > 0 ) {
			$to_m = '00';
		}
		$opts['to'] = implode(' ', [$to_date, implode(':', [$to_h, $to_m, $to_s])]);

		// Check whether this contract is hourly, and manual time is allowed for this contract
		$contract = Contract::find($cid);
		if ( !$contract ) {
			Log::error("[HourlyLog::addManualSlots()] Contract #$cid is not found.");
			return false;
		}

		// Whether this contract is an hourly contract
		if ( !$contract->isHourly() ) {
			Log::error("[HourlyLog::addManualSlots()] Contract #$cid is not an hourly contract.");
			return false;
		}

		// Whether it is allowed to add manual time
		if ( !$contract->isAllowedManualTime() ) {
			Log::error("[HourlyLog::addManualSlots()] Contract #$cid is not allowed to add manual time. Please contact your client.");
			return false;
		}

		// Whether this contract is active
		if ( !$contract->isOpen() ) {
			Log::error("[HourlyLog::addManualSlots()] Contract #$cid is not an active contract.");
			return false;
		}

		// Convert time range to UTC if timezone is given.
		$timezone = $opts['tz'];

		// Fix the time range to HH:m0 ~ HH:m9
		$from = convertTz($opts['from'], 'UTC', $timezone);
	
		// truncate last digit => 0
		$t = strtotime($from);
		$t -= $t % 10;
		$from = date('Y-m-d H:i:s', $t);

		$to = convertTz($opts['to'], 'UTC', $timezone);
		
		// round this range to 10 mins
		$t = strtotime($to);
		$t += 599 - $t % 600;
		$to_max = date('Y-m-d H:i:s', $t);

		// Remove deleted records in this range
		self::where('contract_id', $cid)
			->whereBetween('taken_at', [$from, $to_max])
			->where('is_deleted', 1)
			->delete();

		// Check the weekly limit
		if ( !$contract->isNoLimit() ) {
			$limit = $contract->limit;
			$nSlot = $limit * self::getMaxSlot();
		}

		// Get the total slots logged in this week
		list($week_from, $week_to) = weekRange();

		$week_from .= ' 00:00:00';
		$week_to .= ' 23:59:59';

		$nTotalSlot = self::where('contract_id', $cid)
							->where('is_deleted', 0)
							->whereBetween('taken_at', [$week_from, $week_to])
							->count();

		// Find slots which are included in this time range
		$records = self::where('contract_id', $cid)
						->where('is_deleted', 0)
						->whereBetween('taken_at', [$from, $to_max])
						->select(['id', 'taken_at'])
						->get();

		$slots = [];
		foreach($records as $slot) {
			$t = strtotime($slot->taken_at);
			
			# 
			# Truncate last digit of minute to 0
			# 2016-03-23 02:38:42 => 2016-03-23 02:38:40
			$t -= $t % 600;
			$slots[$t] = $slot;
		}

		$newSlots = [];
		$now = strtotime($from);
		$to_time = strtotime($to);
		while ($now < $to_time) {
			if ( isset($nSlot) && $nSlot <= $nTotalSlot ) {
				break;
			}

			$taken_at = date('Y-m-d H:i:s', $now);

			if ( $taken_at >= $week_from && $now <= time() ) {
		  		// If this slot is already logged, skip
				if ( !isset($slots[$now]) ) {
					$newSlots[] = [
						'contract_id' => $cid,
						'is_manual' => 1,
						'taken_at' => $taken_at,
						'comment' => $opts['memo'],
					];

					$nTotalSlot++;
				}
			}

	  		// Move to next slot (10 mins)
			$now += 600;
		}

		if ( !self::insert($newSlots) ) {
			Log::error("Failed to insert new records into hourly_logs.");
			return false;
		}

        // Update hourly_log_map
        if ( self::generateMap($cid) ) {
            self::updateContractMeter($cid);
        }

		return true;
	}

	/**
	* return limits and tracked information
	* All the datetime used in this method is UTC time.
	*
	* @author paulz
	* @created Mar 23, 2016
	*/
	public static function limitInfo($cid) {
		if ( !$cid ) {
			return false;
		}

		// Get hourly limit of this contract
		$contract = Contract::find($cid);
		if ( !$contract ) {
			Log::error("[HourlyLog::limitInfo()] Contract #$cid is not found.");
			return false;
		}

		if ( !$contract->isHourly() ) {
			Log::error("[HourlyLog::limitInfo()] Contract #$cid is not an hourly contract.");
			return false;
		}

		// Weekly limit of this contract
		$limit = $contract->limit;

		// Calculate week date range of given date
		list($from, $to) = weekRange();

		$from .= ' 00:00:00';
		$to .= ' 23:59:59';

		// Count slots of this week
		$count = self::where('contract_id', $cid)
					->whereBetween('taken_at', [$from, $to])
					->where('is_deleted', 0)
					->count();

		// Count slots of today
		$count_today = self::where('contract_id', $cid)
							->whereBetween('taken_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
							->where('is_deleted', 0)
							->count();

		$maxSlot = self::getMaxSlot();

		// Mark slots over limit
		$return = [];
		$return['tracked'] = $count / $maxSlot;
		$return['tracked_today'] = $count_today / $maxSlot;
		$return['limit'] = $limit;
		
		return $return;
	}

	/**
	* checks if contract's hourly tracking is at its limit
	* All the datetime used in this method is UTC time.
	*
	* @author paulz
	* @created Mar 23, 2016
	*/
	public static function isLimit($cid) {
		if ( !$cid ) {
			return false;
		}

		// Get hourly limit of this contract
		$contract = Contract::find($cid);
		if ( !$contract ) {
			Log::error("[HourlyLog::islimit()] Contract #$cid is not found.");
			return false;
		}

		if ( !$contract->isHourly() ) {
			Log::error("[HourlyLog::islimit()] Contract #$cid is not an hourly contract.");
			return false;
		}

		// Weekly limit of this contract
		if ( $contract->isNoLimit() ) {
			return false;
		}
		
		$limit = $contract->limit;
		$nSlot = $limit * self::getMaxSlot(); 

		// Calculate week date range of given date
		list($from, $to) = weekRange();

		$from .= ' 00:00:00';
		$to .= ' 23:59:59';

		// Count slots of this week
		$count = self::where('contract_id', $cid)
						->where('is_deleted', 0)
						->whereBetween('taken_at', [$from, $to])
						->count();

		// Mark slots over limit
		if ($count >= $nSlot) {
			return true;
		}

		return false;
	}

	/**
	* Deletes records from `hourly_logs`
	*
	* @author Ro Un Nam
	* @since Dec 26, 2017
	*/
	public static function deleteSlot($user_id, $contract_id, $sid) {
		$row = self::leftJoin('contracts', 'contract_id', '=', 'contracts.id')
					->whereIn('hourly_logs.id', $sid)
					->where('contracts.contractor_id', $user_id)
					->where('hourly_logs.contract_id', $contract_id)
					->select(['hourly_logs.contract_id', 'hourly_logs.taken_at'])
					->first();

		if ( !$row ) {
			return false;
		}

		self::whereIn('id', $sid)
			->where('contract_id', $contract_id)
			->update([
				'is_deleted' => 1
			]);

        // Update hourly_log_map
        if ( self::generateMap($contract_id) ) {
            self::updateContractMeter($contract_id);
        }

		return true;
	}

	/**
	* Requested by Ri Chol Min
	*
	* Update memo for records of `hourly_logs`
	*
	* @author paulz
	* @created Mar 22, 2016
	*
	* @param array $sid: array of `hourly_logs`.id
	* @param string $memo: New work diary memo to update to
	*/
	public static function updateMemo($sid, $memo)
	{
		if ( empty($sid) ) {
			Log::error('[HourlyLog::updateMemo()] Invalid screenshot IDs given.');
			return false;
		}

		return self::whereIn('id', $sid)
					->update([
						'comment' => $memo
					]);
	}


	/**
	* Generate hourly log map
	*
	* @author Ro Un Nam
	* @since Dec 26, 2017
	*/
	public static function generateMap($cid) {
		if ( !$cid ) {
			return false;
		}

		try {

			list($from, $to) = weekRange();

			$from .= ' 00:00:00';
			$to .= ' 23:59:59';

			// Check the deleted logs for this week and updated log maps
			$deleted = self::where('contract_id', $cid)
							->where('is_deleted', 1)
							->whereBetween('taken_at', [$from, $to])
							->get();

			$daily_deleted = [];
			if ( $deleted ) {
				foreach ( $deleted as $r ) {
					$r_date = $r->taken_at->format('Y-m-d');

					if ( !isset($daily_deleted[$r_date]) ) {
						$daily_deleted[$r_date] = 0;
					}

					$daily_deleted[$r_date] += 10;
				}

				foreach ( $daily_deleted as $d => $m ) {
					$map = HourlyLogMap::where('contract_id', $cid)
										->where('date', $d)
										->first();

					if ( $map ) {
						$map->mins = $map->mins - $m;
						if ( $map->mins <= 0 ) {
							$map->delete();
						} else {
							$map->save();
						}
					}
				}

				self::where('contract_id', $cid)
					->where('is_deleted', 1)
					->delete();
			}

			$rows = self::where('contract_id', $cid)
						->where('is_overlimit', 0)
						->where('is_calculated', 0)
						->where('is_deleted', 0)
						->orderBy('taken_at')
						->get();

			$daily = [];
			$act = [];

			foreach($rows as $i => $row) {
				$row_date = $row->taken_at->format('Y-m-d');

				if ( !isset($daily[$row_date]) ) {
					$daily[$row_date] = [
						'mins' => 0,
						'act' => json_encode($act)
					];
				}

				$comment = $row->comment;

				if ( !isset($act[$row_date]) ) {
					$act[$row_date] = [
						'min' => 0,
						'comments' => [],
					];
				}

				if ( !isset($act[$row_date]['comments'][$comment]) ) {
					$act[$row_date]['comments'][$comment] = [
						'r' => 0, // Regular time
						'm' => 0, // Manual time
						'allow_manual' => 1
					];
				}

				if ( $row->is_manual ) {
					$act[$row_date]['comments'][$comment]['m'] += 10;
				} else {
					$act[$row_date]['comments'][$comment]['r'] += 10;
				}

				$act[$row_date]['min'] += 10;

				$daily[$row_date] = [
					'mins' => $act[$row_date]['min'],
					'act' => json_encode($act[$row_date]['comments'])
				];

				$row->is_calculated = 1;
				$row->save();
			}

			if ( $daily ) {
				foreach($daily as $date => $row) {
			    	// Insert or update
					$map = HourlyLogMap::firstOrNew([
						'contract_id' => $cid,
						'date' => $date
					]);

					if ( $map ) {
						$map->mins += $row['mins'];

						$acts = json_decode($map->act, true);
						if ( !$acts ) {
							$acts = [];
						}

						$row_acts = json_decode($row['act'], true);

						if ( $row_acts ) {
							$new_acts = $temp_acts = [];

							foreach ( $acts as $a ) {
								if ( !isset($temp_acts[$a['c']]) ) {
									$temp_acts[$a['c']] = [
										'r' => 0, // Regular time
										'm' => 0, // Manual time
										'allow_manual' => 1
									];
								}

								$temp_acts[$a['c']]['r'] = isset($a['r']) ? $a['r'] : 0;
								$temp_acts[$a['c']]['m'] = isset($a['m']) ? $a['m'] : 0;
								$temp_acts[$a['c']]['allow_manual'] = isset($a['allow_manual']) ? $a['allow_manual'] : 0;
							}

							foreach ( $row_acts as $rc => $ra ) {
								if ( isset($temp_acts[$rc]) ) {
									if ( $ra['r'] ) {
										$temp_acts[$rc]['r'] += $ra['r'];
									}

									if ( $ra['m'] ) {
										$temp_acts[$rc]['m'] += $ra['m'];
									}
								} else {
									if ( $ra['r'] ) {
										$temp_acts[$rc]['r'] = $ra['r'];
									}

									if ( $ra['m'] ) {
										$temp_acts[$rc]['m'] = $ra['m'];
									}
								}
							}

							foreach ( $temp_acts as $tc => $tm ) {
								$new_acts[] = [
									'c' => $tc,
									'r' => isset($tm['r']) ? $tm['r'] : 0,
									'm' => isset($tm['m']) ? $tm['m'] : 0,
									'allow_manual' => isset($tm['allow_manual']) ? $tm['allow_manual'] : 0,
								];
							}

							unset($temp_acts);
							$map->act = json_encode($new_acts);
						}
					} else {
						$map->mins = $row['mins'];
						$map->act = $row['act'];
					}

					// Check if exceed one day
					if ( $map->mins > 1440 ) {
						$map->mins = 1440;
					}
					
					$map->save();
				}
			}

		} catch ( Exception $e ) {
			Log::error('[HourlyLog::generateMap()] ' . $e->getMessage());
		 	return false;
		}

		return true;
	}

	/**
	* Update contract_meters for given contracts
	*
	* @author Ro Un Nam
	* @since Dec 25, 2017
	*/
	public static function updateContractMeter($contract_id = 0)
	{
		if ( !$contract_id ) {
			return false;
		}

		try {
			$contract = Contract::where('id', $contract_id)->first();

			if ( !$contract ) {
				return false;
			}

			$week_mins = HourlyLogMap::getContractTotalMins($contract);

			$contract->meter->this_mins = $week_mins;
			$contract->meter->this_amount = $contract->buyerPrice($week_mins);

			$contract->meter->save();
		} catch ( Exception $e ) {
			Log::error('[HourlyLog::updateContractMeter] ' . $e->getMessage());
		 	return false;
		}

		return true;
	}

	public static function dateHistory($cid, $wdate, $timezone = 'UTC') {

		if ( !$cid ) {
			return '';
		}

		$rows = self::where('contract_id', $cid)
					->where('is_deleted', 0);

		if ( $timezone && $timezone != 'UTC' ) {
			$timezone_offset = getTimezoneOffset('UTC', $timezone);
			$rows = $rows->selectRaw('CONVERT_TZ(taken_at, "+00:00", "' . $timezone_offset . '") AS taken_at');
		} else {
			$rows = $rows->selectRaw('taken_at');
		}

		$rows = $rows->get();

		$dates = array();
            
        foreach( $rows as $row ) {
            $dates[] = strtotime(date('Y-m-d', strtotime($row->taken_at)));
        }

		$contract = Contract::where('id', $cid)->first();

		$start_date = strtotime(date('Y-m-d', strtotime($contract->started_at)));
		if ($contract->ended_at == NULL) {
			$end_date = strtotime(date('Y-m-d'));
		} else {
			$end_date = strtotime(date('Y-m-d', strtotime($contract->ended_at)));
		}

		$interval = $end_date - $start_date;
		$interval_date = $interval/(24*3600);

		$log_dates = array();

		for ($i = 0; $i <= $interval_date; $i++) {
			$cal_date = $start_date + $i * 24 * 3600;
		    if (in_array($cal_date, $dates)) {
		    	$log_dates[] = date('Y-m-d', $cal_date);
		    }
		}

		$log_dates[] = $wdate;

		return $log_dates;
	}

	/**
	* Get the total amount under working
	* @param $buyer_id
	* @return double
	*/
	public static function getTotalAmount($buyer_id) {
		list($from, $to) = weekRange();

		$from .= ' 00:00:00';
		$to .= ' 23:59:59';

		$total = self::leftJoin('contracts', 'contract_id', '=', 'contracts.id')
					->where('contracts.buyer_id', $buyer_id)
					->where('hourly_logs.taken_at', '>=', $from)
					->where('hourly_logs.taken_at', '<=', $to)
					->where('hourly_logs.is_deleted', 0)
					->selectRaw('SUM(contracts.price / 6) AS total')
					->pluck('total');

		return doubleval($total[0]);
	}
}