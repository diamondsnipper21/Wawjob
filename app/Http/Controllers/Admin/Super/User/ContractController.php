<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 14, 2017
 * Contracts Page for Freelancer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\Super\ContractController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\TransactionLocal;

use iJobDesk\Models\Views\ViewUser;

class ContractController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Contracts';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Contracts
    * @param $id The identifier of User
    *
    * @return Response
    */
    public function index(Request $request, $uid = null) {
        return parent::index($request, $uid);
    }

    public function detail(Request $request, $contract_id, $user_id = null ) {
        return parent::detail($request, $user_id, $contract_id);
    }
}