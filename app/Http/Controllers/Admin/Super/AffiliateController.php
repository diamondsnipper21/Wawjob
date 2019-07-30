<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since July 14, 2017
 * Contracts Page for Freelancer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Auth;
use App;
use DB;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Views\ViewUser;


class AffiliateController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Affiliates';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request, $id = null) {

        if ($id == null) {
            add_breadcrumb('Affiliates Users');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('Affiliates Users');
        }
        
        return $this->listing($request, $id);
    }

    /**
    * Affiliates
    * @param $id The identifier of User
    *
    * @return Response
    */
    protected function listing(Request $request, $uid=null) {
        $user = null;

        if (!empty($uid)) {
            $user = ViewUser::find($uid);

            if (!$user)
                abort(404);
        }

        $payingUsers = $request->input('ids');

        if ($payingUsers != null) {

            foreach ($payingUsers as $user_id) {
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
            }

            add_message(sprintf('The %d Affiliate(s) has been paid.', count($payingUsers)), 'success');
        } 

        $sort     = $request->input('sort', 'user_id');
        $sort_dir = $request->input('sort_dir', 'asc');

        $sql = 'SELECT      
        			tt.user_id AS user_id, 
                    vu.username AS user_name, 
                    vu.fullname AS full_name,
                    SUM(tt.invited_buyer_count) AS invited_buyer_count, 
                    SUM(tt.invited_freelancer_count) AS invited_freelancer_count,
                    (
                        SELECT 
                        	SUM(ts.amount) FROM transactions ts
                    	WHERE 
                    		tt.user_id = ts.user_id AND 
                    		ts.type in (' . TransactionLocal::TYPE_AFFILIATE . ', ' . TransactionLocal::TYPE_AFFILIATE_CHILD . ') AND 
                    		ts.status = ' . TransactionLocal::STATUS_PENDING . '
                	) AS pending_amount,
                    (
                        SELECT 
                        	SUM(ts.amount) FROM transactions ts
                    	WHERE 
                    		tt.user_id = ts.user_id AND 
                    		ts.type in (' . TransactionLocal::TYPE_AFFILIATE . ', ' . TransactionLocal::TYPE_AFFILIATE_CHILD . ') AND 
                    		ts.status = ' . TransactionLocal::STATUS_DONE . '
                	) AS paid_amount,
                    ( 
						SELECT MAX(ts.done_at) FROM transactions ts 
						WHERE
							tt.user_id = ts.user_id AND 
                    		ts.type in (' . TransactionLocal::TYPE_AFFILIATE . ', ' . TransactionLocal::TYPE_AFFILIATE_CHILD . ') AND 
                    		ts.status = ' . TransactionLocal::STATUS_DONE . '
					) AS last_payment
                FROM 
                (
                    SELECT 
                    	t.user_id, 
                        SUM(t.invited_buyer_count) AS invited_buyer_count, 
                        SUM(t.invited_freelancer_count) AS invited_freelancer_count
                    FROM (
	                    SELECT 
	                    	ua.user_id, 
                            count(ua.affiliate_id) AS invited_buyer_count,
                            0 AS invited_freelancer_count
	                    FROM user_affiliates ua, users u
	                    WHERE
	                    	ua.affiliate_id = u.id AND
	                    	u.role = ' . User::ROLE_USER_BUYER . '
	                    GROUP BY ua.user_id
	                    UNION
	                    SELECT
	                    	ua.user_id, 
                            0 AS invited_buyer_count,
                            count(ua.affiliate_id) AS invited_freelancer_count
	                    FROM user_affiliates ua, users u
	                    WHERE
	                    	ua.affiliate_id = u.id AND
	                    	u.role = ' . User::ROLE_USER_FREELANCER . '
	                    GROUP BY ua.user_id
                    ) t 
                    GROUP BY t.user_id
                ) tt, view_users vu
                WHERE
                	tt.user_id = vu.id AND 
                	isNull(vu.deleted_at)';                

        $filter = $request->input('filter');

        // By ID
        if (isset($filter) && $filter['user_id'] != '') {
            $sql = $sql . 'AND tt.user_id = ' . $filter['user_id'] . ' ';
        }

        if (isset($filter) && $filter['full_name'] != '') {
            if ( is_numeric($filter['full_name']) ) {
                $sql = $sql . 'AND vu.id = ' . intval($filter['full_name']) . ' ';
            } else {
                $sql = $sql . 'AND (vu.username LIKE ' . 'CONCAT("%", "' . trim($filter['full_name']) . '", "%") OR vu.fullname LIKE ' . 'CONCAT("%", "' . trim($filter['full_name']) . '", "%"))' . ' ';
            }
        }

        $sql .= 'GROUP BY tt.user_id ';

        // By Last Payment
        if (!empty($filter['last_payment'])) {
        	$having = '';

            if (!empty($filter['last_payment']['from'])) {
                $having .= 'last_payment >= "' . date('Y-m-d H:i:s', strtotime($filter['last_payment']['from'])) . '" ';
            }

            if (!empty($filter['last_payment']['to'])) {
            	if ( !$having ) {
            		$having .= 'AND ';
            	}

            	$having .= 'last_payment < "' . date('Y-m-d H:i:s', strtotime($filter['last_payment']['to']) + 24* 3600) . '" ';
            }

            if ( $having ) {
            	$sql .= 'HAVING ' . $having;
            }
        }

        $sql .= 'ORDER BY ' . $sort . ' ' . $sort_dir;

        $result = DB::select($sql);

        $request->flashOnly('filter');

        $page     = $request->input('page', 1);
        $perPage = $this->per_page;
        
        $viewData = array_slice($result, ($page-1)*$perPage, $perPage);
        
        $paginator = new Paginator($viewData, count($result), $perPage, $page);
        $paginator->setPath(route('admin.super.affiliates.users'));
        
        return view('pages.admin.super.affiliates.affiliates', [
            'page'          => 'super.affiliates.affiliates',
            'user'          => $user,
            'affiliates'    => $paginator,
            'sort'          => $sort,
            'sort_dir'      => '_' . $sort_dir,
            'userId'        => $uid,
        ]);  
    }

    public function overview(Request $request) {

        add_breadcrumb('Affiliates Overview');
    
        $baseData = [
            'pending_payment'           => $this->getPendingPayment(),
            'last_payment'              => $this->getLastPayment(),
            'lifetime_payment'          => $this->getLifetimePayment(),
            'invited_users_count'       => $this->getInvitedUserCount(),
            'invited_freelancers_count' => $this->getInvitedUserCount(User::ROLE_USER_FREELANCER),
            'invited_buyers_count'      => $this->getInvitedUserCount(User::ROLE_USER_BUYER),
            'affiliate_users_count'     => $this->getAffiliateUserCount(null),
        ];

        if ($request->ajax()) {
            $start = strtotime($request->input('start_date'));
            $end = strtotime($request->input('end_date'));
            $lifetime = $request->input('lifetime');
        }

        if (empty($end) || empty($start)) {
            $end   = strtotime(date('Y-m-d'));
            $start = strtotime( "-6 month", $end); // 6 month ago
        } 

        $start_date_picker = $start;
        $end_date_picker = $end;
        
        $affiliate_graph_line = [ 
            'data' => $this->graphLineData($start, $end),
            'options'  => $this->lineChartOptions(['Invited Buyers', 'Invited Freelancers', 'Invited Users', 'Affiliate Users'])
        ];

        $affiliate_graph_pie = $this->graphPieData($start, $end);

        $payment_graph_line = [ 
            'data' => $this->paymentGraphLineData($start, $end),
            'options'  => $this->lineChartOptions(['Total Payment', 'Invited Buyers', 'Invited Freelancers'])
        ];
        
        $payment_graph_pie = $this->paymentGraphPieData($start, $end);

        return view('pages.admin.super.affiliates.overview', [
            'page'      => 'super.affiliates.overview',
            'baseData'  => $baseData,
            'lifetime'  => true,
            'affiliate_graph_line'  => $affiliate_graph_line,
            'affiliate_graph_pie'   => $affiliate_graph_pie,
            'payment_graph_line'    => $payment_graph_line,
            'payment_graph_pie'     => $payment_graph_pie,
            'period'    => [
                'startDate' => date('m/d/Y', $start_date_picker),
                'endDate'   => date('m/d/Y', $end_date_picker),
            ]
        ]); 
    }

    private function getPendingPayment() {

        return  [
            'amount' => TransactionLocal::whereIn('type', [
							TransactionLocal::TYPE_AFFILIATE,
							TransactionLocal::TYPE_AFFILIATE_CHILD
						])
						->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
						->whereIn('status', [
                            TransactionLocal::STATUS_PENDING,
                            TransactionLocal::STATUS_AVAILABLE
                        ])
						->sum('amount'),
		];
    }

    private function getLifetimePayment() {
        $today = date('Y-m-d');

        return  [
            'amount' => TransactionLocal::whereIn('type', [
							TransactionLocal::TYPE_AFFILIATE,
							TransactionLocal::TYPE_AFFILIATE_CHILD
						])
						->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
						->where('status', TransactionLocal::STATUS_DONE)
						->sum('amount'),
        ];
    }

    private function getLastPayment() {
        $sql =      "select     sum(amount) as total_paid, 
                                date_format(done_at, '%b %m, %Y') as last_paid_date 
                    from        transactions
                    where       `type` in (" . TransactionLocal::TYPE_AFFILIATE . ", " . TransactionLocal::TYPE_AFFILIATE_CHILD . ") AND `for` <> " . TransactionLocal::FOR_IJOBDESK . " AND `status` = " . TransactionLocal::STATUS_DONE . "
                    group by date_format(done_at, '%Y-%m-%d')
                    order by date_format(done_at, '%Y-%m-%d') desc
                    limit 1";
        $result = DB::select($sql);
        
        return  !empty($result) ? [
            'amount' => $result[0]->total_paid,
            'date'  => $result[0]->last_paid_date,
        ] : [
            'amount' => 0,
            'date'  => 'None',   
        ];
    }

    private function graphLineData($start, $end) {
        $graphDataList = [];

        $date = $start;

        while ($date < $end) {

            $period = [
                date('Y-m-d', strtotime('first day of +0 month', $date)), 
                date('Y-m-d', strtotime('last day of +0 month', $date)),
            ];
            
            $graphData = [];
            $graphData['month'] = date("Y-M", $date);
            
            foreach (['Invited Buyers', 'Invited Freelancers', 'Affiliate Users'] as $key)
                $graphData[$key] = 0;

            $graphData['Invited Buyers'] = $this->getInvitedUserCount(User::ROLE_USER_BUYER, $period);
            $graphData['Invited Freelancers'] = $this->getInvitedUserCount(User::ROLE_USER_FREELANCER, $period);
            $graphData['Invited Users'] = $this->getInvitedUserCount(null, $period);
            $graphData['Affiliate Users'] = $this->getAffiliateUserCount($period);

            $graphDataList[] = $graphData;

            $date = strtotime('first day of +1 month', $date);
        }
        
        return $graphDataList;
    }

    private function graphPieData($start, $end) {

        $period = [
            date('Y-m-d', strtotime('first day of +0 month', $start)), 
            date('Y-m-d', strtotime('last day of +0 month', $end)),
        ];

        $graphDataList = [[
                'type' => 'Invited Buyers',
                'value' => $this->getInvitedUserCount(User::ROLE_USER_BUYER, $period),
            ], [
                'type' => 'Invited Freelancers',
                'value' => $this->getInvitedUserCount(User::ROLE_USER_FREELANCER, $period),
            ], [
                'type' => 'Affiliate Users',
                'value' => $this->getAffiliateUserCount($period),
        ]];

        return $graphDataList;
    }

    private function lineChartOptions($categories) {
        foreach ($categories as $key) {
            $options[] = [
                "bullet" => "square",
                "bulletBorderAlpha" => 1,
                "bulletBorderThickness" => 1,
                "fillAlphas" => 0.3,
                // "fillColorsField" => "lineColor" . $value,
                "legendValueText" => "[[value]]",
                // "lineColorField" => "lineColor" . $value,
                "title" => $key,
                "valueField" => $key
            ];
        }

        return $options;
    }

    private function getInvitedUserCount($userType=null, $period=null) {
        if ($userType != null) {
            if ($period != null) {
                if (count($period) == 1) {
                    $start = $period[0];
                    $end = $period[0];
                } else {
                    $start = $period[0];
                    $end = $period[1];
                }
                
                return UserAffiliate::join('users', 'user_affiliates.affiliate_id', '=', 'users.id')
                            ->where('users.role', $userType)
                            ->whereBetween('user_affiliates.created_at', [$start , $end])
                            ->count();
            } else {
                return UserAffiliate::join('users', 'user_affiliates.affiliate_id', '=', 'users.id')
                            ->where('users.role', $userType)
                            ->count();
            }
            
        } else {
            if ($period != null) {
                if (count($period) == 1) {
                    $start = $period[0];
                    $end = $period[0];
                } else {
                    $start = $period[0];
                    $end = $period[1];
                }
                return UserAffiliate::whereBetween('user_affiliates.created_at', [$start, $end])
                                    ->count();
            } else {
                return UserAffiliate::count();
            }
            
        }
    }

    private function getAffiliateUserCount($period=null) {
        if ($period !=null) {
            if (count($period) == 1) {
                $start = $period[0];
                $end = $period[0];
            } else {
                $start = $period[0];
                $end = $period[1];
            }
            
            $result = DB::select("select count(distinct user_id) as count from user_affiliates where created_at between '" . $start . "' and '" . $end . "'");
        } else {
            $result = DB::select('select count(distinct user_id) as count from user_affiliates');
        }
        
        return $result[0]->count;  
    }    

    private function getTotalPayment($period = null) {
        return TransactionLocal::whereIn('type', [
							TransactionLocal::TYPE_AFFILIATE,
							TransactionLocal::TYPE_AFFILIATE_CHILD
						])
						->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
						->where('status', TransactionLocal::STATUS_DONE)
						->whereBetween('done_at', [$period[0] , $period[1]])
						->sum('amount');
    }

    private function getUserInvitedPayment($userType, $period = null) {
        return UserAffiliate::leftJoin('users', 'user_affiliates.affiliate_id', '=', 'users.id')
        					->leftJoin('transactions', 'user_affiliates.affiliate_id', '=', 'transactions.user_id')
							->whereIn('transactions.type', [
								TransactionLocal::TYPE_AFFILIATE,
								TransactionLocal::TYPE_AFFILIATE_CHILD
							])
	        				->where('users.role', $userType)
							->where('transactions.for', '<>', TransactionLocal::FOR_IJOBDESK)
							->where('transactions.status', TransactionLocal::STATUS_DONE)
							->whereBetween('transactions.done_at', [$period[0] , $period[1]])
							->sum('transactions.amount');
    }

    private function paymentGraphLineData($start, $end) {
        $graphDataList = [];
        $date = $start;

        while ($date < $end) {
            $period = [
                date('Y-m-d', strtotime('first day of +0 month', $date)), 
                date('Y-m-d', strtotime('last day of +0 month', $date)),
            ];
            
            $graphData = [];
            $graphData['month'] = date("Y-M", $date);
            
            foreach (['Total Payment', 'Invited Buyers', 'Invited Freelancers'] as $key)
                $graphData[$key] = 0;

            $graphData['Total Payment'] = $this->getTotalPayment($period);
            $graphData['Invited Buyers'] = $this->getUserInvitedPayment(User::ROLE_USER_BUYER, $period);
            $graphData['Invited Freelancers'] = $this->getUserInvitedPayment(User::ROLE_USER_FREELANCER, $period);
            
            $graphDataList[] = $graphData;

            $date = strtotime('first day of +1 month', $date);
        }
        //dd($graphDataList);
        return $graphDataList;
    }

    private function paymentGraphPieData($start, $end) {
        $period = [
            date('Y-m-d', strtotime('first day of +0 month', $start)), 
            date('Y-m-d', strtotime('last day of +0 month', $end)),
        ];

        $graphDataList = [[
                'type' => 'Total Payment',
                'value' => $this->getTotalPayment($period),
            ], [
                'type' => 'Invited Freelancers',
                'value' => $this->getUserInvitedPayment(User::ROLE_USER_FREELANCER, $period),
            ], [
                'type' => 'Invited Buyers',
                'value' => $this->getUserInvitedPayment(User::ROLE_USER_BUYER, $period),
        ]];

        return $graphDataList;
    }
}