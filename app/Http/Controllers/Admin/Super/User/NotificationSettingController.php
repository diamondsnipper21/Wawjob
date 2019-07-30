<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 28, 2017
 * Report Page for User on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use iJobDesk\Http\Controllers\UserController as Controller;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;

class NotificationSettingController extends BaseController {

    private $controller;

    public function __construct() {
        parent::__construct();

        // Add breadcrumbs
        add_breadcrumb('Users', route('admin.super.users.list'));
        add_breadcrumb('Notification Settings');

        $this->page_title = 'Notification Settings';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);
        
        $this->controller = new Controller();
        $this->controller->beforeAction($request);
    }

    public function index(Request $request, $user_id = null) {
        $user = ViewUser::find($user_id);

        if (empty($user))
            abort(404);

        view()->share([
            'page_title' => $this->page_title
        ]);

        return $this->controller->notification_settings($request, $user);
    }
}