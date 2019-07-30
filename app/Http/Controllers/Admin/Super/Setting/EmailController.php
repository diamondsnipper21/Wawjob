<?php namespace iJobDesk\Http\Controllers\Admin\Super;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Views\ViewUser;

class EmailController extends BaseController {

    public function __construct() {
        $this->page_title = 'Send Email';
        parent::__construct();
    }

    public function index(Request $request) {

        add_breadcrumb('Users');

        $sort     = $request->input('sort', 'created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $action = $request->input('_action');
        if (!empty($action)) {
            if ( $action == 'selected' ) {
                $ids = $request->input('ids');            
                $users = User::whereIn('id', $ids)->get();
            } else if ( $action == 'freelancers' ) {
            	$users = User::where('role', User::ROLE_USER_FREELANCER)
            					->where('status', '<>', User::STATUS_NOT_AVAILABLE)
            					->get();
            } else if ( $action == 'buyers' ) {
                $users = User::where('role', User::ROLE_USER_BUYER)
                				->where('status', '<>', User::STATUS_NOT_AVAILABLE)
            					->get();
            } else if ( $action == 'all' ) {
            	$users = User::where('status', '<>', User::STATUS_NOT_AVAILABLE)
            					->get();
            }

            if ( $users ) {
            	$message = $request->input('_reason');

            	foreach ( $users as $user ) {
            		EmailTemplate::send($user, 'SEND_EMAIL', 0, [
						'USER' => $user->fullname(),
                        'MESSAGE' => nl2br($message),
					]);
            	}
            }
        }
        
        $users = ViewUser::whereNotInAdmin()
                         ->addSelect('view_users.*');

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

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.send_email', [
            'page' => 'super.settings.send_email',
            'users' => $users->paginate($this->per_page),
            'role' => null,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'status_selected' => $request->input('status'),
            'login_blocked' => $request->input('login_blocked')
        ]);       
    }
}