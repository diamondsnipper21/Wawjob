<?php namespace iJobDesk\Http\Controllers\Admin;

use iJobDesk\Http\Controllers\Controller as BaseController;

use Auth;
use Config;

use Illuminate\Http\Request;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\AdminMessage;

use iJobDesk\Http\Controllers\FileController;

class AdminController extends BaseController {

    /**
    * The authenticated user.
    *
    * @var User Model
    */
    protected $auth_user;

    /**
    * The flag if the user own super admin role.
    *
    * @var boolean
    */
    protected $is_super_admin = false;

    protected static $tmp;

    public $page_title = '';

    public $role_id;

    /**
    * Constructor
    */
    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);

        $this->avatar_size = Config::get("settings.admin.avatar_size");
        $this->per_page = Config::get('settings.admin.per_page');
        $this->detail_page = Config::get('settings.admin.detail_page');

        // Do something with authenticated user.
        $this->auth_user = Auth::user();
        $this->is_super_admin = $this->auth_user->isSuper();

        $role_id = '';
        if ($this->auth_user) {
            // Unread Notifications
            $unread_notifications = UserNotification::getUnread($this->auth_user->id);

            // Unread Messages
            $unread_messages = AdminMessage::getUnread();

            // Role Identifier
            $role_id = $this->auth_user->role_identifier();
        }

        $this->role_id = $role_id;

        // Add breadcrumb
        reset_breadcrumb();
        add_breadcrumb('Home', route('admin.'.$this->role_id.'.dashboard'));

        // Calculate unread tickets
        $count_of_ticket_with_new_msg = Ticket::getCountUnreadMsg();

        // Share the global vars with view.
        view()->share([
            'auth_user' => $this->auth_user,
            'current_user' => $this->auth_user,
            'is_super_admin' => $this->is_super_admin,

            'unread_notifications'  => $unread_notifications,
            'unread_messages'       => $unread_messages,

            'avatar_size' => $this->avatar_size,
            'per_page' => $this->per_page,
            'page_title' => $this->page_title,
            'role_id' => $role_id,
            
            'count_of_ticket_with_new_msg' => $count_of_ticket_with_new_msg
        ]);

        view()->share('res_version', Config::get('settings.res_version.backend'));
    }

    /**
    * Return failure flag to ajax caller
    *
    * @author paulz
    * @created Mar 9, 2016
    */
    protected function failed($msg = '') {
        return response()->json([
            'success' => false,
            'msg' => $msg
        ]);
    }
}