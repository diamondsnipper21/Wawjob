<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author PYH
 * @since July 9, 2017
 * Job Postings  Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\UserContact;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Notification;

class StatsController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Stats';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Show Job Postings.
    *
    * @return Response
    */
    public function index(Request $request) {
        return view('pages.admin.super.stats.index', [
            'page' => 'super.stats.index'
        ]);
    }
}