<?php namespace iJobDesk\Http\Controllers\Admin\Super\User\Buyer;
/**
 * @author KCG
 * @since July 12, 2017
 * Jobs Page for Buyer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\Super\JobController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;

class JobController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Job Postings';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Jobs
    * @param $id The identifier of User
    *
    * @return Response
    */
    public function index(Request $request, $user_id = null) {
        return parent::index($request, $user_id);
    }

    public function overview(Request $request, $user_id, $job_id = null) {
        return parent::overview($request, $job_id, $user_id);
    }

    public function invitation(Request $request, $user_id, $job_id = null) {
        return parent::invitation($request, $job_id, $user_id);
    }

    public function proposal(Request $request, $user_id, $id = null, $page = '') {
        return parent::proposal($request, $id, $page, $user_id);
    }

    public function interview(Request $request, $user_id, $id = null, $page = '') {
        return parent::interview($request, $id, $page, $user_id);
    }

    public function hire_offers(Request $request, $user_id, $id = null) {
        return parent::hire_offers($request, $id, $user_id);
    }

    public function action_history(Request $request, $user_id = null, $id = null) {
        return parent::action_history($request, $id, $user_id);
    }
}