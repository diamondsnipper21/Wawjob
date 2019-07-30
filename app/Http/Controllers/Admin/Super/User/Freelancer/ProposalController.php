<?php namespace iJobDesk\Http\Controllers\Admin\Super\User\Freelancer;
/**
 * @author KCG
 * @since July 12, 2017
 * Proposoals Page for Freelancer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\Super\ProposalController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\ProjectApplication;

use iJobDesk\Models\Views\ViewUser;

class ProposalController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Proposals';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Proposals
    * @param $id The identifier of User
    *
    * @return Response
    */
    public function index(Request $request, $id = null) {
        return parent::index($request, $id);
    }

    public function detail(Request $request, $user_id, $proposal_id = null) {
        return parent::detail($request, $proposal_id, $user_id);
    }
}