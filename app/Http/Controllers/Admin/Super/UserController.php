<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since July 7, 2017
 * User Management for Super Manager
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\Reason;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Views\ViewUser;

class UserController extends BaseController {

    public function __construct() {
        $this->page_title = 'Users';
        parent::__construct();
    }

    /**
    * Overview for users
    *
    * @return Response
    */
    public function dashboard(Request $request) {

        add_breadcrumb('User Overview');

        if ($request->ajax()) {
            $start_date = strtotime($request->input('start_date'));
            $end_date   = strtotime($request->input('end_date'));
        }

        $lifetime = $request->input('lifetime', false);

        if ($lifetime) {
            $start_date = strtotime(ViewUser::min('created_at_ymd'));
            $end_date   = strtotime(ViewUser::max('created_at_ymd'));
        } elseif (empty($end_date) || empty($start_date)) {
            $start_date = strtotime(date('Y-m-01'));
            $end_date   = strtotime(date('Y-m-t'));
        }

        // users by region
        $user_role = $request->input('user_role');
        $stat_region_users = User::getCountByRegion($user_role);

        $request->flashOnly('user_role');

        return view('pages.admin.super.users.dashboard', [
            'page' => 'super.users.dashboard',
            'start_date'    => date('m/d/Y', $start_date),
            'end_date'      => date('m/d/Y', $end_date),
            'lifetime'      => $lifetime,
            'stat_region_users' => $stat_region_users,
            'user_role' => $user_role,
            'stats'         => [
                                    'line' => [
                                        'data' => $this->userGraphLineData($start_date, $end_date),
                                        'options' => $this->lineChartOptions()
                                    ],
                                    'pie' => $this->userGraphPieData($start_date, $end_date)
                                ]
        ]);
    }

