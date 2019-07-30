<?php namespace iJobDesk\Http\Controllers\Admin\Super;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\UserContact;
use iJobDesk\Models\Todo;

class AdminUsersController extends BaseController {

    public function __construct() {
        $this->page_title = 'Admin Users';
        parent::__construct();
    }

    /**
    * Show administrator list
    *
    * @return Response
    */
    public function index(Request $request) {
        if ($request->input('_action') == 'CHANGE_STATUS') {
            
            $status = $request->input('status');
            $reason = $request->input('_reason');

            $ids = $request->input('id');

            if ($status == User::STATUS_DELETED) {
                $count = 0;
                // Delete users
                foreach ($ids as $id) {
                    $user = User::find($id);

                    if (!$user->delete()) {
                        add_message(sprintf('The %s(%s) haven\'t been deleted.', $user->role_name(), $user->fullname()), 'danger');

                        continue;
                    }
                    
                    $count++;
                }

                if ($count != 0)
                    add_message(sprintf('The %d User(s) have been deleted.', $count), 'success');
            } else {
                foreach ($ids as $id) {
                    $user = User::find($id);
                    $user->status = $status;

                    $user->save();
                }

                if ($status == User::STATUS_SUSPENDED) {
                    add_message(sprintf('%d Administrator(s) have been suspended successfully', count($ids)), 'success');
                } elseif ($status == User::STATUS_AVAILABLE) {
                    add_message(sprintf('%d Administrator(s) have been activated successfully', count($ids)), 'success');
                }
            }
        }

        // sort
        $sort     = $request->input('sort', 'created_at');
        $sort_dir = $request->input('sort_dir', 'desc');
        
        $admins = ViewUser::whereIn('role', array_keys(User::adminType()))
                          ->orderBy($sort, $sort_dir);
                          // ->withTrashed();

        $filter = $request->input('filter');

        if (!empty($filter['email'])) {
            $admins->where('email', 'LIKE', '%' . trim($filter['email']) . '%');
        }

        if (!empty($filter['fullname'])) {
            $admins->whereRaw("LOWER(fullname) LIKE '%" . trim($filter['fullname']) . "%'");
        }

        if (!empty($filter['role'])) {
            $admins->where('role', $filter['role']);
        }

        if (!empty($filter['status'])) {
            $admins->where('status', $filter['status']);
        }

        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $admins->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $admins->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24 * 3600 ));
            }
        }

        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $admins->where('updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $admins->where('updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24 * 3600 ));
            }
        }

        $request->flashOnly('filter');        

        return view('pages.admin.super.admin_users.list', [
            'page'   => 'super.admin_users.list',
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'admins' => $admins->paginate($this->per_page)
        ]);
    }

    public function edit(Request $request, $id = null) {
        $admin_user = new User();
        if (!empty($id)) {
            $admin_user = User::find($id);

            if (empty($admin_user))
                abort(404);
        }

        $action = $request->input('_action');
        if ($action == 'SAVE') {
            if ($request->input('password'))
                $admin_user->password    = bcrypt($request->input('password'));

            if (empty($id))
                $admin_user->username    = $request->input('username');

            $admin_user->email       = $request->input('email');
            $admin_user->role        = $request->input('role');
            $admin_user->status      = User::STATUS_AVAILABLE;
            $admin_user->save();

            $admin_contact = $admin_user->contact;
            if (!$admin_contact) {
                $admin_contact = new UserContact();
            }

            $admin_contact->user_id     = $admin_user->id;
            $admin_contact->first_name  = $request->input('first_name');
            $admin_contact->last_name   = $request->input('last_name');
            $admin_contact->timezone_id = $request->input('timezone');

            $admin_contact->save();

            if (!$id)
                add_message('Successfully added new administrator.', 'success');
            else
                add_message('Successfully updated administrator.', 'success');
        }

        return view('pages.admin.super.admin_users.edit_modal', [
            'id' => $id,
            'admin_user' => $admin_user
        ]);
    }

    public function check_duplicated(Request $request, $id = null) {
        $admin_user = new User();

        if ($id)
            $admin_user = User::find($id);

        $field_name     = $request->input('field');
        $field_value    = $request->input($field_name);

        $exists = User::whereRaw("$field_name = '$field_value'")
                      ->where(function($query) use ($id) {
                            if ($id)
                                $query->where('id', '!=', $id);
                      })
                      ->exists();
        if ($exists)
            return "false";
        else 
            return "true";
    }
}