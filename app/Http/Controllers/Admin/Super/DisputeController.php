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
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\HourlyLogMap;

class DisputeController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Dispute';
        $user = Auth::user();

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Show dispute list.
    *
    * @return Response
    */
    public function index(Request $request) {
        $user = Auth::user();

        add_breadcrumb('Dispute');
        
        // sort
        $sort     = $request->input('sort', 'tickets.status');
        $sort_dir = $request->input('sort_dir', 'asc');

        $tickets = Ticket::where('tickets.type', Ticket::TYPE_DISPUTE)
                         ->join('contracts AS c', 'c.id', '=', 'tickets.contract_id')
                         ->join('projects', 'projects.id', '=', 'c.project_id')
                         ->join('view_users AS bv', 'bv.id', '=', 'projects.client_id')
                         ->join('view_users AS fv', 'fv.id', '=', 'c.contractor_id')
                         ->join('view_users AS cv', 'cv.id', '=', 'tickets.user_id')
                         ->addSelect('tickets.*')
                         ->addSelect("bv.fullname AS buyer")
                         ->addSelect("bv.id AS buyer_id")
                         ->addSelect("fv.fullname AS contractor")
                         ->addSelect("fv.id AS contractor_id")
                         ->addSelect("cv.fullname AS creator")
                         ->addSelect("c.title AS title")
                         // ->whereNull('bv.deleted_at') // soft deleted
                         // ->whereNull('fv.deleted_at') // soft deleted
                         // ->whereNull('cv.deleted_at') // soft deleted
                         ->whereNull('projects.deleted_at') // soft deleted
                         ->orderBy($sort, $sort_dir)
                         ->orderBy('tickets.created_at', 'desc');

        $queryBuilder = clone $tickets;

        // Filtering
        $filter = $request->input('filter');

        // By Action Result
        if (!empty($filter['result']) && $filter['result'] == 'active') {
            $tickets->whereNull('tickets.archive_type');
        }
        elseif (!empty($filter['result']) && $filter['result'] == 'archived') {
            $tickets->whereNotNull('tickets.archive_type');
        }

        // By Contract Title or #ID
        if (!empty($filter['title'])) {
            $tickets->where(function($query) use ($filter) {
                $query->orWhere('c.title', 'LIKE', '%'.trim($filter['title']).'%')
                      ->orWhere('c.id', '=', $filter['title']);
            });
        }

        // By #ID
        if (!empty($filter['id'])) {
            $tickets->where('tickets.id', trim($filter['id']));
        }

        // By Buyer Name
        if (!empty($filter['buyer'])) {
            $tickets->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(bv.fullname) LIKE "%'.trim(strtolower($filter['buyer'])).'%"')
                      ->orWhere('bv.id', '=', $filter['buyer']);
            });
        }

        // By Contractor Name
        if (!empty($filter['freelancer'])) {
            $tickets->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(fv.fullname) LIKE "%'.trim(strtolower($filter['freelancer'])).'%"')
                      ->orWhere('fv.id', '=', $filter['freelancer']);
            });
        }

        // By Creator Name
        if (!empty($filter['creator'])) {
            $tickets->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(cv.fullname) LIKE "%'.trim(strtolower($filter['creator'])).'%"')
                      ->orWhere('cv.id', '=', $filter['creator']);
            });
        }

        // By Period
        if (!empty($filter['date_posted'])) {
            if (!empty($filter['date_posted']['from'])) {
                $tickets->where('tickets.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['date_posted']['from'])));
            }

            if (!empty($filter['date_posted']['to'])) {
                $tickets->where('tickets.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['date_posted']['to']) + 24* 3600));
            }
        }

        // By Status
        if (!empty($filter) && array_key_exists('status', $filter) && $filter['status'] != '') {
            $tickets->where('tickets.status', $filter['status']);
        }

        $request->flashOnly('filter');

        $queryBuilder1 = clone $queryBuilder;
        $queryBuilder2 = clone $queryBuilder;

        return view('pages.admin.super.dispute.dispute', [
            'page'   => 'super.dispute.dispute',
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'disputes' => $tickets->paginate($this->per_page),
            'config' => Config::get('settings'),
            'opened_disputes' => $queryBuilder1->whereIn('tickets.status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED])
                                               ->count(),
            'unassigned_disputes' => $queryBuilder2->whereIn('tickets.status', [Ticket::STATUS_OPEN])
                                                   ->count()
        ]);
    }

    public function determine(Request $request, $ticket_id) {
        $me = Auth::user();

        $archive_type = $request->input('archive_type');
        $reason = $request->input('reason');

        $ticket = Ticket::findOrFail($ticket_id);
        $contract = $ticket->contract;

        if ($request->isMethod('POST')) {
            $ticket->archive_type       = $archive_type;
            $ticket->reason             = $reason;
            $ticket->dispute_winner_id  = ($archive_type == Ticket::RESULT_PUNISH_BUYER?
                                                $contract->contractor_id:
                                          ($archive_type == Ticket::RESULT_PUNISH_FREELANCER?
                                                $contract->buyer_id:
                                                null
                                          ));
            $ticket->status             = Ticket::STATUS_SOLVED;
            $ticket->admin_id           = (!empty($ticket->admin_id)?$ticket->admin_id:$me->id);
            $ticket->assigner_id        = (!empty($ticket->assigner_id)?$ticket->assigner_id:$me->id);
            $ticket->ended_at           = date('Y-m-d H:i:s');

            $success = true;
            $old_contract_status = $contract->status;

            $contractor = $contract->contractor; // contractor
            $buyer      = $contract->buyer; // buyer

            // Punish Buyer
            if ($archive_type == Ticket::RESULT_PUNISH_BUYER) {
                // 1. end contract
                $contract->status = Contract::STATUS_CLOSED;

                $amount = 0;
                $dis_amount = 0;
                if ($contract->isFixed()) {
                    // 2. escrow will be released to freelancer
                    $milestones = $contract->getFundedMilestones();
                    foreach ($milestones as $milestone) {
                        $result = TransactionLocal::release($contract->id, $milestone->id, true);

                        // Fail
                        if (!$result['success']) {
                            $success = false;
                            $dis_amount += $result['amount'];
                        } else {
                            $amount += $result['amount'];
                        }
                    }

                    if (!$success) {
                        add_message(sprintf('There is no enough budget in buyer\'s account. The buyer (%s) must pay $%.2f more.', $contract->buyer->fullname(), $dis_amount), 'danger');
                    }
                }

                $_POST['_reason'] = sprintf('The buyer (%s) has been punished. $%.2f have been paid to the Freelancer (%s).', $contract->buyer->fullname(), $amount, $contract->contractor->fullname());
            } elseif ($archive_type == Ticket::RESULT_PUNISH_FREELANCER) {
                // 1. end contract
                $contract->status = Contract::STATUS_CLOSED;

                $amount = 0;
                $dis_amount = 0;
                if ($contract->isFixed()) {
                    // 2. escrow will be refund to buyer
                    $milestones = $contract->getFundedMilestones();
                    foreach ($milestones as $milestone) {
                        $result = TransactionLocal::refund_fund($contract->id, $milestone->id, true, TransactionLocal::REFUND_REASON_DISPUTE_PUNISHED);

                        // Fail
                        if (!$result['success']) {
                            $success = false;
                            $dis_amount += $result['amount'];
                        } else {
                            $amount += $result['amount'];
                        }
                    }

                    if (!$success) {
                        add_message('We can\'t refund for this contractor.', 'danger');
                    }
                } else { // Hourly. The money in this week can't move to "IN REVIEW"
                    $amount = HourlyLogMap::disableInThisWeek($contract);
                }

                $_POST['_reason'] = sprintf('The freelancer(%s) has been punished. $%.2f have been refunded to the Buyer (%s).', $contract->contractor->fullname(), $amount, $contract->buyer->fullname());
            } elseif ($archive_type == Ticket::RESULT_SOLVED_THEMSELVES) {
                $contract->status = Contract::STATUS_OPEN;

                $_POST['_reason'] = sprintf('No punishment. Dispute has been solved themselves.');
            }

            if (!$success) { // if dispute is failed, contract won't close.
                $contract->status = $old_contract_status;
                
                // if failed to release to freelancer when punish to buyer, buyer will be suspended financially.
                if ($archive_type == Ticket::RESULT_PUNISH_BUYER) {
                    $buyer->status = User::STATUS_FINANCIAL_SUSPENDED;

                    $buyer->save();
                } else {
                    $contractor->status = User::STATUS_FINANCIAL_SUSPENDED;
                    $contractor->save();
                }

                add_message('It failed to solve this dispute', 'danger');
            } else { // Freelancer will release from suspension if freelancer hasn't another dispute
                if ($archive_type == Ticket::RESULT_PUNISH_BUYER)
                    $contract->buyer_need_leave_feedback = 0;
                elseif ($archive_type == Ticket::RESULT_PUNISH_FREELANCER)
                    $contract->freelancer_need_leave_feedback = 0;
                        
                $contract->save();
                $ticket->save(); // if success, ticket will be solved successfully.

                $contractor->status             = User::STATUS_AVAILABLE;
                $contractor->save();
            }

            if ($contract->status == Contract::STATUS_CLOSED) {
                $contract->closeAndLeaveFeedback($request);
            }

            unset($_POST['_reason']);
        }

        $buyer_id       = $contract->buyer_id;
        $freelancer_id  = $contract->contractor_id;

        $buyer_dispute_counts       = Ticket::where('tickets.type', Ticket::TYPE_DISPUTE)
                                            ->where('tickets.user_id', $buyer_id)
                                            ->count();
        $buyer_dispute_win_counts   = Ticket::where('tickets.type', Ticket::TYPE_DISPUTE)
                                            ->where('tickets.dispute_winner_id', $buyer_id)
                                            ->count();

        $freelancer_dispute_counts      = Ticket::where('tickets.type', Ticket::TYPE_DISPUTE)
                                                ->where('tickets.user_id', $freelancer_id)
                                                ->count();
        $freelancer_dispute_win_counts  = Ticket::where('tickets.type', Ticket::TYPE_DISPUTE)
                                                ->where('tickets.dispute_winner_id', $freelancer_id)
                                                ->count();

        return view('pages.admin.super.dispute.determine_modal', [            
            'ticket'        => $ticket,
            'contract'      => $contract,

            'buyer_dispute_counts'          => $buyer_dispute_counts,
            'buyer_dispute_win_counts'      => $buyer_dispute_win_counts,
            'freelancer_dispute_counts'     => $freelancer_dispute_counts,
            'freelancer_dispute_win_counts' => $freelancer_dispute_win_counts
        ]);
    }
    
}