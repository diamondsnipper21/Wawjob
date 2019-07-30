<?php namespace iJobDesk\Http\Controllers\Admin\Super\Payment;
/**
 * @author KCG
 * @since July 28, 2017
 * Escrow Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use Config;
use Log;

use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\Contract;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\WalletHistory;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\SiteWalletHistory;

class SiteWithdrawController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'iJobDesk Payment';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        $current_user = Auth::user();
        
        add_breadcrumb('iJobDesk Payment');

        // Check iJobDesk total earning amount
        $earning = SiteWallet::earning();
        $total_earning = $earning->amount;

        try {
            $action = $request->input('_action');

            if ( $action ) {
                if ($action == 'WITHDRAW') {
                    $amount = $request->input('amount');

    				if ( $amount > $total_earning ) {
    					add_message('You can not withdraw more than total earning amount.', 'danger');
    				} else {

    	                $transaction = new TransactionLocal;
    	                
    	                $transaction->for       = TransactionLocal::FOR_IJOBDESK;
    	                $transaction->type      = TransactionLocal::TYPE_SITE_WITHDRAWAL;
    	                $transaction->user_id   = Auth::user()->id;
    	                $transaction->note      = $request->input('note');
    	                $transaction->amount    = -($amount);
    	                $transaction->status    = TransactionLocal::STATUS_AVAILABLE;

    	                if ( $transaction->save() ) {
                            // Update iJobDesk earning wallet history
                            $earning = SiteWallet::earning();
                            $newAmount = $earning->amount - $amount;
                            $earning->amount = $newAmount;
                            $earning->save();

                            SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $transaction->id);

    	    				add_message('Withdraw request has been done successfully.', 'success');
    	                } else {
    	                    add_message('Withdraw request has not been done successfully.', 'danger');
    	                }
    	            }

                } else if ($action == TransactionLocal::STATUS_PROCEEDING) {
                    $ids = $request->input('ids');
                    if ( is_string($ids) ) {
                        $ids = explode(',', $ids);
                    }

                    //TransactionLocal::whereIn('id', $ids)->update(['status' => TransactionLocal::STATUS_PROCEEDING]);
                    if ( $ids ) {
                        foreach ($ids as $withdraw_id) {
                            $t = TransactionLocal::find($withdraw_id);
							$t->status = TransactionLocal::STATUS_DONE;
							$t->done_at = date('Y-m-d H:i:s');

							if ( $t->save() ) {
								// Update iJobDesk holding wallet history
								$holding = SiteWallet::holding();
								$newAmount = $holding->amount - abs($t->amount);
								$holding->amount = $newAmount;
								$holding->save();

								SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

								// Send email to super admin
								EmailTemplate::sendToSuperAdmin('SUPER_ADMIN_SITE_WITHDRAW', User::ROLE_USER_SUPER_ADMIN, [
									'AMOUNT' => formatCurrency(abs($t->amount)),
									'DOER' => $t->user->fullname(),
									'ROLE' => array_get(User::adminType(), $t->user->role),
									'DATE' => date('Y/m/d H:i:s')
								]);
							}
                        }
                    }

                    add_message(sprintf('The %d requests has been proceed.', count($ids)), 'success');

                } elseif ($action == 'DELETE') {
                    $ids = $request->input('ids');

                    foreach ($ids as $id) {
    					$withdraw = TransactionLocal::find($id);

                        // Update iJobDesk earning wallet history
                        $earning = SiteWallet::earning();
                        $newAmount = $earning->amount + abs($withdraw->amount);
                        $earning->amount = $newAmount;
                        $earning->save();

                        SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $withdraw->id);

                    	$withdraw->delete();
                    }
                    
                    add_message(sprintf('The %d withdraw(s) has been deleted.', count($ids)), 'success');

                }

                if ( $current_user->isFinancial() )
                    return redirect()->route('admin.financial.site_withdraws');
                else
                    return redirect()->route('admin.super.payment.site_withdraws');
            }
        } catch ( Exception $e ) {
            Log::error('[Admin - SiteWithdrawController.php::index()] Error: ' . $e->getMessage());
        }

        $withdraws = TransactionLocal::leftJoin('view_users AS vu', 'vu.id', '=', 'transactions.user_id')
		                       ->where('transactions.for', TransactionLocal::FOR_IJOBDESK)
		                       ->where('transactions.type', TransactionLocal::TYPE_SITE_WITHDRAWAL)
		                       ->whereIn('vu.role', [
                                    User::ROLE_USER_SUPER_ADMIN,
                                    User::ROLE_USER_FINANCIAL_MANAGER
                                ])
		                       ->addSelect('transactions.*')
		                       ->addSelect('vu.fullname');

        $sort = $request->input('sort', 'transactions.created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $withdraws = $withdraws->orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Comment
        if (isset($filter) && $filter['note'] != '') {
            $withdraws->where('transactions.note', 'LIKE', '%'.trim($filter['note']).'%');
        }

        // By Name or ID
        if (!empty($filter['fullname'])) {
            $withdraws->where(function($query) use ($filter) {
                if ( is_numeric($filter['fullname']) ) {
                    $query->where('vu.id', intval($filter['fullname']));
                } else {
                    $query->whereRaw('LOWER(vu.fullname) LIKE "%' . trim(strtolower($filter['fullname'])) . '%"')
                          ->orWhereRaw('LOWER(vu.username) LIKE "%' . trim(strtolower($filter['fullname'])) . '%"');
                }
            });
        }

        // By Amount
        if (!empty($filter['amount'])) {
            $withdraws->where('transactions.amount', -($filter['amount']));
        }

        // By Created Date
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $withdraws->where('transactions.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $withdraws->where('transactions.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Withdraw Status
        if ($filter['status'] != '') {
            $withdraws->where('transactions.status', $filter['status']);
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.payment.site_withdraws', [
            'page' => 'super.payment.site_withdraws',
            'withdraws' => $withdraws->paginate($this->per_page),
            'total' => $total_earning,
            'action' => $action,
            'sort' => $sort,
            'sort_dir' => '_' . $sort_dir,
            'filter' => $filter,
        ]);
    }
}