<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author Ro Un Nam
 * @since Dec 12, 2017
 */

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Config;
use iJobDesk\Models\Cronjob;

class CronjobsController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Cronjobs';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Cronjobs Overview
    *
    * @return Response
    */
    public function index(Request $request) {
        add_breadcrumb('Cronjobs');

        $action = $request->input('_action');
        if ( $action == 'CHANGE_STATUS' ) {
            $status = $request->input('status');
            $ids = $request->input('ids');
            
            $cronjobs = Cronjob::whereIn('id', $ids);

            if ( $status == Cronjob::STATUS_DISABLED ) {
                $cronjobs->update(['status' => Cronjob::STATUS_DISABLED]);
                add_message(sprintf('The %d Cronjob(s) has been disabled.', count($ids)), 'success');
            } else if ( $status == Cronjob::STATUS_READY ) {
                $cronjobs->update(['status' => Cronjob::STATUS_READY]);
                add_message(sprintf('The %d Cronjob(s) has been enabled.', count($ids)), 'success');
            } else if ( $status == Cronjob::STATUS_PROCESSING ) {
                // $cronjobs->update(['status'=> Cronjob::STATUS_PROCESSING]);
                foreach ( $cronjobs->get() as $cr ) {
                    switch ($cr->type) {
                        case Cronjob::TYPE_HOURLY_LOG_MAP:
                            Cronjob::crHourlyLogMap();
                            break;

                        case Cronjob::TYPE_PROCESS_REVIEW_TRANSACTIONS:
                        	Cronjob::crProcessReview();
							break;

						case Cronjob::TYPE_PROCESS_PENDING_TRANSACTIONS:
							Cronjob::crProcessPending();
							break;

						case Cronjob::TYPE_REVIEW_LAST_WEEK:
							Cronjob::crReviewLastWeek();
							break;

						case Cronjob::TYPE_PROCESS_PROJECTS:
							Cronjob::crProcessProjects();
							break;

                        case Cronjob::TYPE_PROCESS_CONTRACTS:
                            Cronjob::crProcessContracts();
                            break;

						case Cronjob::TYPE_PROCESS_USER_STATS:
							Cronjob::crProcessUserStat();
							break;

						case Cronjob::TYPE_PROCESS_USER_SKILL_POINTS:
							Cronjob::crProcessUserSkillPoints();
							break;

                        case Cronjob::TYPE_PROCESS_USER_POINTS:
                            Cronjob::crProcessUserPoints();
                            break;

                        case Cronjob::TYPE_UPDATE_USER_POINTS:
                            Cronjob::crUpdateUserPoints();
                            break;

						case Cronjob::TYPE_PROCESS_USER_CONNECTS:
							Cronjob::crResetConnects();
							break;

                        case Cronjob::TYPE_PROCESS_AFFILIATE_TRANSACTIONS:
                            Cronjob::crProcessAffiliate();
                            break;

                        case Cronjob::TYPE_PROCESS_DEPOSITS:
                            Cronjob::crProcessDeposits(true);
                            break;

                        case Cronjob::TYPE_CHECK_WITHDRAWS:
                            Cronjob::crCheckWithdraws();
                            break;

						case Cronjob::TYPE_PROCESS_SITE_WITHDRAWS:
                            Cronjob::crProcessSiteWithdraws();
                            break;

                        case Cronjob::TYPE_PROCESS_USER_PAYMENT_METHODS:
                            Cronjob::crProcessUserPaymentMethods();
                            break;

                        case Cronjob::TYPE_CHECK_AFFILIATE_TRANSACTIONS:
                            Cronjob::crCheckAffiliateTransactions();
                            break;

                        case Cronjob::TYPE_PROCESS_USER_CREDIT_CARDS:
                            Cronjob::crProcessUserCreditCards();
                            break;

                        case Cronjob::TYPE_PROCESS_WITHDRAWS:
                            Cronjob::crProcessWithdraws(); // ADDED AND CHANGED BY KCG, I have added the "forcely" parameter
                            break;

                        case Cronjob::TYPE_CHECK_TRANSACTIONS:
                            Cronjob::crCheckTransactions();
                            break;

                        case Cronjob::TYPE_JOB_RECOMMENDATION:
                            Cronjob::crJobRecommendation();
                            break;

                        case Cronjob::TYPE_PROCESS_USER_PROJECTS:
                            Cronjob::crProcessUserProjects();
                            break;

                        default:
                            break;
                    }
                }

                add_message(sprintf('The %d Cronjob(s) has been processed.', count($ids)), 'success');
            }
        }

        $cronjobs = Cronjob::leftJoin('cronjob_types AS ct', 'cronjobs.type', '=', 'ct.type');

        $sort = $request->input('sort', 'ct.name');
        $sort_dir = $request->input('sort_dir', 'asc');
        $filter = $request->input('filter');

        if ( isset($filter) && $filter['status'] != '' ) {
            $cronjobs->where('cronjobs.status', $filter['status']);
        }

		if ( isset($filter['type']) && $filter['type'] != '' ) {
			$cronjobs->where('ct.name', 'LIKE', '%'.trim($filter['type']).'%');
		}

		if ( isset($filter['max_runtime']) && $filter['max_runtime'] != '' ) {
			$cronjobs->where('cronjobs.max_runtime', '>=', $filter['max_runtime']);
		}

		if ( !empty($filter['done_at']) ) {
			if ( !empty($filter['done_at']['from']) ) {
				$cronjobs->where('cronjobs.done_at', '>=', date('Y-m-d H:i:s', strtotime($filter['done_at']['from'])));
			}

			if ( !empty($filter['done_at']['to']) ) {
				$cronjobs->where('cronjobs.done_at', '<=', date('Y-m-d H:i:s', strtotime($filter['done_at']['to']) + 24* 3600));
			}
		}

        $cronjobs = $cronjobs->orderBy($sort, $sort_dir)
                            ->orderBy('ct.name', 'asc')
                            ->select(['cronjobs.*', 'ct.name'])
                            ->get();

        $request->flashOnly('filter');

        return view('pages.admin.super.cronjobs.cronjobs', [
            'page' => 'super.cronjobs.cronjobs',
            'cronjobs' => $cronjobs,
			'sort'   => $sort,
			'sort_dir'   => '_' . $sort_dir,
			'filter' => $filter            
        ]);
    }
}