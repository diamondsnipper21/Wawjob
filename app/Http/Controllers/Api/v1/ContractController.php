<?php namespace iJobDesk\Http\Controllers\Api\v1;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Api\v1\ApiController;

use Illuminate\Http\Request;

use Auth;

// Models
use iJobDesk\Models\Contract;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\NotifyInsufficientFund;

class ContractController extends ApiController {

	/**
	* Constructor
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Check hourly limit.
	*
	* @param  Request $request
	* @return JSON
	*/
	public function limit(Request $request)
	{
		$payload = $this->parseJWT($request->header('JWT'));
		$contract_id = $payload['contract'];

		$limit_info = HourlyLog::limitInfo($contract_id);

		return response()->json([
			'Tracked' => $limit_info['tracked'],
			'Tracked_today' => $limit_info['tracked_today'],
			'Limit' => $limit_info['limit']
		]);
	}

	/**
	* Upload time log.
	*
	* @param  Request $request
	* @return JSON
	*/
	public function timelog(Request $request) {
		try {
			$payload = $this->parseJWT($request->header('JWT'));
			$logs = $payload['logs'];

			/*
			$logs = [
				'1525005999' => [
					'contract' => 4,
					'comment' => 'Checking tool',
					'active_window' => '',
					'activities' => [
						'12:30' => ['k' => 0, 'm' =>0],
						'12:31' => ['k' => 10, 'm' =>0],
						'12:32' => ['k' => 20, 'm' =>0],
						'12:33' => ['k' => 30, 'm' =>0],
						'12:34' => ['k' => 40, 'm' =>0],
						'12:35' => ['k' => 50, 'm' =>0],
						'12:36' => ['k' => 60, 'm' =>0],
						'12:37' => ['k' => 70, 'm' =>0],
						'12:38' => ['k' => 80, 'm' =>0],
						'12:39' => ['k' => 90, 'm' =>0],
						'12:40' => ['k' => 0, 'm' =>0],
						'12:41' => ['k' => 0, 'm' =>0],
						'12:42' => ['k' => 0, 'm' =>0],
						'12:43' => ['k' => 0, 'm' =>0],
						'12:44' => ['k' => 0, 'm' => 0],
						'12:45' => ['k' => 0, 'm' => 0],
						'12:46' => ['k' => 0, 'm' => 0],
					]
				]
			];
			*/

			if ( $logs ) {

				$return = [];

				foreach ($logs as $time => $log) {
					$time_item = [];
					$time = intval($time);
					$time_item['time'] = $time;
					
					$contract_id = $log['contract'];
					$comment = $log['comment'];
					$active_window = $log['active_window'];
					$activities = $log['activities'];

					$contract = Contract::findOrFail($contract_id);
					if ( $contract ) {
						if ( $contract->isPaused() ) {
							return response()->json([
								'error_code' => 8, 
								'error' => trans('message.api.error.8')
							]);
						} else if ( $contract->isSuspended() ) {
							return response()->json([
								'error_code' => 9, 
								'error' => trans('message.api.error.9')
							]);
						} else if ( $contract->isClosed() ) {
							return response()->json([
								'error_code' => 10, 
								'error' => trans('message.api.error.10')
							]);
						} else if ( $contract->contractor->isSuspended() ) {
							return response()->json([
								'error_code' => 3, 
								'error' => trans('message.api.error.3')
							]);
						} else if ( $contract->buyer->isSuspended() ) {
							return response()->json([
								'error_code' => 11, 
								'error' => trans('message.api.error.11')
							]);
						} else if ( HourlyLog::isLimit($contract_id) ) {
							return response()->json([
								'error_code' => 7, 
								'error' => trans('message.api.error.7')
							]);
						} else {
					  		// Process screeenshot uploaded.
							$screenshot = $request->file('screenshot_' . $time);
							if ($screenshot && $screenshot->isValid()) {

								// Check the buyer's balance
								$totalBalance = $contract->buyer->myBalance();

								if ( Wallet::MIN_HOURLY_LIMIT >= $totalBalance && !$contract->isAllowedOverTime() ) {

									return response()->json([
										'error_code' => 15, 
										'error' => trans('message.api.error.15')
									]);
								} else {

									$upload_location = get_screenshot_path($contract_id, date('YmdHi', $time), 'array');
									$screenshot->move($upload_location['path'], $upload_location['filename']);

									// Record screenshot info.
									$formatted_time = date('Y-m-d H:i:s', $time);

									$score = $this->updateScore($contract_id, $activities, $time);

									// Check if the screenshot is already taken and uploaded and delete original screenshot
									HourlyLog::where('contract_id', $contract_id)
											->whereBetween('taken_at', minuteRange($time))
											->delete();

									$hourlyLog = new HourlyLog;

									$hourlyLog->contract_id = $contract_id;
									$hourlyLog->comment = $comment;
									$hourlyLog->activity = json_encode($activities);
									$hourlyLog->score = $score;
									$hourlyLog->active_window = $active_window;
									$hourlyLog->taken_at = $formatted_time;

									if ( $hourlyLog->save() ) {
										if ( !$contract->tracked_time ) {
											$contract->tracked_time = 1;
											$contract->save();
											
											/*
											$contract_name = sprintf('<a href="%s">%s</a>', _route('contract.contract_view', ['id' => $contract->id]), $contract->title);

											if ( $contract->contractor->userNotificationSetting->time_logging_begins ) {
												EmailTemplate::send($contract->contractor, 'TIMELOG_STARTED', 0, [
													'USER' => $contract->contractor->fullname(),
													'CONTRACT' => $contract_name,
												]);
											}

											if ( $contract->buyer->userNotificationSetting->time_logging_begins ) {
												EmailTemplate::send($contract->buyer, 'TIMELOG_STARTED', 0, [
													'USER' => $contract->buyer->fullname(),
													'CONTRACT' => $contract_name,
												]);
											}
											*/
										}

								        // Update hourly_log_map
								        if ( HourlyLog::generateMap($contract->id) ) {
								            HourlyLog::updateContractMeter($contract->id);
								        }

										if ( Wallet::MIN_HOURLY_LIMIT >= $totalBalance ) {
											$contract_link = _route('contract.contract_view', ['id' => $contract->id], true, null, $contract->contractor);
											$deposit_link = route('user.deposit');

											// Check if already sent an email
											if ( !NotifyInsufficientFund::where('contract_id', $contract->id)
																	->where('user_id', $contract->contractor_id)
																	->count() ) {

												EmailTemplate::send($contract->contractor, 'INSUFFICIENT_FUND_FOR_CONTRACT', 1, [
													'USER' => $contract->contractor->fullname(),
													'CONTRACT_TITLE' => $contract->title,
													'CONTRACT_URL' => $contract_link,
												]);

												NotifyInsufficientFund::addNew([
													'contract_id' => $contract->id,
													'user_id' => $contract->contractor_id,
												]);
											}

											if ( !NotifyInsufficientFund::where('contract_id', $contract->id)
																	->where('user_id', $contract->buyer_id)
																	->count() ) {
												EmailTemplate::send($contract->buyer, 'INSUFFICIENT_FUND_FOR_CONTRACT', 2, [
													'USER' => $contract->buyer->fullname(),
													'CONTRACT_TITLE' => $contract->title,
													'CONTRACT_URL' => $contract_link,
													'DEPOSIT_URL' => $deposit_link,
												]);

												NotifyInsufficientFund::addNew([
													'contract_id' => $contract->id,
													'user_id' => $contract->buyer_id,
												]);
											}

											return response()->json([
												'error_code' => 13, 
												'error' => trans('message.api.error.13')
											]);
										}
									} else {
										return response()->json([
											'error_code' => 99, 
											'error' => trans('message.api.error.99')
										]);
									}
								}
							} else {
								return response()->json([
									'error_code' => 12, 
									'error' => trans('message.api.error.12')
								]);
							}
						}
					} else {
						return response()->json([
							'error_code' => 6, 
							'error' => trans('message.api.error.6')
						]);
					}

					$return[] = $time_item;

				}
			} else {
				return response()->json([
					'error_code' => 14, 
					'error' => trans('message.api.error.14')
				]);
			}
		} catch (Exception $e) {
			return response()->json([
				'error_code' => 100, 
				'error' => 'Exception: ' . $e->getMessage()
			]);
		}

		return response()->json($return);
	}

