<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Storage;
use Config;
use Session;
use DB;
use Log;

// Models
use iJobDesk\Models\Contract;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\HourlyReview;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Role;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\User;
use iJobDesk\Models\WalletHistory;

use iJobDesk\Models\Views\ViewUser;

class ReportController extends Controller {

	/**
	* Constructor
	*/
	public function __construct()
	{
		parent::__construct();
	}

	public function common_translactions() {
		return [
			'apply' => trans('common.apply'),
			'cancel' => trans('common.cancel'),
			'from' => trans('common.from'),
			'to' => trans('common.to'),
			'custom' => trans('common.custom'),
			'last_week' => trans('common.last_week'),
			'this_week' => trans('common.this_week'),
			'this_month' => trans('common.this_month'),
			'this_year' => trans('common.this_year'),
			'mon' => trans('common.weekdays_abbr2.1'),
			'tue' => trans('common.weekdays_abbr2.2'),
			'wed' => trans('common.weekdays_abbr2.3'),
			'thu' => trans('common.weekdays_abbr2.4'),
			'fri' => trans('common.weekdays_abbr2.5'),
			'sat' => trans('common.weekdays_abbr2.6'),
			'sun' => trans('common.weekdays_abbr2.7'),
			'jan' => trans('common.month_names.1'),
			'feb' => trans('common.month_names.2'),
			'mar' => trans('common.month_names.3'),
			'apr' => trans('common.month_names.4'),
			'may' => trans('common.month_names.5'),
			'jun' => trans('common.month_names.6'),
			'jul' => trans('common.month_names.7'),
			'aug' => trans('common.month_names.8'),
			'sep' => trans('common.month_names.9'),
			'oct' => trans('common.month_names.10'),
			'nov' => trans('common.month_names.11'),
			'dec' => trans('common.month_names.12'),
		];
	}

	public function index(Request $request) {
		$user = Auth::user();

		if ( $user->isFreelancer() ) {
			return redirect()->route('report.overview');
		} else if ( $user->isBuyer() ) {
			return redirect()->route('report.weekly_summary');
		}

		abort(404);
	}

	/**
	* Weekly Summary Page
	*
	* @author nada
	* @since Mar 21, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function weekly_summary(Request $request) {
		$user = Auth::user();

		$dates = [
			'from' => '',
			'to' => ''
		];

		try {

			$page_token = csrf_token();

			// Perform manually logged hours
			/*
			if ( $request->isMethod('post') && $request->input('_action') == 'allow_manual_time' && $request->input('_contract_id') ) {

				if ( !$request->input('_manual_time') ) {
					$contract = Contract::findOrFail($request->input('_contract_id'));

					if ( $contract->checkIsAuthor($user->id) ) {
						if ( HourlyReview::disAllowManualHours($contract->id) ) {
							add_message(trans('report.message_success_disallowed_manual_hours'), 'success');
						}
					}
				}
			}
			*/

			$from = $request->input('from');
			if ( strtotime($from) ) {
				list($dates['from'], $dates['to']) = weekRange($from, 'Y-m-d'/*, $this->user_timezone_name*/);
			} else {
				list($dates['from'], $dates['to']) = weekRange('now', 'Y-m-d'/*, $this->user_timezone_name*/);
			}

			// Transactions Data
			$total = [
				'mins' => 0, 
				'amount' => 0, 
				'others' => 0,
				'manual' => 0,
			];

			$timesheets = $contracts_amount = [];

			$logMapData = HourlyLogMap::getUserLogMap([
				'buyer_id' => $user->id,
				'from' => $dates['from'],
				'to' => $dates['to']
			]);

