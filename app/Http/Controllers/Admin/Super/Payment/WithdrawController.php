<?php namespace iJobDesk\Http\Controllers\Admin\Super\Payment;
/**
 * @author PYH
 * @since July 28, 2017
 * Withdraw Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Config;
use Auth;
use Log;

use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\PaymentGateway;
use iJobDesk\Models\Notification;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\WalletHistory;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\SiteWalletHistory;
use iJobDesk\Models\UserDeposit;

class WithdrawController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Withdraw';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        $current_user = Auth::user();

        add_breadcrumb('Payments', route('admin.super.payment.transactions'));
        add_breadcrumb('Withdraw');

        $currency_sign = Settings::get('CURRENCY_SIGN');

        // Get the payment gateway list
        $paymentGateways = PaymentGateway::getActiveGateways();

        // Get financial managers
        $financial_managers = User::getAdminUsers([
            User::ROLE_USER_FINANCIAL_MANAGER
        ]);

        try {
	        if ($request->method('post')) {
	            $action = $request->input('_action');

                if ( $action ) {

    	            if ( $action == 'NOTIFY' ) {
                        $withdraw_ids = $request->input('ids');
                        $admin_user = User::where('id', $request->input('notify_manager'))
                                            ->where('role', User::ROLE_USER_FINANCIAL_MANAGER)
                                            ->first();

                        if ( is_string($withdraw_ids) ) {
                            $withdraw_ids = explode(',', $withdraw_ids);
                        }

    		            if ( $withdraw_ids && $admin_user ) {
                            // Get pending withdraws
                            $pending_withdraws = TransactionLocal::whereIn('id', $withdraw_ids)
                                                                ->where('status', TransactionLocal::STATUS_AVAILABLE)
                                                                ->where('notify_sent', 0)
                                                                ->get();

    						if ( count($pending_withdraws) ) {
    							$withdraws_string = '';
    							$amount = 0;

    							$withdraws_string = '<table width="100%" border="1" bordercolor="#ccc" cellspacing="0" cellpadding="5">';
    								$withdraws_string .= '<thead>';
    									$withdraws_string .= '<tr>';
    										$withdraws_string .= '<th width="20%">User</th>';
    										$withdraws_string .= '<th width="15%">Amount</th>';
    										$withdraws_string .= '<th>Gateway</th>';
    										$withdraws_string .= '<th width="15%">Date</th>';
    									$withdraws_string .= '</tr>';
    								$withdraws_string .= '</thead>';

    								$withdraws_string .= '<tbody>';
    								foreach ( $pending_withdraws as $t ) {
    									$t_data = $t->getArray();

    									$amount += abs($t->amount);

    									$withdraws_string .= '<tr>';
    										$withdraws_string .= '<td>' . $t->user->fullname() . '</td>';

    										if ( $t->userPaymentGateway->isWeixin() ) {
    											$withdraws_string .= '<td>CNY ' . formatCurrency($t_data['exchange_amount']) . '</td>';
    											$withdraws_string .= '<td>';
    											$withdraws_string .= $t->gateway_string();

    											if ( isset($t_data['user_payment_gateway_data']['file_url']) ) {
    												$withdraws_string .= '<br /><br /><img src="' . $t_data['user_payment_gateway_data']['file_url'] . '" width="150" height="150">';
    											}

    											$withdraws_string .= '</td>';
    										} else {
    											$withdraws_string .= '<td>' . $currency_sign . formatCurrency(abs($t->amount)) . '</td>';
    											$withdraws_string .= '<td>' . $t->gateway_string() . '</td>';
    										}
    										
    										$withdraws_string .= '<td>' . format_date('M d, Y g:i A', $t->created_at) . '</td>';
    									$withdraws_string .= '</tr>';

                                        $t->notify_sent = 1;
                                        $t->save();
    								}
    								$withdraws_string .= '</tbody>';
    							$withdraws_string .= '</table>';

								EmailTemplate::send($admin_user, 'SUPER_ADMIN_NOTIFY_WITHDRAWS', 0, [
									'USER' => $admin_user->fullname(),
									'WITHDRAWS' => $withdraws_string,
									'AMOUNT' => formatCurrency($amount),
								]);

    							add_message('We have just sent the pending withdrawal requests to the financial manager.', 'success');
    						}
    		            }
    		            
                    } else if ($action == 'CHANGE_STATUS') {
    	                $status = $request->input('withdraw_status');
    	                $withdraw_ids = $request->input('ids');

                        if ( is_string($withdraw_ids) ) {
                            $withdraw_ids = explode(',', $withdraw_ids);
                        }
    	                
    	                if ($status == TransactionLocal::STATUS_PROCEEDING) {
    	                	$now = date('Y-m-d H:i:s');

    	                	foreach ($withdraw_ids as $withdraw_id) {
                                $t = TransactionLocal::find($withdraw_id);
    	                		/*
                                $withdraw->status = TransactionLocal::STATUS_PROCEEDING;
    	                		$withdraw->save();

    	                		TransactionLocal::where('ref_id', $withdraw_id)
    	                               			->update(['status' => TransactionLocal::STATUS_PROCEEDING]);
                                */

                                $t->status = TransactionLocal::STATUS_DONE;
                                $t->done_at = $now;
                                
                                if ( $t->save() ) {
                                    $amount = abs($t->amount);
                                    
                                    // Update iJobDesk holding wallet history
                                    $holding = SiteWallet::holding();
                                    $newAmount = $holding->amount - $amount;
                                    $holding->amount = $newAmount;
                                    $holding->save();

                                    // Fee
                                    $fee_amount = 0;
                                    if ( $t->reference ) {
                                        $fee_amount = abs($t->reference->amount);
                                    }

                                    // Send notification and email
                                    Notification::send(
                                        Notification::USER_WITHDRAWAL, 
                                        SUPERADMIN_ID,
                                        $t->user_id, 
                                        [
                                            'amount' => formatCurrency($amount + $fee_amount)
                                        ]
                                    );

                                    EmailTemplate::send($t->user, 'WITHDRAW', 0, [
                                        'USER' => $t->user->fullname(),
                                        'AMOUNT' => formatCurrency($amount + $fee_amount),
                                        'PAYMENT_METHOD' => $t->gateway_string(),
                                        'CONTACT_US_URL' => route('frontend.contact_us'),
                                    ]);

                                    SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

                                    $fee = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
                                                            ->where('ref_id', $t->id)
                                                            ->where('user_id', SUPERADMIN_ID)
                                                            ->first();
                                    if ( $fee ) {
                                        // Update withdraw fee transaction
                                        TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
                                                        ->where('ref_id', $t->id)
                                                        ->update([
                                                            'status' => TransactionLocal::STATUS_DONE,
                                                            'done_at' => $now,
                                                        ]);

                                        if ( $fee->save() ) {
                                            // Update iJobDesk earning wallet history
                                            $earning = SiteWallet::earning();
                                            $newAmount = $earning->amount + $fee->amount;
                                            $earning->amount = $newAmount;
                                            $earning->save();

                                            SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $fee->id);
                                        }
                                    }
                                }
    	                	}

    	                	add_message(sprintf('The %d withdraws has been proceed.', count($withdraw_ids)), 'success');
    	                } elseif ($status == TransactionLocal::STATUS_SUSPENDED) {
    	                	foreach ($withdraw_ids as $withdraw_id) {
    	                		$withdraw = TransactionLocal::find($withdraw_id);
    	                		$withdraw->status = TransactionLocal::STATUS_SUSPENDED;
    	                		$withdraw->save();

    	                		TransactionLocal::where('ref_id', $withdraw_id)
    	                               			->update(['status' => TransactionLocal::STATUS_SUSPENDED]);
    	                	}

    	                    add_message(sprintf('The %d withdraws has been suspended.', count($withdraw_ids)), 'success');
    	                } elseif ($status == TransactionLocal::STATUS_CANCELLED) {
    	                    foreach ($withdraw_ids as $withdraw_id) {
    							$withdraw = TransactionLocal::find($withdraw_id);

                                if ( !$withdraw->isCancelled() ) {
        							$withdraw->status = TransactionLocal::STATUS_CANCELLED;
        							$withdraw->done_at = date('Y-m-d H:i:s');
        							$withdraw->save();

        							$cancelled_withdraw = $withdraw->replicate();
        	                        $cancelled_withdraw->old_id = $withdraw_id;
        							$cancelled_withdraw->status = TransactionLocal::STATUS_CANCELLED;
        							$cancelled_withdraw->amount = -$cancelled_withdraw->amount;
        							$cancelled_withdraw->save();

        	                        // Fee
        	                        $fee_amount = 0;
        	                        
        	                        $withdraw_fees = TransactionLocal::where('ref_id', $withdraw_id)->get();
        							if ( count($withdraw_fees) ) { 
        								foreach ( $withdraw_fees as $withdraw_fee ) {
        									$withdraw_fee->status = TransactionLocal::STATUS_CANCELLED;
        									$withdraw_fee->done_at = date('Y-m-d H:i:s');
        									$withdraw_fee->save();

        									$cancelled_withdraw_fee = $withdraw_fee->replicate();
        	                                $cancelled_withdraw_fee->old_id = $withdraw_fee->id;
        									$cancelled_withdraw_fee->status = TransactionLocal::STATUS_CANCELLED;
        									$cancelled_withdraw_fee->amount = -$cancelled_withdraw_fee->amount;
        									$cancelled_withdraw_fee->ref_id = $cancelled_withdraw->id;
        									$cancelled_withdraw_fee->save();

                                            // Fee amount
                                            if ( $withdraw_fee->for == TransactionLocal::FOR_IJOBDESK && $withdraw_fee->user_id == SUPERADMIN_ID ) {
                                                $fee_amount = $withdraw_fee->amount;
                                            }
        								}
        							}

        					  		// Update user wallet history
        							$wallet = Wallet::where('user_id', $withdraw->user_id)->first();
        							$newAmount = $wallet->amount + (-$withdraw->amount) + $fee_amount;
        							$wallet->amount = $newAmount;
        							$wallet->save();

                                    // Update amount in user_deposits table
                                    if ( $withdraw->user->isBuyer() ) {
                                        if ( $withdraw->userPaymentGateway->real_id ) {
                                            UserDeposit::updateAmount($withdraw->user_id, $withdraw->userPaymentGateway->gateway, $withdraw->userPaymentGateway->real_id, (-$withdraw->amount) + $fee_amount);
                                        }
                                    }

        							WalletHistory::addHistory($withdraw->user_id, $newAmount, $cancelled_withdraw->id);

        							EmailTemplate::send($withdraw->user, 'WITHDRAW_CANCELLED', 0, [
        								'USER' => $withdraw->user->fullname(),
        	                            'AMOUNT' => formatCurrency(-($withdraw->amount) + $fee_amount),
        	                            'PAYMENT_METHOD' => $withdraw->gateway_string(),
        	                            'CONTACT_US_URL' => route('frontend.contact_us'),
        							]);
                                }
    	                    }

    	                    add_message(sprintf('The %d withdraws has been cancelled.', count($withdraw_ids)), 'success');
    	                } elseif ($status == TransactionLocal::STATUS_AVAILABLE) {
    	                    TransactionLocal::whereIn('id', $withdraw_ids)
    	                               ->update(['status' => TransactionLocal::STATUS_AVAILABLE]);

    	                    add_message(sprintf('The %d withdraws has been pending.', count($withdraw_ids)), 'success');
    	                }
    	            }

                    if ( $current_user->isFinancial() )
                        return redirect()->route('admin.financial.withdraw');
                    else
                        return redirect()->route('admin.super.payment.withdraw');
                }
	        }
	    } catch ( Exception $e ) {
	    	Log::error('[Admin - WithdrawController.php::index()] Error: ' . $e->getMessage());
	    }

        $total_overdue = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
        									->where('user_id', '<>', 1)
        									->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
                                            ->where('status', TransactionLocal::STATUS_AVAILABLE)
                                            ->whereRaw('DATEDIFF(CURDATE(), created_at) > ' . TransactionLocal::DAYS_OVERDUE)
                                            ->count();

        $total_in_queue = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
        									->where('user_id', '<>', 1)
        									->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
                                            ->where('status', TransactionLocal::STATUS_AVAILABLE)
                                            ->count();

        $total_proceeding = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
        									->where('user_id', '<>', 1)
        									->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
                                            ->where('status', TransactionLocal::STATUS_PROCEEDING)
                                            ->count();

        $total_suspended = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
        									->where('user_id', '<>', 1)
        									->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
                                            ->where('status', TransactionLocal::STATUS_SUSPENDED)
                                            ->count();

        /*
        $overdue_date = date('Y-m-d 23:59:59', strtotime('-' . TransactionLocal::DAYS_OVERDUE . ' days'));

        $totals = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
                             ->where('user_id', '<>', 1)
                             ->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
                             ->whereNull('done_at')
                             ->sum('amount');

        $totals_overdue = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
                             ->where('user_id', '<>', 1)
                             ->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
                             ->whereNull('done_at')
                             ->where('created_at', '<=', $overdue_date)
                             ->sum('amount');
        */

        $withdraws = TransactionLocal::leftJoin('view_users AS vu', 'vu.id', '=', 'transactions.user_id')
                                ->leftJoin('user_payment_gateways AS upg', 'upg.id', '=', 'transactions.user_payment_gateway_id')
                                ->where('transactions.type', TransactionLocal::TYPE_WITHDRAWAL)
                                ->where('transactions.for', '<>', TransactionLocal::FOR_IJOBDESK)
                                ->where('transactions.user_id', '<>', 1)
                                ->where('transactions.amount', '<', 0)
                                ->addSelect('transactions.*')
                                ->addSelect('vu.fullname AS username')
                                ->addSelect('vu.role AS role')
                                ->addSelect('vu.location AS user_location')
                                ->addSelect('vu.email AS user_email')
                                ->addSelect('upg.gateway AS gateway')
                                ->addSelect(DB::raw('IF(transactions.status = ' . TransactionLocal::STATUS_AVAILABLE . ', 0, 1) AS `order`'));

        $sort = $request->input('sort', 'order');
        $sort_dir = $request->input('sort_dir', 'ASC');

        // Filtering
        $filter = $request->input('filter');

        // By #ID
        if (isset($filter['id']) && $filter['id'] != '') {
            $withdraws->where('transactions.id', $filter['id']);
        }

        // By User Name
        if (!empty($filter['username'])) {
            $withdraws->where(function($query) use ($filter) {
                if ( is_numeric($filter['username']) ) {
                    $query->where('vu.id', intval($filter['username']));
                } else {
                    $query->whereRaw('LOWER(vu.username) LIKE "%' . trim(strtolower($filter['username'])) . '%"')
                            ->orWhereRaw('LOWER(vu.fullname) LIKE "%' . trim(strtolower($filter['username'])) . '%"');
                }
            });
        }

        // By User Role
        if (!empty($filter['role'])) {
            $withdraws->where('vu.role', $filter['role']);
        }

        // By User Location
        if (!empty($filter['user_location'])) {
            $withdraws->where('vu.location', 'LIKE', '%'.trim($filter['user_location']).'%');
        }

        // By User Email
        if (!empty($filter['user_email'])) {
            $withdraws->where('upg.data', 'LIKE', '%'.trim($filter['user_email']).'%')
                        ->whereNull('upg.deleted_at');
        }

        // By Payment Gateway
        if (!empty($filter['gateway'])) {
            $withdraws->where('upg.gateway', $filter['gateway']);
        }

        // By Amount
        if (!empty($filter['amount'])) {
            $withdraws->whereRaw('-1 * CAST(transactions.amount AS INT) = ' . $filter['amount']);
        }

        // By Created At
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $withdraws->where('transactions.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $withdraws->where('transactions.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Done At
        if (!empty($filter['done_at'])) {
            if (!empty($filter['done_at']['from'])) {
                $withdraws->where('transactions.done_at', '>=', date('Y-m-d H:i:s', strtotime($filter['done_at']['from'])));
            }

            if (!empty($filter['done_at']['to'])) {
                $withdraws->where('transactions.done_at', '<=', date('Y-m-d H:i:s', strtotime($filter['done_at']['to']) + 24* 3600));
            }
        }

        // By Withdraw Status
        if ($filter['status'] != '') {
            $withdraws->where('transactions.status', $filter['status']);
        }

        $withdraws = $withdraws->orderBy($sort, $sort_dir)
                                ->orderBy('transactions.created_at', 'desc');

        $request->flashOnly('filter');

        return view('pages.admin.super.payment.withdraw', [
            'page' => 'super.payment.withdraw',
            'payment_gateways' => $paymentGateways,
            'withdraws' => $withdraws->paginate($this->per_page),
            'total_overdue' => $total_overdue,
            'total_in_queue' => $total_in_queue,
            'total_proceeding' => $total_proceeding,
            'total_suspended' => $total_suspended,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'filter' => $filter,
            'financial_managers' => $financial_managers,
        ]);
    }
}