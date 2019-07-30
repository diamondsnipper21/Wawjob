<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since July 12, 2017
 * Proposoals Page for Freelancer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use Auth;
use App;
use DB;

use iJobDesk\Models\User;
use iJobDesk\Models\ProjectApplication;

use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Views\ViewProjectMessage;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Reason;
use iJobDesk\Models\EmailTemplate;

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
        if (empty($id)) {
            add_breadcrumb('Proposals');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('Proposals');
        }
        
        return $this->listing($request, $id);
    }

    private function _listQuery($proposal_id = null) {
        $proposals = ProjectApplication::addSelect('project_applications.*')
                                       ->addSelect("bvu.fullname AS buyer_name")
                                       ->addSelect("fvu.fullname AS freelancer_name")
                                       ->addSelect("projects.subject AS project_title")
                                       ->addSelect("bvu.id AS buyer_id")
                                       ->addSelect("fvu.id AS freelancer_id")
                                       ->join('projects', 'projects.id', '=', 'project_applications.project_id')
                                       ->join('view_users AS fvu', 'fvu.id', '=', 'project_applications.user_id')
                                       ->join('view_users AS bvu', 'bvu.id', '=', 'projects.client_id')
                                       ->whereNull('projects.deleted_at') // Soft Deleted
                                       ->whereNull('project_applications.deleted_at') // Soft Deleted
        ;
        if (!empty($proposal_id))
            $proposals->where('project_applications.id', '=', $proposal_id);

        return $proposals;
    }

    protected function listing($request, $id = null) {
        $user = null;

        if (!empty($id)) {
            $user = ViewUser::find($id);

            if (!$user)
                abort(404);
        }

        $admin = Auth::user();

        $action = $request->input('_action');
        if ( !empty($action) ) {
            $ids = $request->input('ids');
            
            if ( $action == 'DELETE' ) {
                $deleted_ids = 0;
                $reason_text = $request->input('_reason');

                foreach ( $ids as $pid ) {
                	$proposal = ProjectApplication::find($pid);
                	$buyer = $proposal->project->user;
                	$freelancer = $proposal->user;
                	$buyer_name = $buyer->fullname();
                	$freelancer_name = $freelancer->fullname();
                	$project_name = $proposal->project->subject;
                	$project_url = _route('job.view', ['id' => $proposal->project->id], true, null, $buyer);

                    if ( $proposal && $proposal->delete() ) {
						EmailTemplate::send($buyer, 'PROPOSAL_DELETED', 0, [
							'USER' => $buyer_name,
							'PROPOSAL_SENDER_NAME' => $freelancer_name,
							'JOB_POSTING' => $project_name,
							'JOB_POSTING_URL' => $project_url,
							'REASON' => $reason_text
						]);

						EmailTemplate::send($freelancer, 'PROPOSAL_DELETED', 0, [
							'USER' => $freelancer_name,
							'PROPOSAL_SENDER_NAME' => $freelancer_name,
							'JOB_POSTING' => $project_name,
							'JOB_POSTING_URL' => $project_url,
							'REASON' => $reason_text
						]);

                        // Add reason
                        $reason = new Reason;
                        $reason->message = $reason_text;
                        $reason->admin_id = $admin->id;
                        $reason->type = Reason::TYPE_PROPOSAL;
                        $reason->affected_id = $pid;
                        $reason->action = Reason::ACTION_DELETE;
                        $reason->save();

                        $deleted_ids++;
                    }
                }

                add_message(sprintf('The %d Proposal(s) has been deleted.', $deleted_ids), 'success');
            }
        }

        $sort     = $request->input('sort', 'created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $proposals = $this->_listQuery()
                          ->orderBy($sort, $sort_dir);

        if (!empty($id))
            $proposals->where('project_applications.user_id', '=', $id);

        // Filtering
        $filter = $request->input('filter');

        // By Id
        if (!empty($filter['id'])) {
            $proposals->where('project_applications.id', $filter['id']);
        }

        // By Type
        if (isset($filter) && $filter['type'] != '') {
            $proposals->where('project_applications.type', $filter['type']);
        }

        // By Created Date
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $proposals->where('project_applications.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $proposals->where('project_applications.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Project Id or Title
        if (!empty($filter['project_title'])) {
            $proposals->where(function($query) use ($filter) {
                $query->orWhere('projects.subject', 'LIKE', '%'.trim($filter['project_title']).'%')
                      ->orWhere('projects.id', '=', $filter['project_title']);
            });
        }

        // By Buyer Name OR #ID
        if (!empty($filter['buyer_name'])) {
            $proposals->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(bvu.fullname) LIKE "%' . trim(strtolower($filter['buyer_name'])) . '%"')
                      ->orWhere('bvu.id', '=', $filter['buyer_name']);
            });
        }

        // By Freelancer Name OR #ID
        if (!empty($filter['freelancer_name'])) {
            $proposals->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(fvu.fullname) LIKE "%' . trim(strtolower($filter['freelancer_name'])) . '%"')
                      ->orWhere('fvu.id', '=', $filter['freelancer_name']);
            });
        }

        // By Invited
        if (isset($filter) && $filter['invited'] != '') {
            $proposals->where('project_applications.provenance', $filter['invited'] == 0?'<>':'=', ProjectApplication::PROVENANCE_INVITED);
        }

        // By Status
        if (isset($filter) && $filter['status'] != '') {
            if ($filter['status'] == ProjectApplication::STATUS_WITHDRAWN)
                $proposals->where('project_applications.is_declined', '>', 0);
            else {
                $proposals->whereRaw("project_applications.status IN ({$filter['status']})")
                          ->where('project_applications.is_declined', 0);
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.proposals', [
            'page' => 'super.'.(!empty($id)?'user.freelancer.':'').'proposals',
            'proposals' => $proposals->paginate($this->per_page),

            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,

            'user' => $user
        ]);
    }

    public function detail(Request $request, $proposal_id, $user_id = null) {
        $user = null;

        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        $proposal = ProjectApplication::find($proposal_id);

        if (!$proposal)
            abort(404);

        $contract = Contract::where('application_id', $proposal->id)
                            ->first();

        $milestone_total_price = 0;
        if ($contract) { // 
            foreach($contract->milestones as $milestone)
                $milestone_total_price += $milestone->price;
        }

        // messages
        $messages = ViewProjectMessage::where('proposal_id', '=', $proposal_id)
                                      ->orderBy('created_at', 'asc');

        // Add bread crumb
        if (empty($user_id)) {
            add_breadcrumb('Proposals', route('admin.super.proposals'));
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('Proposals', route('admin.super.user.freelancer.proposals', ['id' => $user_id]));
        }

        add_breadcrumb($proposal->project->subject);

        return view('pages.admin.super.proposal.detail', [
            'page' => 'super.'.(!empty($user_id)?'user.freelancer.':'').'proposal.detail',
            
            'proposal' => $proposal,
            'contract' => $contract,
            // 'messages' => $messages->paginate($this->per_page),
            'messages' => $messages->paginate(3),

            'milestone_total_price' => $milestone_total_price,

            'page_title' => 'Proposal Detail',
            'user' => $user
        ]);
    }
}