			foreach ($logMapData as $d) {
				$day = date_format(date_create($d->date), 'N');
				$timesheets[$d->contract_id]['week'][$day] = $d;
				$timesheets[$d->contract_id]['contract'] = $d->contract;
				
				if ( !isset($timesheets[$d->contract_id]['total_manual']) )
					$timesheets[$d->contract_id]['total_manual'] = 0;

				$mins = $d->mins;
				$amount = $d->contract->buyerPrice($mins);

	        	if ( !isset($timesheets[$d->contract_id]['mins']) )
	                $timesheets[$d->contract_id]['mins'] = 0;

	           	if ( !isset($timesheets[$d->contract_id]['amount']) )
	                $timesheets[$d->contract_id]['amount'] = 0;

	            $timesheets[$d->contract_id]['mins'] += $mins;
	            $timesheets[$d->contract_id]['amount'] += $amount;

				$timesheets[$d->contract_id]['json'] = json_encode([
					'id' => $d->contract->id,
					'title' => $d->contract->title,
					'url' => _route('contract.contract_view', [
						'id' => $d->contract->id
					]),
					'contractor' => $d->contract->contractor->fullname(),
					'token' => $page_token
				]);

	            $manual_hours = 0;
	            $acts = json_decode($d->act, true);
	            if ( $acts ) {
	            	foreach ( $acts as $a ) {
	            		if ( $a['m'] && (!isset($a['allow_manual']) || !$a['allow_manual']) ) {
	            			$timesheets[$d->contract_id]['total_manual'] += $a['m'];

	            			$manual_hours += $a['m'];
	            			$total['manual'] += $a['m'];
	            		}
	            	}
	            }

	            $timesheets[$d->contract_id]['week_manual'][$day] = $manual_hours;
				$timesheets[$d->contract_id]['manual_time_allowed'] = $d->contract->isAllowedManualTime() ? 1 : 0;

				$total['mins'] += $mins;
				$total['amount'] += $amount;

	        	if ( !isset($contracts_amount[$d->contract_id]) )
	                $contracts_amount[$d->contract_id] = [];

	            if ( !isset($contracts_amount[$d->contract_id]['amount']) )
	                $contracts_amount[$d->contract_id]['amount'] = 0;

	            $contracts_amount[$d->contract_id]['amount'] += $amount;
	            $contracts_amount[$d->contract_id]['title'] = $d->contract->title;
			}

			$opened_contracts = Contract::getOpenedContracts([
				'buyer_id' => $user->id, 
				'type' => Project::TYPE_HOURLY,
				'from' => $dates['from'], 
				'to' => $dates['to'], 
				'user_timezone_offset' => $this->user_timezone_offset,
				'server_timezone_offset' => $this->server_timezone_offset,
			]);

			foreach ($opened_contracts as $c) {
				if ( !isset($timesheets[$c->id]) ) {
					$timesheets[$c->id] = [
						'week'      => [], 
						'contract'  => $c, 
						'mins'      => 0, 
						'amount'    => 0,
						'manual_time_allowed' => false,
						'total_manual' => 0,
					];
				}
			}

			$other_payments = TransactionLocal::getUserOtherTransactions([
				'buyer_id' => $user->id,
				'from' => $dates['from'],
				'to' => $dates['to'],
				'server_timezone_offset' => $this->server_timezone_offset,
				'user_timezone_offset' => $this->user_timezone_offset
			]);

			foreach ($other_payments as $t) {
				$total['others'] += $t->amount;

	            if ( !isset($contracts_amount[$t->contract_id]) )
	                $contracts_amount[$t->contract_id] = [];

	            if ( !isset($contracts_amount[$t->contract_id]['amount']) )
	                $contracts_amount[$t->contract_id]['amount'] = 0;

	            $contracts_amount[$t->contract_id]['amount'] += (-$t->amount) < 0 ? 0 : -$t->amount;
	            $contracts_amount[$t->contract_id]['title'] = $t->contract ? $t->contract->title : '';
			}

