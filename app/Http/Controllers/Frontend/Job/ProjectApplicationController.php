<?php namespace iJobDesk\Http\Controllers\Frontend\Job;

use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Auth;
use DB;
use Storage;
use Config;
use Session;
use Exception;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Role;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectInvitation;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\ProjectOffer;
use iJobDesk\Models\ProjectSkill;
use iJobDesk\Models\Contract;
use iJobDesk\Models\ContractMeter;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\Notification;
use iJobDesk\Models\Skill;
use iJobDesk\Models\File;
use iJobDesk\Models\UserSavedProject;
use iJobDesk\Models\Country;
use iJobDesk\Models\Language;
use iJobDesk\Models\UserStat;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\Settings;

class ProjectApplicationController {
	/**
	* View Applicants Page For Freelancer (job/application/{id})
	*
	* @author Ro Un Nam
	* @since Dec 28, 2017
	*/
	public function detail(Request $request, $application_id) {
		try {
			$user = Auth::user();
			
			$app = ProjectApplication::find($application_id);

			if (!$user->isAdmin()) {
				$app = ProjectApplication::findByUnique($application_id);
				$application_id = $app->id;
			}

			if ( !$app ) {
				return redirect()->route('job.my_proposals');
			}

			if ( $user->id != $app->user_id ) {
				return redirect()->route('job.my_proposals');
			}

			$job = $app->project;

			if ( $job->isSuspended() ) {
				return redirect()->route('job.my_proposals');
			}

			$is_affiliated = UserAffiliate::checkAffiliated($job->client_id, $user->id);

			$active_count = Contract::where('buyer_id', $job->client_id)
						        	->whereIn('status', [
						        		Contract::STATUS_OPEN, 
						        		Contract::STATUS_PAUSED,
						        		Contract::STATUS_SUSPENDED,
						        	])->count();

			$old_contracts_count = Contract::where('buyer_id', $job->client_id)
			    						   ->where('status', Contract::STATUS_CLOSED)
			    						   ->count();

	    	$job_post_count = Project::where('client_id', $job->client_id)->count();
	    	$contract_started_count = Contract::where('buyer_id', $job->client_id)->select('project_id')->distinct()->count();
			$opended_job_count = Project::where('client_id', $job->client_id)->where('status', Project::STATUS_OPEN)->count();
			$hired_count = Contract::where('buyer_id', $job->client_id)->count();

			if ( $request->isMethod('post') ) {
				if ($app->project->client->trashed())
					abort(404);
				
				$user->updateLastActivity();

				if ( $request->input('type') == 'T' ) { // Revise Terms

					if ( $job->isHourly() ) {
						$hourly_price = floatval($request->input('billing_hourly_rate'));
						if ( $hourly_price > MAX_HOURLY_PRICE ) {
							add_message(trans('message.freelancer.job.invite.error_max_hourly_price', ['amount' => formatCurrency(MAX_HOURLY_PRICE)]), 'danger');

							return redirect()->to(_route('job.application_detail', ['id' => $application_id]));  
						} else {
							$app->price = $request->input('billing_hourly_rate');
						}
					} else {
						$fixed_price = floatval($request->input('billing_fixed_rate'));
						if ( $fixed_price > MAX_FIXED_PRICE ) {
							add_message(trans('message.freelancer.job.invite.error_max_fixed_price', ['amount' => formatCurrency(MAX_FIXED_PRICE)]), 'danger');

							return redirect()->to(_route('job.application_detail', ['id' => $application_id]));  
						} else {
							$app->price = $request->input('billing_fixed_rate');
						}

						$app->duration = $request->input('duration');
					}

					$app->save();

					add_message(trans('message.freelancer.job.proposal.success_revise_term'), 'success');

					return redirect()->to(_route('job.application_detail', ['id' => $app->id]));
				} else if ( $request->input('type') == 'W' ) {
					$app->is_declined = ProjectApplication::IS_FREELANCER_DECLINED;
					$app->decline_reason = $request->input('reason');
					
					if ( $app->save() ) {

						EmailTemplate::send($app->project->client, 'PROPOSAL_WITHDRAWN', 2, [
							'USER' => $app->project->client->fullname(),
							'FREELANCER' => $app->user->fullname(),
							'JOB_POSTING' => $app->project->subject,
							'JOB_POSTING_URL' => _route('job.view', ['id' => $app->project->id], true, null, $app->project->client),
							'REASON' => $app->withdrawn_reason_string(),
						]);

						// Reset Connections
						$user_timestamp = strtotime(date('Y-m-d', strtotime($user->created_at)));
						$diff_timestamp = strtotime(date('Y-m-d')) - $user_timestamp;
						$cycle_timestamp = Settings::get('DAYS_RESET_CONNECTIONS') * 24 * 3600;
						$diff_int = intval($diff_timestamp / $cycle_timestamp);
						$cycle_start_timestamp = $diff_int * $cycle_timestamp + $user_timestamp;

						$cycle_start = date('Y-m-d', $cycle_start_timestamp);
						$cycle_end = date('Y-m-d', $cycle_start_timestamp + $cycle_timestamp);

						if ( $app->created_at >= $cycle_start && $app->created_at < $cycle_end ) {
							$connections = ProjectApplication::JOB_CONNECTIONS;

							// For featured job
							if ( $job->isFeatured() ) {
								$connections = Settings::get('CONNECTIONS_FEATURED_PROJECT');
							}

							if ( $app->is_featured ) {
								$connections = $connections * ProjectApplication::FEATURED_PROPOSAL_TIMES;
					    	}

							$user->stat->connects += $connections;

							$totalConnectionsReset = Settings::get('TOTAL_CONNECTIONS_RESET');
							if ( $user->stat->connects > $totalConnectionsReset ) {
								$user->stat->connects = $totalConnectionsReset;
							}

							$user->stat->save();
						}

						add_message(trans('message.freelancer.job.proposal.success_withdraw'), 'success');

						return redirect()->route('job.my_proposals');
					}
				}
			}

			return view('pages.freelancer.job.my_applicant', [
				'page' => 'freelancer.job.my_applicant',
				'job' => $job,
				'client' => $job->client,
				'application' => $app,
				'old_contracts_count' => $old_contracts_count,
				'job_count' => $job_post_count,
				'contract_started_count' => $contract_started_count,
				'opened_job_count' => $opended_job_count,
				'hired_count' => $hired_count,
				'active_count' => $active_count,
				'rate' => Settings::getRate($is_affiliated),
				'is_affiliated' => $is_affiliated,

				'j_trans' => [
					'please_enter_a_valid_number' => trans('common.please_enter_a_valid_number'), 
					'please_enter_a_value_less_than_or_equal_to_999' => trans('common.please_enter_a_value_less_than_or_equal_to_999'),
					'please_enter_a_value_less_than_or_equal_to_9999999' => trans('common.please_enter_a_value_less_than_or_equal_to_9999999'), 
					'please_enter_a_value_greater_than_or_equal_to_1' => trans('common.please_enter_a_value_greater_than_or_equal_to_1'),
					'MAX_FIXED_PRICE' => MAX_FIXED_PRICE,
					'MAX_HOURLY_PRICE' => MAX_HOURLY_PRICE,
				],
			]);

		} catch (Exception $e) {
			error_log('JobController.php [application_detail] - ' . $e->getMessage());
			return redirect()->route('job.my_proposals');
		}
	}
}