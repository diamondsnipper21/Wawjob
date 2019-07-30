<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since July 14, 2017
 * Contracts Page for Freelancer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use Auth;
use App;
use DB;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\ActionHistory;
use iJobDesk\Models\Project;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\ContractAction;
use iJobDesk\Models\EmailTemplate;

use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Views\ViewProjectMessage;

use iJobDesk\Http\Controllers\ContractController as Controller;

class ContractController extends BaseController {

    private $controller;

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Contracts';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);
        
        $this->controller = new Controller();
        $this->controller->beforeAction($request);
    }

    public function index(Request $request, $id = null) {

        if ($id == null) {
            add_breadcrumb('Contracts');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('Contracts');
        }
        
        return $this->listing($request, $id);
    }

    /**
    * Contracts
    * @param $id The identifier of User
    *
    * @return Response
    */
    protected function listing(Request $request, $uid = null) {
        $user = null;

        if (!empty($uid)) {
            $user = ViewUser::find($uid);

            if (!$user)
                abort(404);
        }

        $action = $request->input('_action');
        $reason = $request->input('_reason');

        if ($action == 'CHANGE_STATUS') {
            $status = $request->input('status');
            $ids = $request->input('ids');

            foreach ($ids as $id) {
                $contract = Contract::find($id);

                $contract->status = $status;

                $contract->save();

                if ($status == Contract::STATUS_SUSPENDED) {
                    $reasons = [
                        'me'         => trans('contract.contract_suspension_reason_by_admin', ['reason' => $reason]),
                        'buyer'      => trans('contract.contract_suspension_reason_by_admin', ['reason' => $reason]),
                        'freelancer' => trans('contract.contract_suspension_reason_by_admin', ['reason' => $reason]),
                    ];
                    $contract->suspend($reasons);
                } else if ($status == Contract::STATUS_OPEN) {
                    EmailTemplate::send($contract->buyer, 'CONTRACT_ACTIVATED', 0, [
                        'USER'           => $contract->buyer->fullname(),
                        'CONTRACT_TITLE' => $contract->title,
                        'CONTRACT_LINK'  => _route('contract.contract_view', ['id' => $contract->id], true, null, $contract->buyer)
                    ]);

                    EmailTemplate::send($contract->contractor, 'CONTRACT_ACTIVATED', 0, [
                        'USER'           => $contract->contractor->fullname(),
                        'CONTRACT_TITLE' => $contract->title,
                        'CONTRACT_LINK'  => _route('contract.contract_view', ['id' => $contract->id], true, null, $contract->contractor)
                    ]);
                }
            }
        }

        $sort     = $request->input('sort', 'contracts.id');
        $sort_dir = $request->input('sort_dir', 'desc');

        $contracts = Contract::addSelect('contracts.*')
                             ->addSelect("bv.fullname AS buyer_name")
                             ->addSelect("fv.fullname AS contractor_name")
                             ->join('projects', 'projects.id', '=', 'contracts.project_id')
                             ->join('view_users AS bv', 'bv.id', '=', 'projects.client_id')
                             ->join('view_users AS fv', 'fv.id', '=', 'contracts.contractor_id')
                             ->whereNull('projects.deleted_at') // soft deleted
                             ->whereIn('contracts.status', Contract::onlyContractStatus())
                             ->orderBy($sort, $sort_dir);

        if ($uid != null) {
            // $contracts->whereNull('bv.deleted_at')
            //           ->whereNull('fv.deleted_at');
                      
            if ($user->isFreelancer())
                $contracts->where('contracts.contractor_id', '=', $uid);
            if ($user->isBuyer())
                $contracts->where('contracts.buyer_id', '=', $uid);
        }

        // Filtering
        $filter = $request->input('filter');

        // By ID
        if (isset($filter) && $filter['id'] != '') {
            $contracts->where('contracts.id', $filter['id']);
        }

        // By Type
        if (isset($filter) && $filter['type'] != '') {
            $contracts->where('contracts.type', $filter['type']);
        }

        // By Contract Title
        if (!empty($filter['title'])) {
            $contracts->where('contracts.title', 'LIKE', '%'.trim($filter['title']).'%');
        }

        // By Contractor Name OR #ID
        if (!empty($filter['contractor_name'])) {
            $contracts->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(fv.fullname) LIKE "%'.trim(strtolower($filter['contractor_name'])).'%"')
                      ->orWhere('fv.id', '=', $filter['contractor_name']);
            });
        }

        // By Buyer Name OR #ID
        if (!empty($filter['buyer_name'])) {
            $contracts->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(bv.fullname) LIKE "%'.trim(strtolower($filter['buyer_name'])).'%"')
                      ->orWhere('bv.id', '=', $filter['buyer_name']);
            });
        }

        // By Period
        if (!empty($filter['period'])) {
            if (!empty($filter['period']['from'])) {
                $contracts->where('contracts.started_at', '>=', date('Y-m-d H:i:s', strtotime($filter['period']['from'])));
            }

            if (!empty($filter['period']['to'])) {
                $contracts->where('contracts.ended_at', '<=', date('Y-m-d H:i:s', strtotime($filter['period']['to']) + 24* 3600));
            }
        }

        // By Status
        if (isset($filter) && $filter['status'] != '') {
            $contracts->where('contracts.status', $filter['status']);
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.contracts', [
            'page' => 'super.'.(!empty($uid)?'user.commons.':'').'contracts',
            //'page' => 'super.contracts',
            'user' => $user,
            'contracts' => $contracts->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'userId' => $uid,
        ]);  
    }

    public function detail(Request $request, $contract_id, $user_id = null) {
        if (empty($user_id)) {
            add_breadcrumb('Contracts', route('admin.super.contracts'));
            add_breadcrumb('Overview');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Contracts', route('admin.super.user.contracts', ['user_id' => $user_id]));
            add_breadcrumb('Overview');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        view()->share([
            'page_title' => $this->page_title,
            'user' => $user,
            'user_id' => $user_id
        ]);

        $this->action_history($request, $contract_id, $user_id);
        
        return $this->controller->contract_view($request, $contract_id, $user_id);
    }

    public function action_history(Request $request, $contract_id, $user_id = null) {

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        try {

            $sort     = $request->input('sort', 'action_histories.created_at');
            $sort_dir = $request->input('sort_dir', 'desc');

            $histories = ActionHistory::addSelect('action_histories.*')
                                      ->addSelect('du.fullname AS doer_fullname')
                                      ->addSelect('du.id AS doer_id')
                                      ->join('view_users AS du', 'du.id', '=', 'action_histories.doer_id')
                                      ->join('contracts AS c', 'c.id', '=', 'action_histories.target_id')
                                      ->where('c.id', $contract_id)
                                      ->where('action_histories.type', ActionHistory::TYPE_CONTRACT)
                                      ->orderBy($sort, $sort_dir);

            // Filtering
            $filter = $request->input('filter');

            // By Action Type
            if (!empty($filter['type'])) {
                $histories->whereRaw("action_type LIKE '%{$filter['type']}%'");
            }

            // By Message
            if (!empty($filter['description'])) {
                $histories->whereRaw("description LIKE '%{$filter['description']}%'");
            }

            // By Doer
            if (!empty($filter['doer_fullname'])) {
                $histories->whereRaw("du.fullname LIKE '%{$filter['doer_fullname']}%'");
            }

            // By Create At
            if (!empty($filter['created_at'])) {
                if (!empty($filter['created_at']['from'])) {
                    $histories->where('action_histories.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
                }

                if (!empty($filter['created_at']['to'])) {
                    $histories->where('action_histories.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
                }
            }

            $request->flashOnly('filter');

            $contract = Contract::findOrFail($contract_id);

            view()->share([
                'contract'      => $contract,
                'user'          => $user,
                'user_id'       => $user_id,
                'sort'          => $sort,
                'sort_dir'      => '_' . $sort_dir,
                'histories'     => $histories->paginate($this->per_page)
            ]);
                                        
            return view('pages.admin.super.contract.action_history', [
                'page'          => 'super.contract.action_history'
            ]); 
            
        } catch(Exception $e) {
            return redirect()->route('admin.super.contract', ['id' => $contract_id]);
        }
    }
}