			// Check if period is week
			$periodUnit = 'week';
			$prev = $next = '';
			if ( $periodUnit ) {
				$p_first = strtotime("-1 {$periodUnit}", strtotime($dates['from']));
				$range = call_user_func($periodUnit."Range", $p_first);
				$prev = date('Y-m-d', strtotime($range[0]));

				$n_first = strtotime("+1 {$periodUnit}", strtotime($dates['from']));
				$range = call_user_func($periodUnit."Range", $n_first);
				if ( $range[0] > date('Y-m-d H:i:s') ) {
		    		// Disabled Next
				} else {
					$next = date('Y-m-d', strtotime($range[0]));
				}
			}
		} catch ( Exception $e ) {
			Log::error('[ReportController::weekly_summamry()] ' . $e->getMessage());
		}

		// Title of the week
		$mode = 'past';
		$week_title = trans('common.past_week');
		list($from, $to) = weekRange('now', 'Y-m-d');
		list($last_week_from, $last_week_to) = weekRange('-1 weeks', 'Y-m-d');

		$is_in_review = isInReview();

		if ( $dates['from'] == $from ) {
			$week_title = trans('common.current_week_in_progress');
			$mode = 'current';
		} else if ( $dates['from'] <= $from ) {
			if ( $dates['from'] == $last_week_from ) {
				if ( $is_in_review ) {
					$week_title = trans('common.last_week_in_review');
				} else {
					$week_title = trans('common.last_week');
				}

				$mode = 'last';
			}
		} else {
			$week_title = '';
		}

		// Get the last updated
		$last_updated = HourlyLogMap::getLastUpdated();

		return view('pages.buyer.report.weekly_summary', [
			'page'        => 'buyer.report.weekly_summary',
			'dates'       => $dates,
			'prev'  => $prev, 
			'next'  => $next, 

			'last_week_from' => $last_week_from,
			'is_in_review' => $is_in_review,

			'week_title' => $week_title,
			'mode' => $mode,
			'last_updated' => $last_updated,
			'total'       => $total, 
			'timesheets'  => $timesheets, 
			'others'      => $other_payments,
			'contracts_amount' => $contracts_amount,

			'j_trans' => $this->common_translactions(),
		]);
	} 

	/**
	* Transactions Page
	*
	* @author Ro Un Nam
	* @since Aug 23, 2017
	* @param  Request $request
	* @return Response
	*/
	public function transactions(Request $request, $user_id = null, $global = false) {
		$user = Auth::user();
		if ( $user->isAdmin() ) {
			if ( !empty($user_id) )
				$user = User::find($user_id);
			else
				$user = null;
		}

		$contract_id = 0;
		$type = '';

		list($from, $to) = weekRange('now', 'Y-m-d'/*, $this->user_timezone_name*/);

		$dates = [
			'from' => $from . ' 00:00:00',
			'to' => $to . ' 23:59:59'
		];

		if ( $request->isMethod('post') ) {

	  		// Flash data to the session.
			$request->flashOnly(
				'date_range', 
				'transaction_type', 
				'contract_selector'
			);

			list($from, $to) = parseDateRange($request->input('date_range'));
			$dates['from'] = $from . ' 00:00:00';
			$dates['to'] = $to . ' 23:59:59';			

			$contract_id = $request->input('contract_selector');

			$type = $request->input('transaction_type');
		}

		// Get Transaction Data
		$begining_balance = $current_balance = 0;
		if ( $user ) {
			$wallet_history = WalletHistory::where('user_id', $user->id)
											->whereRaw("CONVERT_TZ(created_at, '" . $this->server_timezone_offset . "', '" . $this->user_timezone_offset . "') <= '" . $dates['to'] . "'")
											->orderBy('created_at', 'desc')
											->orderBy('id', 'desc')
											->first();

			if ( $wallet_history ) {
				$current_balance = $wallet_history->balance;
			}
			
			$wallet_history = WalletHistory::where('user_id', $user->id)
											->whereRaw("CONVERT_TZ(created_at, '" . $this->server_timezone_offset . "', '" . $this->user_timezone_offset . "') >= '" . $dates['from'] . "'")
											->orderBy('created_at', 'asc')
											->first();

			if ( $wallet_history ) {
				$begining_balance = $wallet_history->balance;
			}
		}

		// Get all transactions
		$params = [
			'from' => $dates['from'],
			'to' => $dates['to'],
			'server_timezone_offset' => $this->server_timezone_offset,
			'user_timezone_offset' => $this->user_timezone_offset,
		];

		if ( $user ) {
			$params['user'] = $user;
		}

		if ( $contract_id ) {
			$params['contract_id'] = $contract_id;
		}

		if ( $type ) {
			$params['type'] = $type;
		}

		$transactions = TransactionLocal::getTransactions($params);

		if ( $user ) {
			$_s = TransactionLocal::getStatement([
				'user' => $user, 
				'balance' => $begining_balance,
				'from'    => $dates['from'],
				'to'      => $dates['to'],
				'type' => $type,
				'contract_id' => $contract_id,
				'admin' => Auth::user()->isAdmin() ? true : false,
			]);

			$statement = [
				'beginning' => $_s['beginning'],
				'debits'   => $_s['out'], 
				'credits'  => $_s['in'], 
				'change'   => $_s['change'], 
				'ending'   => $_s['ending']
			];	
		} else {
			$statement = [
				'beginning' => null,
				'debits'   => null, 
				'credits'  => null, 
				'change'   => null, 
				'ending'   => null
			];	
		}		

		// Contract Selector
		$params_contracts = [
			'status' => [
			 	Contract::STATUS_OPEN,
			 	Contract::STATUS_PAUSED,
			 	Contract::STATUS_SUSPENDED,
			 	Contract::STATUS_CLOSED
			],
			'orderby' => 'title'
		];

		if ( $user->isBuyer() ) {
			$params_contracts['buyer_id'] = $user->id;
		} else if ( $user->isFreelancer() ) {
			$params_contracts['contractor_id'] = $user->id;
		}

		$contracts = Contract::getOpenedContracts($params_contracts);

		// Check if period is week or month or year
		$periodUnit = getPeriodUnit($dates['from'], $dates['to'], 'Y-m-d H:i:s');
		$prev = $next = '';
		if ($periodUnit) {
			$p_first = strtotime("-1 {$periodUnit}", strtotime($dates['from']));
			$range = call_user_func($periodUnit."Range", $p_first);
			$prev = date('M d, Y', strtotime($range[0])) . ' - ' . date('M d, Y', strtotime($range[1]));

			$n_first = strtotime("+1 {$periodUnit}", strtotime($dates['from']));
			$range = call_user_func($periodUnit."Range", $n_first);

			if ( $range[0] > date('Y-m-d H:i:s') ) {
	    		// Disabled Next
			} else {
				$next = date('M d, Y', strtotime($range[0])) . ' - ' . date('M d, Y', strtotime($range[1]));
			}
		}

		if ( $user && !$global ) {
			$role_label = 'freelancer';
			if ($user->isBuyer())
				$role_label = 'buyer';

			$page_blade = 'pages.'.$role_label.'.report.transactions';
			$page_id = (Auth::user()->isAdmin()?'super.user.'.$role_label.'.transactions':$role_label.'.report.transactions');
		} else {
			$page_blade = 'pages.admin.super.payment.transactions';
			$page_id = 'super.payment.transactions';
		}

		return view($page_blade, [
			'page'        => $page_id,
			'contracts'   => $contracts,
			'type'        => $type, 
			'dates'       => $dates,
			'transactions'=> $transactions, 
			'statement' => $statement, 
			'contract_id' => $contract_id,
			'prev' => $prev, 
			'next' => $next, 
			'user' => $user ? ViewUser::find($user->id) : null,
			'balance' => $statement['ending'],

			'j_trans' => $this->common_translactions(),
		]);
	}

	/**
	* Timesheet Page
	*
	* @author nada
	* @since Mar 21, 2016
	* @version 1.0
	* @author KCG
	* @since July 21, 2017
	* @version 2.0
	* @param  Request $request
	* @return Response
	*/
	public function timesheet(Request $request, $user_id = null) {
		$user = Auth::user();

		if ( $user_id && $user->isAdmin() ) {
			$user = User::find($user_id);
		}

		if ( empty($user) )
			abort(404);

		list($dates['from'], $dates['to']) = weekRange('now', 'Y-m-d'/*, $this->user_timezone_name*/);

		$mode = 'd';
		if ( $request->input('mode') ) {
			$mode = $request->input('mode');
		}

		if ( $request->input('from') && strtotime($request->input('from')) ) {
			$from = $request->input('from');
		}

		if ( $request->input('to') && strtotime($request->input('to')) ) {
			$to = $request->input('to');
		}

		if ( isset($from) ) {
			$dates['from'] = date('Y-m-d', strtotime($from));
		}

		if ( isset($to) ) {
			$dates['to'] = date('Y-m-d', strtotime($to));
		}

		$contract_id = 0;
		if ( $request->input('contract_id') ) {
			$contract_id = $request->input('contract_id');
		}

		// Report data
		$r_data = [];
		
		$total = [
			'mins' => 0,
			'amount' => 0,
		];

		$timesheets = HourlyLogMap::leftJoin('contracts', 'contract_id', '=', 'contracts.id')
									->where('mins', '>', 0);

		if ( $user->isBuyer() ) {
			$timesheets = $timesheets->where('contracts.buyer_id', $user->id);
		} else if ( $user->isFreelancer() ) {
			$timesheets = $timesheets->where('contracts.contractor_id', $user->id);
		}

		if ( $contract_id ) {
			$timesheets = $timesheets->where('contract_id', $contract_id);
		}

		if ( $mode == 'd' ) {
			$logData = $timesheets->where('date', '>=', $dates['from'])
								   ->where('date', '<=', $dates['to'])
								   ->select(['date', 'contract_id', 'mins', 'act'])
								   ->orderBy('date', 'asc')
								   ->get();

			foreach ( $logData as $d ) {
				$amount = $d->contract->buyerPrice($d->mins);

				$date = date('M d, Y', strtotime($d->date));
				$r_data[$date][] = [
					'user' => $user->isBuyer() ? $d->contract->contractor->fullname() : $d->contract->buyer->fullname(),
					'description' => $d->contract->title, 
					'mins' => $d->mins, 
					'amount' => $amount, 
				];

				$total['mins'] += $d->mins;
				$total['amount'] += $amount;
			}
		} else if ($mode == 'w') {
			$weeks = $this->getPeriodList($dates['from'], $dates['to'], $mode);

			foreach ($weeks as $week) {
				$w_from = $week[0];
				$w_to = $week[1];

				$queryBuilder = clone $timesheets;
				$logData = $queryBuilder->whereBetween('date', [$w_from, $w_to])
									 ->select('contract_id', 'act', DB::raw('SUM(mins) as mins'))
									 ->groupBy('contract_id')
									 ->orderBy('contract_id', 'asc')
									 ->get();

				foreach ( $logData as $d ) {
					$amount = $d->contract->buyerPrice($d->mins);

					$date = date('M d, Y', strtotime($w_from)) . ' ~ ' . date('M d, Y', strtotime($w_to));
					$r_data[$date][] = [
						'user' => $user->isBuyer() ? $d->contract->contractor->fullname() : $d->contract->buyer->fullname(),
						'description' => $d->contract->title, 
						'mins' => $d->mins, 
						'amount' => $amount, 
					];

					$total['mins'] += $d->mins;
					$total['amount'] += $amount;
	    		}

			}
		} else if ($mode == 'm') {
			$months = $this->getPeriodList($dates['from'], $dates['to'], $mode);

			foreach ($months as $month) {
				$m_from = $month[0];
				$m_to   = $month[1];

				$queryBuilder = clone $timesheets;
				$logData = $queryBuilder->whereBetween('date', [$m_from, $m_to])
									 ->select('contract_id', 'act', DB::raw('SUM(mins) as mins'))
									 ->groupBy('contract_id')
									 ->orderBy('contract_id', 'asc')
									 ->get();

				foreach ($logData as $d) {
					$amount = $d->contract->buyerPrice($d->mins);

					$date = date_format(date_create($m_to), 'M Y');
					$r_data[$date][] = [
						'user' => $user->isBuyer() ? $d->contract->contractor->fullname() : $d->contract->buyer->fullname(),
						'description' => $d->contract->title, 
						'mins' => $d->mins, 
						'amount' => $amount, 
					];

					$total['mins'] += $d->mins;
					$total['amount'] += $amount;
			    }
			}
		}

		// Contract Selector
		$params_contracts = [
			'type' => Project::TYPE_HOURLY,
			'status' => [
			 	Contract::STATUS_OPEN,
			 	Contract::STATUS_CLOSED
			],
			'orderby' => 'title'
		];

		if ( $user->isBuyer() ) {
			$params_contracts['buyer_id'] = $user->id;
		} else if ( $user->isFreelancer() ) {
			$params_contracts['contractor_id'] = $user->id;
		}

		$contracts = Contract::getOpenedContracts($params_contracts);

		// Get the last updated
		$last_updated = HourlyLogMap::getLastUpdated();

		return view('pages.report.timesheet', [
			'page' => (Auth::user()->isAdmin() ? 'super.user.buyer.timesheet' : 'report.timesheet'),
			'contracts' => $contracts,

			'from' => $dates['from'],
			'to' => $dates['to'],
			'dates' => $dates,

			'last_updated' => $last_updated,
			'r_data'=> $r_data, 
			'total' => $total,
			'mode' => $mode, 
			'contract_id' => $contract_id,

			'user' => ViewUser::find($user->id),

			'j_trans' => $this->common_translactions(),
		]);
	}

	/**
	* Overview Page
	* @author Ro Un Nam
	* @since Jun 07, 2017
	*/
	public function overview(Request $request) {
		$user = Auth::user();

		list($week_start, $week_end) = weekRange('now', 'Y-m-d'/*, $this->user_timezone_name*/);

		$week = [
			'from' => $week_start,
			'to' => $week_end
		];

		list($last_week_start, $last_week_end) = weekRange('-1 weeks', 'Y-m-d'/*, $this->user_timezone_name*/);

		$last_week = [
			'from' => $last_week_start,
			'to' => $last_week_end
		];

		/*********** Begin work in progress ***********/
		$total_work_in_progress = [
			'mins' => 0, 
			'amount' => 0,
			'manual_hours' => 0,
		];

		$timesheets = [];

		// Timesheets for this week
		$logMapData = HourlyLogMap::getUserLogMap([
			'contractor_id' => $user->id,
			'from' => $week_start,
			'to' => $week_end
		]);
		
		foreach ($logMapData as $d) {
			$day = date_format(date_create($d->date), 'N');
			$timesheets[$d->contract_id]['week'][$day] = $d;
			$timesheets[$d->contract_id]['contract'] = $d->contract;

			$mins = $d->mins;
			$amount = $d->contract->buyerPrice($mins);
			$fee_rate = $d->contract->feeRate();
			$fee_amount = $amount - $d->contract->freelancerPrice($mins);

        	if ( !isset($timesheets[$d->contract_id]['mins']) )
                $timesheets[$d->contract_id]['mins'] = 0;

           	if ( !isset($timesheets[$d->contract_id]['amount']) )
                $timesheets[$d->contract_id]['amount'] = 0;

            if ( !isset($timesheets[$d->contract_id]['fee_amount']) )
                $timesheets[$d->contract_id]['fee_amount'] = 0;

            $timesheets[$d->contract_id]['mins'] += $mins;
            $timesheets[$d->contract_id]['amount'] += $amount;
			$timesheets[$d->contract_id]['fee_rate'] = $fee_rate;
			$timesheets[$d->contract_id]['fee_amount'] += $fee_amount;

			if ( !isset($timesheets[$d->contract_id]['total_manual']) )
				$timesheets[$d->contract_id]['total_manual'] = 0;

            $manual_hours = 0;
            $acts = json_decode($d->act, true);
            if ( $acts ) {
            	foreach ( $acts as $a ) {
            		if ( $a['m'] ) {
            			$timesheets[$d->contract_id]['total_manual'] += $a['m'];

            			$manual_hours += $a['m'];
            			$total_work_in_progress['manual_hours'] += $a['m'];
            		}
            	}
            }

            $timesheets[$d->contract_id]['week_manual'][$day] = $manual_hours;

			$total_work_in_progress['mins'] += $mins;
			$total_work_in_progress['amount'] += $amount;
		}

		$opened_contracts = Contract::getOpenedContracts([
			'contractor_id' => $user->id, 
			'type' => Project::TYPE_HOURLY,
			'from' => $week['from'], 
			'to' => $week['to'], 
			'user_timezone_offset' => $this->user_timezone_offset,
			'server_timezone_offset' => $this->server_timezone_offset,
		]);

		foreach ($opened_contracts as $c) {
			if ( !isset($timesheets[$c->id]) ) {
				$timesheets[$c->id] = [
					'week'      => [], 
					'contract'  => $c,
					'rate'      => $c->price,
					'fee_rate'   => $c->feeRate(),
					'mins'      => 0, 
					'amount'    => 0,
					'fee_amount' => 0,
					'week_manual' => [],
					'total_manual' => 0,
				];
			}
		}   

		// Fixed contract milestones
		$fixed_contracts = Contract::getContracts([
			'contractor_id' => $user->id,
			'type' => Contract::TYPE_FIXED,
			'status' => Contract::STATUS_OPEN,
		]);

		$total_fixed_milestones = 0;
		try {
			if ( count($fixed_contracts) ) {
				foreach ( $fixed_contracts as $c ) {
					foreach ( $c->milestones as $m ) {
						if ( $m->isFunded() && $m->transaction ) {
							$total_fixed_milestones += $m->getPrice();
						}
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[ReportController::overview()] ' . $e->getMessage());
		}
		/*********** End work in progress ***********/

		/*********** Begin in review ***********/
		$hourly_in_review = HourlyReview::getHourlyReviews([
			'contractor_id' => $user->id,
			'from' => $last_week_start,
			'to' => $last_week_end,
		]);
		$total_in_review = HourlyReview::getTotalHourlyReviewAmount([
			'contractor_id' => $user->id,
			'from' => $last_week_start,
			'to' => $last_week_end,
		]);
		/*********** End in review ***********/

		/*********** Begin pending ***********/
		$last2_week_transactions = TransactionLocal::where(function($query) use ($user) {
												$query->where('user_id', $user->id)
													->where('status', TransactionLocal::STATUS_AVAILABLE)
													->where('for', TransactionLocal::FOR_FREELANCER)
													->where('type', TransactionLocal::TYPE_HOURLY);
											})
											->orWhere(function($query) use ($user) {
												$query->where('user_id', $user->id)
													->where('status', TransactionLocal::STATUS_AVAILABLE)
													->where('for', TransactionLocal::FOR_FREELANCER)
													->where('type', '<>', TransactionLocal::TYPE_HOURLY)
													->where('type', '<>', TransactionLocal::TYPE_WITHDRAWAL);
											})
											->orderBy('id', 'desc')
											->get();

		$total_pending = 0;
		if ( count($last2_week_transactions) ) {
			foreach ( $last2_week_transactions as $t ) {
				if ( in_array($t->type, [
						TransactionLocal::TYPE_AFFILIATE,
						TransactionLocal::TYPE_AFFILIATE_CHILD
					]) ) {
					$total_pending += $t->amount;
				} else {
					if ( $t->reference )
						$total_pending += abs($t->reference->amount);
					else
						$total_pending += $t->amount;
				}
			}
		}
		/*********** End pending ***********/

		/*********** Begin available ***********/
		$last_30_days_date = date_format( date_add(date_create(), date_interval_create_from_date_string('-30 days')), 'Y-m-d 00:00:00' );

		$recent_transactions = TransactionLocal::where(function($query) use ($user, $last_30_days_date) {
												$query->where('user_id', $user->id)
														->where('status', TransactionLocal::STATUS_DONE)
														->where('for', TransactionLocal::FOR_FREELANCER)
														->where('type', '<>', TransactionLocal::TYPE_WITHDRAWAL)
														->where('created_at', '>=', $last_30_days_date);
											})
											->orWhere(function($query) use ($user, $last_30_days_date) {
												$query->where('ref_user_id', $user->id)
														->where('status', TransactionLocal::STATUS_DONE)
														->where('for', TransactionLocal::FOR_IJOBDESK)
														->whereNotIn('type', [
															TransactionLocal::TYPE_WITHDRAWAL,
															TransactionLocal::TYPE_AFFILIATE,
															TransactionLocal::TYPE_AFFILIATE_CHILD
														])
														->where('created_at', '>=', $last_30_days_date);
											})
											->orWhere(function($query) use ($user, $last_30_days_date) {
												$query->where('user_id', $user->id)
														->where('user_id', '<>', SUPERADMIN_ID)
														->whereIn('status', [
															TransactionLocal::STATUS_AVAILABLE,
															TransactionLocal::STATUS_DONE
														])														
														->where('type', TransactionLocal::TYPE_WITHDRAWAL)
														->where('created_at', '>=', $last_30_days_date);
											})
											->orderBy('id', 'desc')
											->get();
		/*********** End available ***********/

		// Last payment aount
		$total_last_payment_amount = TransactionLocal::lastWithdrawalAmount($user->id);

		return view('pages.freelancer.report.overview', [
			'page' => 'freelancer.report.overview',
			'week' => $week,
			'last_week' => $last_week,
			'total_work_in_progress' => $total_work_in_progress,
			'total_in_review' => $total_in_review,
			'total_pending' => $total_pending,
			'total_last_payment_amount' => $total_last_payment_amount,
			'timesheets' => $timesheets,
			'fixed_contracts' => $fixed_contracts,
			'total_fixed_milestones' => $total_fixed_milestones,
			'hourly_in_review' => $hourly_in_review,
			'last2_week_transactions' => $last2_week_transactions,
			'recent_transactions' => $recent_transactions,
			'balance' => $user->myBalance(),
		]);
	}

	/**
	* Timelogs
	*
	* @author Ro Un Nam
	* @since Jun 09, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function timelogs(Request $request){
		$user = Auth::user();

		$dates = [
			'from' => '',
			'to' => '',
		];

		$from = $request->input('from');
		if ( strtotime($from) ) {
			list($dates['from'], $dates['to']) = weekRange($from, 'Y-m-d'/*, $this->user_timezone_name*/);
		} else {
			list($dates['from'], $dates['to']) = weekRange('now', 'Y-m-d'/*, $this->user_timezone_name*/);
		}

		$timesheets = [];

		$total = [
			'mins' => 0,
			'amount' => 0,
			'week' => [],
		];

		// Timesheets
		$logMapData = HourlyLogMap::getUserLogMap([
			'contractor_id' => $user->id,
			'from' => $dates['from'],
			'to' => $dates['to']
		]);

		foreach ($logMapData as $d) {
			$day = date_format(date_create($d->date), 'N');
			$timesheets[$d->contract_id]['week'][$day] = $d;
			$timesheets[$d->contract_id]['contract'] = $d->contract;
		}		

		foreach ($timesheets as $contract_id => &$c_ts) {
			$c_ts['client'] = $c_ts['contract']->buyer->fullname();
			$c_ts['contract_title'] = $c_ts['contract']->title;
			$c_ts['mins'] = 0;
			foreach ($c_ts['week'] as $w => $c) {
				$c_ts['mins'] += $c->mins;
				
				if ( !isset($total['week'][$w]) ) {
					$total['week'][$w] = 0;
				}

				$total['week'][$w] += $c->mins;
			}
			$c_ts['amount'] = $c_ts['contract']->buyerPrice($c_ts['mins']);
			$c_ts['rate'] = $c_ts['contract']->price;

			$total['mins'] += $c_ts['mins'];
			$total['amount'] += $c_ts['amount'];
		}

		// Show the contracts having empty timelogs
		$opened_contracts = Contract::getOpenedContracts([
			'contractor_id' => $user->id, 
			'type' => Project::TYPE_HOURLY,
			'from' => $dates['from'], 
			'to' => $dates['to'], 
			'user_timezone_offset' => $this->user_timezone_offset,
			'server_timezone_offset' => $this->server_timezone_offset,
		]);

		foreach ($opened_contracts as $c) {
			if ( !isset($timesheets[$c->id]) ) {
				$timesheets[$c->id] = [
					'week' => [], 
					'client' => $c->buyer->fullname(), 
					'contract_title' => $c->title,
					'mins' => 0, 
					'amount' => 0,
					'rate' => 0,
				];
			}
		}

		// Fixed-Price and Others Payment
		$other_payments = TransactionLocal::getUserOtherTransactions([
			'contractor_id' => $user->id,
			'created_from' => $dates['from'],
			'created_to' => $dates['to'],
			'server_timezone_offset' => $this->server_timezone_offset,
			'user_timezone_offset' => $this->user_timezone_offset
		]);

		$total_other_payments = 0;
		if ( count($other_payments) ) {
			foreach ( $other_payments as $t ) {
				if ( $t->type == TransactionLocal::TYPE_REFUND ) {
					$total_other_payments += -($t->reference->amount);
				} else {
					$total_other_payments += abs($t->reference->amount);
				}
			}
		}

		// Check if period is week
		$periodUnit = 'week';
		$prev = $next = '';
		if ($periodUnit) {
			$p_first = strtotime("-1 {$periodUnit}", strtotime($dates['from']));
			$range = call_user_func($periodUnit . 'Range', $p_first);
			$prev = date('Y-m-d', strtotime($range[0]));

			$n_first = strtotime("+1 {$periodUnit}", strtotime($dates['from']));
			$range = call_user_func($periodUnit . 'Range', $n_first);
			if ( $range[0] > date('Y-m-d H:i:s') ) {
	    		// Disabled Next
			} else {
				$next = date('Y-m-d', strtotime($range[0]));
			}
		}

		return view('pages.freelancer.report.timelogs', [
			'page'        => 'freelancer.report.timelogs',
			
			'from' => $dates['from'],
			'to' => $dates['to'],

			'prev'        => $prev,
			'next'        => $next,
			'timesheets'  => $timesheets,
			'total' => $total,
			'other_payments' => $other_payments,
			'total_other_payments' => $total_other_payments,

			'j_trans' => $this->common_translactions(),
		]);
	}

	/**
	* Get Period Lists between two dates ($from, $to)
	*
	* @author nada
	* @since Mar 21, 2016
	* @version 1.0
	* @param  $from, $to : Range of Date.
	*         $mode : week - in Week, month - in Month
	* @return Response
	*         List of Period
	*
	* getPeriodList("2016-03-23", "2016-03-29", "w")
	*    return: array(0=>[2016-03-21, 2016-03-27], 
	*                  1=>[2016-03-28, 2016-04-02])
	*/
	protected function getPeriodList($from, $to, $mode) {
		$list = array();

		if ($mode == 'w') {
			$from_week = weekRange($from);
			$to_week   = weekRange($to);

			$list[] = $from_week;
			$next_week = $from_week;

			while ($next_week[1] < $to_week[1]) {
				$next_week = weekRange(strtotime("+4 days", strtotime($next_week[1])));
				$list[] = $next_week;
			}
		}
		else if ($mode == 'm') {
			$from_month = monthRange($from);
			$to_month   = monthRange($to);

			$list[] = $from_month;
			$next_month = $from_month;

			while ($next_month[1] < $to_month[1]) {
				$next_month = monthRange(strtotime("+15 days", strtotime($next_month[1])));
				$list[] = $next_month;
			}
		}

		return $list;
	}
}