	// Sample logs value
	/*
	{"11:37":{"k":9,"m":22},"11:38":{"k":0,"m":0},"11:39":{"k":0,"m":27},"11:40":{"k":0,"m":16},"11:41":{"k":14,"m":18},"11:42":{"k":3,"m":43}}
	*/
	protected function updateScore($cid, $logs, $time) {
		list($mf, $mt) = minuteRange($time, false);
		$ms = date('H:i', $time);

		$score = 0;
		$previous_score = 0;

		foreach ($logs as $t => $activity) {
			if ( $activity['k'] > 0 || $activity['m'] > 0 ) {
				// For current slot
				if ( $t >= $mf ) {
					if ( $t <= $ms ) {
						$score++;
					}
				// For previous slot
				} else {
					$previous_score++;
				}
			}
		}	

		// Update score for previous slot
		if ( $previous_score ) {
			$previous_slot = HourlyLog::where('contract_id', $cid)
										->whereBetween('taken_at', minuteRange($time - 10 * 60))
										->first();

			if ( $previous_slot ) {
				$previous_score = $previous_slot->score + $previous_score;
				if ( $previous_score > 10 ) {
					$previous_score = 10;
				}

				$previous_slot->score = $previous_score;
				$previous_slot->save();
			}
		}

		return $score;
	}
}