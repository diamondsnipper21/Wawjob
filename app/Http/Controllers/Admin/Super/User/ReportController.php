<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 21, 2017
 * Report Page for User on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use iJobDesk\Http\Controllers\ReportController as Controller;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;

class ReportController extends BaseController {

    private $controller;

    public function __construct() {
        parent::__construct();
    }

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);
        
        $this->controller = new Controller();
        $this->controller->beforeAction($request);
    }

    /**
    * Transactions
    * @param $id The identifier of User
    *
    * @return Response
    */
    public function transactions(Request $request, $user_id) {

        $this->page_title = 'Transactions';

        view()->share([
            'page_title' => $this->page_title
        ]);

        $user = User::find($user_id);

        // Add breadcrumbs
        add_breadcrumb('Users', route('admin.super.users.list'));
        add_breadcrumb('Transactions');

        return $this->controller->transactions($request, $user_id);
    }

    /**
    * Timesheets
    * @param $id The identifier of User
    *
    * @return Response
    */
    public function timesheet(Request $request, $user_id) {

        $this->page_title = 'Transactions';

        view()->share([
            'page_title' => $this->page_title
        ]);

        $user = User::find($user_id);

        // Add breadcrumbs
        add_breadcrumb('Users', route('admin.super.users.list'));
        add_breadcrumb('Timesheet');

        return $this->controller->timesheet($request, $user_id);
    }
}