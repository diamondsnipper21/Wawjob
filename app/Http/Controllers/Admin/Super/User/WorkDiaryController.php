<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 21, 2017
 * Report Page for User on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use iJobDesk\Http\Controllers\WorkdiaryController as Controller;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;

use iJobDesk\Models\Contract;

class WorkDiaryController extends BaseController {

    private $controller;

    public function __construct() {
        parent::__construct();

        // Add breadcrumbs
        add_breadcrumb('Users', route('admin.super.users.list'));
        add_breadcrumb('Work Diary');

        $this->page_title = 'Work Diary';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);
        
        $this->controller = new Controller();
        $this->controller->beforeAction($request);
    }

    public function view_first(Request $request, $user_id = null) {
        $user = ViewUser::find($user_id);

        if (empty($user))
            abort(404);

        view()->share([
            'page_title' => $this->page_title,
            'user'      => $user
        ]);

        return $this->controller->view_first($request, $user);
    }

    public function view(Request $request, $user_id, $cid) {
        $user = ViewUser::find($user_id);
        
        view()->share([
            'page_title' => $this->page_title
        ]);

        return $this->controller->view($request, $cid, $user);
    }

    public function ajaxAction(Request $request) {
        view()->share([
            'page_title' => $this->page_title
        ]);

        return $this->controller->ajaxAction($request);
    }
}