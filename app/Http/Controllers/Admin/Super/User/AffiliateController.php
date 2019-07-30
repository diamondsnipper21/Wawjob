<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 11, 2017
 * User Overview Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\TransactionLocal;

use iJobDesk\Models\Views\ViewUser;

class AffiliateController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Affiliate';

        add_breadcrumb('Users', route('admin.super.users.list'));
        add_breadcrumb('Affiliate');

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * User Detail Overview
    * @param $user_id The identifier of User
    *
    * @return Response
    */
    public function index(Request $request, $user_id, $tab=null) {
        $user = ViewUser::find($user_id);

        if (!$user)
            abort(404);

        $lastPayment = TransactionLocal::where('user_id', $user_id)
                                        ->whereIn('type', [
                                            TransactionLocal::TYPE_AFFILIATE,
                                            TransactionLocal::TYPE_AFFILIATE_CHILD
                                        ])
                                        ->where('status', TransactionLocal::STATUS_DONE)
                                        ->orderBy('done_at', 'desc')
                                        ->first();

        $lastPaymentAmount = '-';
        if ( $lastPayment ) {
            $lastPaymentAmount = $lastPayment->amount;

            if ( $lastPaymentAmount > 0 ) {
                $lastPaymentAmount = '$' . formatCurrency($lastPaymentAmount);
            } else {
                $lastPaymentAmount = '($' . formatCurrency(abs($lastPaymentAmount)) . ')';
            }
        }

        $paymentData = [
            'total_accepted_buyers' => $user->getTotalAffiliated(User::ROLE_USER_BUYER),
            'total_accepted_freelancers' => $user->getTotalAffiliated(User::ROLE_USER_FREELANCER),
            'total_secondary_accepted_buyers' => $user->getTotalSecondaryAffiliated(User::ROLE_USER_BUYER),
            'total_secondary_accepted_freelancers' => $user->getTotalSecondaryAffiliated(User::ROLE_USER_FREELANCER),
            'total_sent' => $user->getTotalAffiliatesSent(),
            'last_payment_amount' => $lastPaymentAmount,
            'all_user_count' => $user->getTotalAffiliatesUsers(), 
            'pending_amount' => $user->getTotalAffiliatesAmount([
                'status' => [
                    TransactionLocal::STATUS_PENDING,
                    TransactionLocal::STATUS_AVAILABLE,
                ]
            ]), 
            'lifetime_amount' => $user->getTotalAffiliatesAmount([
                'status' => [
                    TransactionLocal::STATUS_DONE,
                ]
            ])
        ];

        $affiliatedUsers = $user->getAffiliatedUsers();

        $sort = $request->input('sort', 'transactions.created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $transactions = TransactionLocal::leftJoin('view_users AS vu', 'vu.id', '=', 'transactions.ref_user_id')
                                    ->where('transactions.user_id', $user_id)
        							->whereIn('transactions.type', [
        								TransactionLocal::TYPE_AFFILIATE,
        								TransactionLocal::TYPE_AFFILIATE_CHILD
        							])
        							->where('transactions.for', '<>', TransactionLocal::FOR_IJOBDESK)
                                    ->addSelect('transactions.*')
                                    ->addSelect('vu.fullname AS username')
                                    ->addSelect('vu.email AS email');

        // Filtering
        $filter = $request->input('filter');

        // By #ID
        if (isset($filter) && $filter['id'] != '') {
            $transactions->where('transactions.id', $filter['id']);
        }

        // By User Name
        if (!empty($filter['username'])) {
            $transactions->where(function($query) use ($filter) {
                if ( is_numeric($filter['username']) ) {
                    $query->where('vu.id', intval($filter['username']));
                } else {
                    $query->whereRaw('LOWER(vu.username) LIKE "%' . trim(strtolower($filter['username'])) . '%"')
                            ->orWhereRaw('LOWER(vu.fullname) LIKE "%' . trim(strtolower($filter['username'])) . '%"');
                }
            });
        }

        // By Amount
        if (!empty($filter['amount'])) {
            $transactions->where('transactions.amount', $filter['amount']);
        }

        // By Deposited At
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $transactions->where('transactions.created_at', '>=', convertTz($filter['created_at']['from'], 'UTC', $this->auth_user->getTimezoneName(), 'Y-m-d H:i:s'));
            }

            if (!empty($filter['created_at']['to'])) {
                $transactions->where('transactions.created_at', '<=', convertTz(date('Y-m-d', strtotime($filter['created_at']['to']) + 24* 3600), 'UTC', $this->auth_user->getTimezoneName(), 'Y-m-d H:i:s'));
            }
        }

        // By Status
        if ($filter['status'] != '') {
            $transactions->where('transactions.status', $filter['status']);
        }

        $transactions = $transactions->orderBy($sort, $sort_dir);

        $request->flashOnly('filter');

        return view('pages.admin.super.user.affiliate', [
            'page'              => 'super.user.affiliate',
            'user'              => $user,
            'userId'            => $user->id,
            'paymentData'       => $paymentData,
            'affiliated_users'  => $affiliatedUsers,
            'transactions'      => $transactions->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'historyTab'        => 'master',
            'actTab'            => $tab == null ? 'payment' : 'history',
        ]);  
    }

    public function pay($user_id) {
    	$user = ViewUser::find($user_id);

        if ( !$user ) {
            abort(404);
        }

    	if ( $user->isSuspended() || $user->isFinancialSuspended() ) {
    		return redirect()->route('admin.super.user.affiliate', ['user_id' => $user_id]);
    	}

        $transactions = TransactionLocal::where('user_id', $user_id)
							->whereIn('type', [
								TransactionLocal::TYPE_AFFILIATE,
								TransactionLocal::TYPE_AFFILIATE_CHILD
							])
							->where('status', TransactionLocal::STATUS_PENDING)
							->get();

		if ( count($transactions) ) {
			foreach ( $transactions as $t ) {
				// Process payment
				TransactionLocal::payOne($t);
			}
		}

        return redirect()->route('admin.super.user.affiliate', ['user_id' => $user_id]);
    }

    public function getAffiliateUsers($user_id, $user_status) {
        return UserAffiliate::join('users', 'user_affiliates.affiliate_id', '=', 'users.id')
							->where('users.status', $user_status)
							->where('user_affiliates.user_id', $user_id)
							->select('user_affiliates.*')
							->get();
    }
}