    public function userGraphLineData($start_date = null, $end_date = null) {
        $graphDataList = [];

        $date_count = ($end_date - $start_date)  / (24 * 3600);
        $show_data_type = 'Day';
        if ($date_count > 30 && $date_count <= 365) { // By Month
            $show_data_type = 'Month';
        } elseif ($date_count > 365) { // By Year
            $show_data_type = 'Year';
        }

        for ($date = $start_date; $date <= $end_date; $date += 24* 3600) {
            $users = User::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)])->get();

            $query_params = [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)];
            $users = User::where(function($query) use ($query_params) {
                            $query->orWhere(function($query) use ($query_params) {
                                        $query->where('status', '!=', User::STATUS_SUSPENDED)
                                              ->where(function($query) use($query_params) {
                                                    $query->orWhereBetween('created_at', $query_params)
                                                          ->orWhereBetween('deleted_at', $query_params);
                                              });
                                  })
                                  ->orWhere(function($query) use ($query_params) {
                                        $query->where('status', '=', User::STATUS_SUSPENDED)
                                              ->whereBetween('updated_at', $query_params);
                                  });
                         })
                         ->withTrashed()
                         ->get();

            $current_date = date('Y-n-j', $date);
            if ($show_data_type == 'Year')
                $current_date = date('Y', $date);
            elseif ($show_data_type == 'Month')
                $current_date = date('Y-n', $date);

            $graphData = [];
            if (array_key_exists($current_date, $graphDataList)) {
                $graphData = $graphDataList[$current_date];
            }

            if (empty($graphData)) {
                $graphData['date'] = $current_date;

                if ($show_data_type == 'Day') {
                    $graphData['date'] = date('n/j', $date);
                    foreach ($graphDataList as $k_date => $gd) {
                        if (date('Y-m', strtotime($k_date)) == date('Y-m', $date)) {
                            $graphData['date'] = date('j', $date);
                        }
                    }
                }

                foreach ($this->chartCategories() as $key)
                    $graphData[$key] = 0;
            }

            foreach ($users as $user) {
                if ($user->trashed()) {
                    $graphData['Deleted']++;
                } elseif ($user->status == User::STATUS_SUSPENDED) {
                    $graphData['Suspended']++;
                } elseif ($user->isBuyer()) {
                    $graphData['Buyer']++;
                } elseif ($user->isFreelancer()) {
                    $graphData['Freelancer']++;
                }
            }

            $graphDataList[$current_date] = $graphData;
        }

        return array_values($graphDataList);
    }

    private function userGraphPieData($start_date = null, $end_date = null) {
        $graphDataList = [];

        if (empty($start_date)) {
            $end_date = strtotime(date('Y-m-d'));
            $start_date = $end_date - 7 * 24 * 3600; // a week ago
        }

        $query_params = [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)];
        $users = User::where(function($query) use ($query_params) {
                        $query->orWhere(function($query) use ($query_params) {
                                    $query->where('status', '!=', User::STATUS_SUSPENDED)
                                          ->where(function($query) use($query_params) {
                                                $query->orWhereBetween('created_at', $query_params)
                                                      ->orWhereBetween('deleted_at', $query_params);
                                          });
                              })
                              ->orWhere(function($query) use ($query_params) {
                                    $query->where('status', '=', User::STATUS_SUSPENDED)
                                          ->whereBetween('updated_at', $query_params);
                              });
                     })
                     ->withTrashed()
                     ->get();

        foreach ($this->chartCategories() as $key) {
            $graphDataList[$key] = ['type' => $key, 'value' => 0];
        }

        foreach ($users as $user) {
            if ($user->trashed()) {
                $graphDataList['Deleted']['value']++;
            } elseif ($user->status == User::STATUS_SUSPENDED) {
                $graphDataList['Suspended']['value']++;
            } elseif ($user->isBuyer()) {
                $graphDataList['Buyer']['value']++;
            } elseif ($user->isFreelancer()) {
                $graphDataList['Freelancer']['value']++;
            }
        }
        
        return array_values($graphDataList);
    }

    private function chartCategories() {
        return ['Buyer', 'Freelancer', 'Suspended', 'Deleted'];
    }

    private function lineChartOptions() {
        foreach ($this->chartCategories() as $key) {
            $options[] = [
                "bullet" => "square",
                "bulletBorderAlpha" => 1,
                "bulletBorderThickness" => 1,
                "fillAlphas" => 0.3,
                // "fillColorsField" => "lineColor" . $key,
                "legendValueText" => "[[value]]",
                // "lineColorField" => "lineColor" . $key,
                "title" => $key,
                "valueField" => $key
            ];
        }

        return $options;
    }

    public function listing(Request $request, $role = null) {

        add_breadcrumb('Users');

        $sort     = $request->input('sort', 'created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $action = $request->input('_action');
        if (!empty($action)) {
            $ids = $request->input('ids');
            
            $users = User::whereIn('id', $ids);

            $status = $action;

            if ($status == User::STATUS_DELETED) {
                $count = 0;

                $_POST['_reason']    = $request->input('message');
                $_REQUEST['_reason'] = $request->input('message');
                // Delete users
                foreach ($ids as $id) {
                    $user = User::find($id);

                    // if (!$user->canDelete()) {
                    //     add_message(sprintf('The user(%s) has still %d active contract(s) or $%01.2f in wallet.', $user->fullname(), $user->totalActiveContracts(), $user->myBalance()), 'danger');

                    //     continue;
                    // }

                    if (!$user->delete()) {
                        add_message(sprintf('The %s(%s) haven\'t been deleted.', $user->role_name(), $user->fullname()), 'danger');

                        continue;
                    }

                    $count++;
                }

                if ($count != 0)
                    add_message(sprintf('The %d User(s) have been deleted.', $count), 'success');
            } else if ($status == User::STATUS_LOGIN_ENABLED) {
                $count = 0;
                // Enable users login
                foreach ($ids as $id) {
                    $user = User::find($id);

                    $user->login_blocked = 0;
                    $user->try_login = 0;
                    $user->try_password = 0;
                    $user->try_question = 0;

                    if ( $user->save() )
                    	$count++;
                }

                if ($count != 0)
                    add_message(sprintf('The %d User(s) have been enabled for login.', $count), 'success');
            } else {
                foreach ($ids as $id) {
                    $user = User::find($id);

                    if ($status == User::STATUS_SUSPENDED || $status == User::STATUS_FINANCIAL_SUSPENDED)
                        $user->is_auto_suspended = 0;
                    else
                        $user->is_auto_suspended = null;

                    if ($status == User::STATUS_REQUIRE_ID_VERIFIED) {
                        $status = User::STATUS_SUSPENDED;
                        $user->requireIDVerification();
                    }

                    $user->status = $status;

                    $user->save();
                }

                if ($status == User::STATUS_SUSPENDED || $status == User::STATUS_FINANCIAL_SUSPENDED) {
                    add_message(sprintf('%d User(s) have been suspended successfully', count($ids)), 'success');
                } elseif ($status == User::STATUS_AVAILABLE) {
                    add_message(sprintf('%d User(s) have been activated successfully', count($ids)), 'success');
                }
            }
        }
        
        $users = ViewUser::whereNotInAdmin()
                         ->withTrashed()
                         ->addSelect('view_users.*')
                         ->addSelect(DB::raw('IF(deleted_at IS NOT NULL, 1, 0) AS deleted'))
                         ->orderBy('deleted', 'ASC');

        if ($role == 'buyers')
            $role = User::ROLE_USER_BUYER;
        elseif ($role == 'freelancers')
            $role = User::ROLE_USER_FREELANCER;
        else
            $role = null;

        if (!empty($role))
            $users->where('role', $role);

        $users->orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        if ( $request->input('status') ) {
            $filter['status'] = $request->input('status');
        }

        if ( $request->input('login_blocked') ) {
            $filter['login_blocked'] = 1;
        }

        // By Email
        if (!empty($filter['email'])) {
            $users->where('email', 'LIKE', '%'.trim($filter['email']).'%');
        }

        // By ID
        if (!empty($filter['id'])) {
            $users->where('id', $filter['id']);
        }

        // By Username 
        if (!empty($filter['username'])) {
            $users->where('username', 'LIKE', '%'.trim($filter['username']).'%');
        }

        // By FullName
        if (!empty($filter['fullname'])) {
            $users->whereRaw('LOWER(fullname) LIKE "%'.trim(strtolower($filter['fullname'])).'%"');
        }

        // By Location
        if (!empty($filter['location'])) {
            $users->whereRaw('LOWER(location) LIKE "%'.trim(strtolower($filter['location'])).'%"');
        }

        // By Location
        if (!empty($filter['country'])) {
            $users->whereRaw('LOWER(country) LIKE "%'.trim(strtolower($filter['country'])).'%"');
        }

        // By Status
        if ( isset($filter['status']) ) {
        	if ( $filter['status'] == User::STATUS_LOGIN_BLOCKED ) {
        		$filter['login_blocked'] = 1;
        		unset($filter['status']);
        	} else {
	            if ( $filter['status'] == User::STATUS_DELETED ) {
	                $users->onlyTrashed();
	            } else {
	                $users->where('status', $filter['status']);
	            }
	        }
        }

        if (isset($filter['idv_status'])) { 
            if ( $filter['idv_status'] == User::STATUS_ID_VERFIED) {
                $filter['id_verified'] = 1;
                unset($filter['status']);
            } elseif ( $filter['idv_status'] == User::STATUS_ID_UNVERFIED ) {
                $filter['id_verified'] = 0;
                unset($filter['status']);
            }
        }

        // By Login Blocked
        if ( isset($filter['login_blocked']) ) {
            $users->where('login_blocked', $filter['login_blocked']);
        }

        // By ID verified
        if ( isset($filter['id_verified']) ) {
            $users->where('id_verified', $filter['id_verified']);
        }

        // By Role
        if (!empty($filter['role'])) {
            $users->where('role', $filter['role']);
        }

        // By Created Date
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $users->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $users->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24 * 3600));
            }
        }

        // By Last Activity
        if (!empty($filter['last_activity'])) {
            if (!empty($filter['last_activity']['from'])) {
                $users->where('last_activity', '>=', date('Y-m-d H:i:s', strtotime($filter['last_activity']['from'])));
            }

            if (!empty($filter['last_activity']['to'])) {
                $users->where('last_activity', '<=', date('Y-m-d H:i:s', strtotime($filter['last_activity']['to']) + 24* 3600));
            }
        }

        $users = $users->withTrashed();

        $request->flashOnly('filter');

        return view('pages.admin.super.users.list', [
            'page' => 'super.users.list',
            'users' => $users->paginate($this->per_page),
            'role' => $role,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'status_selected' => $request->input('status'),
            'login_blocked' => $request->input('login_blocked')
        ]);       
    }

    public function listing_freelancers(Request $request) {
        return $this->listing($request, 'freelancers');
    }

    public function listing_buyers(Request $request) {
        return $this->listing($request, 'buyers');
    }

    public function ajax_search_users(Request $request) {
        $result = [];

        $keywords = $request->q;

        $users = ViewUser::where('fullname', 'LIKE', '%' . $keywords . '%')
                         ->orWhere('email', 'LIKE', '%' . $keywords . '%')
                         ->orWhere('username', 'LIKE', '%' . $keywords . '%')
                         ->orWhere('id', $keywords)
                         ->get();

        if ( count($users) > 0 ) {
            foreach ( $users as $user ) {
                $result[] = [
                    'id' => $user->id, 
                    'text' => $user->username . ' - ' . $user->fullname, 
                    'title' => $user->name
                ];
            }
        }

        return response()->json($result);
    }
}