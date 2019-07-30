<?php namespace iJobDesk\Http\Controllers\Admin\Super\Payment;
/**
 * @author KCG
 * @since July 23, 2017
 * Transactions on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Config;
use Log;

use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Views\ViewProjectMessage;
use iJobDesk\Models\User;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\WalletHistory;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\SiteWalletHistory;

class TransactionController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Transactions';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Transactions');

        if ( $request->input('action') == 'search_user' ) {
            return $this->users($request);
        }

		$view = $request->input('view') ? $request->input('view') : '';
		
		$date_range = $request->input('date_range') ? $request->input('date_range') : '';
		if ( $date_range ) {
			list($from, $to) = parseDateRange($date_range);
		} else {
			list($from, $to) = weekRange();
		}

		$from = $from . ' 00:00:00';
		$to = $to . ' 23:59:59';

		$life_time = $request->input('view') == 'all' ? 1 : 0;
		
		$type = $request->input('transaction_type') ? $request->input('transaction_type') : '';
		
		$user_id = $request->input('user_id') ? $request->input('user_id') : '';

		$per_page = $request->input('view_by') ? $request->input('view_by') : 20;

		$user = null;
		if ( $user_id ) {
			$user = ViewUser::find($user_id);
		}

		switch ($view) {
			case 'today' :
				$from = date('Y-m-d 00:00:00');
				$to = date('Y-m-d 23:59:59');

				break;
			case 'month' :
				$month = monthRange();
				$from = $month[0];
				$to = $month[1];

				break;
			case 'all':
				$from = TransactionLocal::orderBy('created_at', 'asc')->min('created_at');
				$to = TransactionLocal::orderBy('created_at', 'desc')->max('created_at');

				break;
			case 'escrow':
				$for = TransactionLocal::FOR_BUYER;
				$milestone_id = 1;

				break;
			case 'pending':
				$status = TransactionLocal::STATUS_PENDING;

				break;
			case 'withdraw':
				$type = TransactionLocal::TYPE_WITHDRAWAL;

				break;
			case 'earning':
				$type = TransactionLocal::TYPE_IJOBDESK_EARNING;

				break;

			default:
				break;
		}

		$dates['from'] = date('M j, Y', strtotime($from));
		$dates['to'] = date('M j, Y', strtotime($to));

		if ( $type != TransactionLocal::TYPE_IJOBDESK_EARNING ) {
			$transactions = TransactionLocal::where(function($query) use ($user_id, $user) {
				$query->where(function($query2) use ($user_id, $user) {
							$query2->whereIn('type', [
										TransactionLocal::TYPE_FIXED,
										TransactionLocal::TYPE_HOURLY,
										TransactionLocal::TYPE_BONUS,
										TransactionLocal::TYPE_REFUND,
									])
									->where(function($query3) use ($user_id, $user) {
										if ( $user_id && $user->isBuyer() ) {
											$query3->where('user_id', $user_id);
										} else {
											$query3->where('for', '<>', TransactionLocal::FOR_BUYER);
										}
									});
						})
						->orWhere(function($query2) use ($user_id) {
							$query2->where('type', TransactionLocal::TYPE_FEATURED_JOB)
									->where('for', TransactionLocal::FOR_BUYER);
							if ( $user_id ) {
								$query2->where('user_id', $user_id);
							}
						})
						->orWhere(function($query2) use ($user_id) {
							$query2->where('type', TransactionLocal::TYPE_CHARGE);
							if ( $user_id ) {
								$query2->where('user_id', $user_id);
							}
						})
						->orWhere(function($query2) use ($user_id) {
							$query2->where('type', TransactionLocal::TYPE_WITHDRAWAL)
									->where(function($query3) use ($user_id) {
										if ( $user_id ) {
											$query3->where('user_id', $user_id);
										} else {
											$query3->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
													->orWhere('user_id', SUPERADMIN_ID);
										}
									});
						})
						->orWhere(function($query2) {
							$query2->whereIn('type', [
									TransactionLocal::TYPE_AFFILIATE,
									TransactionLocal::TYPE_AFFILIATE_CHILD,
								])
								->where('for', '<>', TransactionLocal::FOR_IJOBDESK);
						});
			});
		} else {
			$transactions = TransactionLocal::where('for', TransactionLocal::FOR_IJOBDESK)
											->where('user_id', SUPERADMIN_ID);
		}

		if ( $life_time ) {
			$from = $to = '';
		}

		if ( $from != '' ) {
			$transactions = $transactions->where('created_at', '>=', $from);
		}

		if ( $to != '' ) {
			$transactions = $transactions->where('created_at', '<=', $to);
		}

		if ( $type != TransactionLocal::TYPE_IJOBDESK_EARNING ) {
			if ( $type ) {
				// Affiliate
				if ( $type == TransactionLocal::TYPE_AFFILIATE ) {
					$transactions = $transactions->whereIn('type', [
						TransactionLocal::TYPE_AFFILIATE,
						TransactionLocal::TYPE_AFFILIATE_CHILD
					]);
				} else {
					$transactions = $transactions->where('type', $type);
				}
			}

			if ( isset($for) ) {
				$transactions = $transactions->where('for', $for);
			}

			if ( isset($milestone_id) ) {
				$transactions = $transactions->where('milestone_id', '<>', 0);
			}

			if ( $user_id != '' ) {
				$transactions = $transactions->where(function($query) use ($user_id) {
					$query->where('user_id', $user_id)
						  ->orWhere('ref_user_id', $user_id);
				});
			}
		} else {
			if ( $user_id != '' ) {
				$transactions = $transactions->where('ref_user_id', $user_id);
			}
		}

		if ( isset($status) ) {
			$transactions = $transactions->where('status', $status);
		}

		$transactions = $transactions->orderBy('id', 'desc')
									->orderBy('created_at', 'desc')
									->orderBy('done_at', 'desc')
									->paginate($per_page);

		// Check if period is week or month or year
		$periodUnit = getPeriodUnit($from, $to, 'Y-m-d H:i:s');

		$prev = $next = '';
		if ( $periodUnit ) {
			$p_first = strtotime("-1 {$periodUnit}", strtotime($from));
			$range = call_user_func($periodUnit."Range", $p_first);
			$prev = date('M d, Y', strtotime($range[0])) . ' - ' . date('M d, Y', strtotime($range[1]));

			$n_first = strtotime("+1 {$periodUnit}", strtotime($from));

			$range = call_user_func($periodUnit."Range", $n_first);

			if ( $range[0] > date('Y-m-d H:i:s') ) {
	    	// Disabled Next
			} else {
				$next = date('M d, Y', strtotime($range[0])) . ' - ' . date('M d, Y', strtotime($range[1]));
			}
		}

		// Get Statement
		$begining_balance= 0;
		
		try {
			if ( $type != TransactionLocal::TYPE_IJOBDESK_EARNING ) {
				if ( $user_id ) {
					$user = ViewUser::find($user_id);
					$balance = $user->myBalance();
					
					$wallet_history = WalletHistory::where('user_id', $user->id)
													->where('date', '>=', $from)
													->orderBy('updated_at', 'asc')
													->first();

					if ( $wallet_history ) {
						$begining_balance = $wallet_history->balance;
					}

					$_s = TransactionLocal::getStatement([
						'user' => $user, 
						'balance' => $begining_balance,
						'from' => $from,
						'to' => $to,
						'type' => $type,
					]);
				} else {
					$balance = SiteWallet::holding()->amount;
					
					$wallet_history = SiteWalletHistory::where('type', SiteWalletHistory::TYPE_HOLDING)
														->where('date', '>=', $from)
														->orderBy('updated_at', 'asc')
														->first();

					if ( $wallet_history ) {
						$begining_balance = $wallet_history->balance;
					}

					$_s = TransactionLocal::getHoldingStatement([
						'balance' => $begining_balance,
						'from' => $from,
						'to' => $to,
						'type' => $type,
					]);
				}
			} else {
				$balance = SiteWallet::earning()->amount;

				$wallet_history = SiteWalletHistory::where('type', SiteWalletHistory::TYPE_EARNING)
													->where('date', '>=', $from)
													->orderBy('updated_at', 'asc')
													->first();

				if ( $wallet_history ) {
					$begining_balance = $wallet_history->balance;
				}

		    	$_s = TransactionLocal::getEarningStatement([
					'balance' => $begining_balance,
					'from' => $from,
					'to' => $to,
					'user_id' => $user_id,
				]);
			}
		} catch ( Exception $e ) {
			Log::error('[Admin - TransactionController.php::index()] Error: ' . $e->getMessage());
		}

		$statement = [
			'beginning' => $_s['beginning'],
			'debits'   => $_s['out'], 
			'credits'  => $_s['in'], 
			'change'   => $_s['change'], 
			'ending'   => $_s['ending']
		];

		return view('pages.admin.super.payment.transactions', [
			'page' => 'super.payment.transactions',
			'transactions' => $transactions,
			'life_time' => $life_time,
			'type' => $type,
			'user_id' => $user_id, 
			'user' => $user, 
			'view_by' => $per_page,
			'dates' => $dates,
			'prev' => $prev, 
			'next' => $next,
			'view' => $view,
			'statement' => $statement,
			'balance' => $statement['ending'],
        ]);
    }

    private function users(Request $request) {
        $term = $request->input('term');
        $id = $request->input('id');

        if ( empty($term) && !empty($id) ) {
            return response()->json(ViewUser::findOrFail($id));
        }

        $users = ViewUser::where(function($query) use($term) {
        						if ( is_numeric($term) ) {
					        		$query->where('id', intval($term));
					        	} else {
					                $query->orWhereRaw('LOWER(fullname) LIKE "%' . trim(strtolower($term)) . '%"')
					                 		->orWhereRaw('LOWER(username) LIKE "%' . trim(strtolower($term)) . '%"');
					            }
					        })
					        ->whereIn('role', [
					        	User::ROLE_USER_FREELANCER,
					        	User::ROLE_USER_BUYER
					        ])
					        ->get();

        return response()->json(['users' => $users]);
    }
}