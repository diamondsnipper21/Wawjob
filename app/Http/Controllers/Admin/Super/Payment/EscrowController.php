<?php namespace iJobDesk\Http\Controllers\Admin\Super\Payment;
/**
 * @author KCG
 * @since July 24, 2017
 * Escrow Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Config;
use Auth;
use Log;

use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Reason;

class EscrowController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Escrow';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Escrow');

        try {
            if ( $request->method('post') ) {
                $action = $request->input('_action');

                if ($action == 'CHANGE_STATUS') {
                    $status = $request->input('fund_status');

                    $ids = $request->input('ids');

                    $success_count = $failure_count = 0;

                    if ( $status == ContractMilestone::FUND_PAID ) {
                        foreach ($ids as $id => $contract_id) {
                            if ( TransactionLocal::release($contract_id, $id) ) {
                                $success_count++;

								ContractMilestone::where('id', $id)
												->update([
													'performed_by' => ContractMilestone::PERFORMED_BY_SUPER_ADMIN
												]);
                            } else {
                                $failure_count++;
                            }
                        }

                        if ( $success_count ) {
                            add_message(sprintf('The %d milestone(s) has been released.', $success_count), 'success');
                        }

                        if ( $failure_count ) {
                            add_message(sprintf('The %d milestone(s) has not been released.', $failure_count), 'danger');
                        }
                    } else if ( $status == ContractMilestone::FUND_REFUNDED ) {
                        foreach ($ids as $id => $contract_id) {
                            if ( TransactionLocal::refund_fund($contract_id, $id, false, TransactionLocal::REFUND_REASON_BY_SUPER_ADMIN) ) {
                                $success_count++;

                                ContractMilestone::where('id', $id)
                                                ->update([
                                                    'performed_by' => ContractMilestone::PERFORMED_BY_SUPER_ADMIN
                                                ]);
                            } else {
                                $failure_count++;
                            }
                        }

                        if ( $success_count ) {
                            add_message(sprintf('The %d milestone(s) has been refunded.', $success_count), 'success');
                        }

                        if ( $failure_count ) {
                            add_message(sprintf('The %d milestone(s) has not been refunded.', $failure_count), 'danger');
                        }
                    }

    				// Add reason
    				$admin = Auth::user();
    				foreach ($ids as $id => $contract_id) {
    					$reason = new Reason;
    					$reason->message = $request->input('_reason');
    					$reason->reason = $request->input('_reason_option');
    					$reason->admin_id = $admin->id;
    					$reason->type = Reason::TYPE_CONTRACT_MILESTONE;
    					$reason->affected_id = $id;
    					$reason->action = ($status == ContractMilestone::FUND_PAID) ? Reason::ACTION_RELEASE : Reason::ACTION_REFUND;

    					$reason->save();
    				}
                }
            }
        } catch ( Exception $e ) {
            Log::error('[Admin - EscrowController.php::index()] Error: ' . $e->getMessage());
        }

        $totals = ContractMilestone::where('fund_status', ContractMilestone::FUNDED)
                                    ->where('transaction_id', '>', 0)
                                    ->sum('price');

        $escrows = ContractMilestone::leftJoin('contracts AS c', 'contract_milestones.contract_id', '=', 'c.id')
                    				->leftJoin('view_users AS b', 'c.buyer_id', '=', 'b.id')
                    				->leftJoin('view_users AS f', 'c.contractor_id', '=', 'f.id')
                    				->select('contract_milestones.*');

        $sort     = $request->input('sort', 'contract_milestones.created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        // Filtering
        $filter = $request->input('filter');

        if ( !isset($filter) ) {
            $filter = [];
            $filter['fund_status'] = ContractMilestone::FUNDED;
        }

        // By #ID
        if (isset($filter['id']) && $filter['id'] != '') {
            $escrows->where('contract_milestones.id', $filter['id']);
        }

        // By Contract Title
        if (!empty($filter['contract_title'])) {
            $escrows->where(function($query) use ($filter) {
                if ( is_numeric($filter['contract_title']) ) {
                    $query->where('c.id', intval($filter['contract_title']));
                } else {
                    $query->whereRaw('LOWER(c.title) LIKE "%' . trim($filter['contract_title']) . '%"')
                            ->orWhereRaw('LOWER(contract_milestones.name) LIKE "%' . trim($filter['contract_title']) . '%"');
                }
            });
        }

        // By Buyer Name or ID
        if (!empty($filter['buyer_name'])) {
            $escrows->where(function($query) use ($filter) {
                if ( is_numeric($filter['buyer_name']) ) {
                    $query->where('b.id', intval($filter['buyer_name']));
                } else {
                    $query->whereRaw('LOWER(b.fullname) LIKE "%' . trim(strtolower($filter['buyer_name'])) . '%"')
                          ->orWhereRaw('LOWER(b.username) LIKE "%' . trim(strtolower($filter['buyer_name'])) . '%"');
                }
            });
        }

        // By Freelancer Name
        if (!empty($filter['freelancer_name'])) {
            $escrows->where(function($query) use ($filter) {
                if ( is_numeric($filter['freelancer_name']) ) {
                    $query->where('b.id', intval($filter['freelancer_name']));
                } else {
                    $query->whereRaw('LOWER(f.fullname) LIKE "%' . trim(strtolower($filter['freelancer_name'])) . '%"')
                            ->orWhereRaw('LOWER(f.username) LIKE "%' . trim(strtolower($filter['freelancer_name'])) . '%"');
                }
            });
        }

        // By Amount
        if (!empty($filter['amount'])) {
            $escrows->whereRaw('CAST(contract_milestones.price AS INT) = ' . $filter['amount']);
        }

        // By Performed By
        if (isset($filter['performed_by']) && $filter['performed_by'] != '') {
            $escrows->where('performed_by', $filter['performed_by']);
        }

        // By Created Date
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $escrows->where('contract_milestones.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $escrows->where('contract_milestones.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Updated Date
        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $escrows->where('contract_milestones.updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $escrows->where('contract_milestones.updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24* 3600));
            }
        }

        // By Fund Status
        if (isset($filter) && $filter['fund_status'] != '') {
        	if ( $filter['fund_status'] == ContractMilestone::FUND_PAID ) {
        		$escrows->leftJoin('transactions', 'contract_milestones.contractor_transaction_id', '=', 'transactions.id')
        				->where('transactions.status', TransactionLocal::STATUS_DONE);
        	} else if ( $filter['fund_status'] == ContractMilestone::FUND_PENDING ) {
        		$escrows->leftJoin('transactions', 'contract_milestones.contractor_transaction_id', '=', 'transactions.id')
        				->where('transactions.status', TransactionLocal::STATUS_AVAILABLE);
        	} else {
            	$escrows->where('contract_milestones.fund_status', $filter['fund_status']);
            }
        } else {
            $escrows->whereIn('contract_milestones.fund_status', [
                ContractMilestone::FUNDED,
                ContractMilestone::FUND_PAID,
                ContractMilestone::FUND_REFUNDED,
            ]);
        }

        $escrows = $escrows->whereNotNull('c.id')
        					->orderBy($sort, $sort_dir)
                           	->orderBy('c.id', 'asc');

        $request->flashOnly('filter');

        return view('pages.admin.super.payment.escrows', [
            'page' => 'super.payment.escrows',
            'escrows' => $escrows->paginate($this->per_page),
            'totals' => $totals,

            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'filter' => $filter
        ]);
    }
}