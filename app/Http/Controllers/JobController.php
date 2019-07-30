<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;
use iJobDesk\Http\Controllers\Frontend\Job\ProjectApplicationController;
use iJobDesk\Http\Controllers\Frontend\Job\JobDetailController;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Auth;
use DB;
use Storage;
use Config;
use Session;
use Exception;
use Log;
use Validator;

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

class JobController extends Controller {

	/**
	* Create Job Page (job/create)
	*
	* @author nada
	* @since Jan 28, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function create(Request $request) {
		return $this->edit_job($request, null);
	}

	public function all_jobs(Request $request, $type = 'open') {
		$user = Auth::user();

		$settings = Config::get('settings');
		$per_page = $settings['buyer']['my_job']['per_page'];

		if ($type == 'draft')
			$status_array = [Project::STATUS_DRAFT];
		
		// Get offers
		$offers = Contract::where('buyer_id', $user->id)
				          ->where('status', Contract::STATUS_OFFER)
				          ->orderBy('status', 'asc')
				          ->orderBy('created_at', 'desc')
				          ->paginate($per_page);
		
        // Get contracts
        $open_contracts = Contract::where('buyer_id', $user->id)
						          ->whereIn('status', [
						          		Contract::STATUS_OPEN, 
						          		Contract::STATUS_PAUSED, 
						          		Contract::STATUS_SUSPENDED
						          ])
						          ->orderBy('status', 'asc')
						          ->orderBy('created_at', 'desc')
						          ->paginate($per_page);

		// Get job postings
		if ($type == 'draft')
			$open_jobs = Project::where('client_id', $user->id)
								->where('status', Project::STATUS_DRAFT)
								// ->where('accept_term', Project::ACCEPT_TERM_YES)
								->orderBy('id', 'desc')
								->paginate($per_page);
		elseif ($type == 'archived')
			$open_jobs = Project::where('client_id','=', $user->id)
								->whereIn('status', [
									Project::STATUS_CANCELLED,
									Project::STATUS_CLOSED
								])
								->where('accept_term', Project::ACCEPT_TERM_YES)
								->orderBy('updated_at', 'desc')
								->paginate($per_page);
		else {
			$open_jobs = Project::where('client_id', $user->id)
								->whereIn('status', [Project::STATUS_OPEN, Project::STATUS_SUSPENDED])
								->where('accept_term', Project::ACCEPT_TERM_YES)
								->orderBy('id', 'desc')
								->paginate($per_page);
		}

		return view('pages.buyer.job.all_jobs', [
			'page' => 'buyer.job.all_jobs',
			
			'open_jobs' => $open_jobs,
			'open_contracts' => $open_contracts,
			'offers' => $offers,

			'type' => $type,

			'j_trans' => [
				'close_job' => trans('j_message.buyer.job.status.close_job'), 
				'cancel_job' => trans('j_message.buyer.job.status.cancel_job'), 
				'delete_job' => trans('j_message.buyer.job.status.delete_job'), 
				'delete_draft' => trans('j_message.buyer.job.status.delete_draft'), 
				'change_public' => trans('j_message.buyer.job.status.change_public'), 
				'app_declined' => trans('j_message.buyer.job.status.app_declined'), 
				'withdraw_offer' => trans('message.buyer.job.offer.success_withdraw'), 
				'status' => [
					'private' => strtolower(trans('common.private')), 
					'public'  => strtolower(trans('common.public')),
					'protected'  => strtolower(trans('common.protected')),
					'featured' => trans('common.featured'),
					'not_featured' => trans('common.not_featured')
				], 
			]
		]);
	}

	/**
	* View Job Page (job/{id})
	*
	* @author nada
	* @since Feb 23, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function view_job(Request $request, $job_id) {
		$controller = new JobDetailController();
		return $controller->index($request, $job_id);
	}

	/**
	* View Job Page (job/{id}/feedbacks/{page})
	*
	*/
	public function load_ended_contracts(Request $request, $user_id, $page) {
		$controller = new JobDetailController();
		return $controller->load_ended_contracts($request, $user_id, $page);
	}

	/**
	* Edit Job Page (job/{id}/edit)
	*
	* @author KCG
	* @since 2017-06-08
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function edit_job(Request $request, $job_id, $action = '') {
		$controller = new JobDetailController();
		return $controller->edit($request, $job_id, $action);
	}

	/**
	* View Applicants Page For Freelancer (job/application/{id})
	*
	* @author Ro Un Nam
	* @since Dec 28, 2017
	*/
	public function application_detail(Request $request, $application_id) {
		$controller = new ProjectApplicationController();
		return $controller->detail($request, $application_id);
	}

	/**
	* Search Job Skills (job/search-skills) [AJAX]
	*
	* @author Ro Un Nam
	* @since May 16, 2017
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function search_job_skills(Request $request)
	{
		$result = [];

		if ( $request->input('ids') ) {
			$skills = Skill::whereIn('id', explode(',', $request->input('ids')))
						   ->get();
		} else {
			$skills = Skill::where('name', 'LIKE', '%' . $request->input('q') . '%')
						   ->orderby('name', 'asc')
						   ->get();
		}

		if ( count($skills) > 0 ) {
			foreach ( $skills as $skill ) {
				$result[] = [
					'id' => $skill->id, 
					'text' => $skill->name, 
					'title' => $skill->name
				];
			}
		}

		return response()->json($result);
	}

	/**
	* Search Locations (job/search-locations) [AJAX]
	*
	* @author Ro Un Nam
	* @since May 23, 2017
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function search_locations(Request $request)
	{
		$result = [];

		if ( $request->input('ids') ) {
			$countries = Country::whereIn('charcode', explode(',', $request->input('ids')))->get();
		} else {
			$countries = Country::where('name', 'like', '%' . $request->input('q') . '%')->orderby('name', 'asc')->get();
		}

		if ( count($countries) > 0 ) {
			foreach ( $countries as $country ) {
				$result[] = [
					'id' => $country->charcode, 
					'text' => $country->name, 
					'name' => $country->name
				];
			}
		}

		return response()->json($result);
	}

	/**
	* Search Languages (job/search-languages) [AJAX]
	*
	* @author Ro Un Nam
	* @since May 23, 2017
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function search_languages(Request $request)
	{
		$result = [];

		if ( $request->input('id') ) {
			$language = Language::where('id', $request->input('id'))->first();

			if ( $language ) {
				$result['id'] = $language->id;
				$result['name'] = $language->name;
			}

			return response()->json($result);
		} else if ( $request->input('ids') ) {
			$languages = Language::whereIn('id', explode(',', $request->input('ids')))->get();
		} else {
			$languages = Language::where('name', 'like', '%' . $request->input('q') . '%')->orderby('name', 'asc')->get();
		}

		if ( count($languages) > 0 ) {
			foreach ( $languages as $lang ) {
				$result[] = [
					'id' => $lang->id, 
					'text' => $lang->name, 
					'name' => $lang->name
				];
			}
		}

		return response()->json($result);
	}

	/**
	* Change Job Status (job/{id}/change_status/{status}) [AJAX]
	*
	* @author Ro Un Nam
	* @since Dec 28, 2017
	*/
	public function change_status(Request $request, $job_id, $status, $user = null)
	{
		$result = [
			'success' => false, 
			'message' => '',
		];

		if (!$user)
			$user = Auth::user();

		try {
			$job = Project::find($job_id);
			if (!Auth::user()->isAdmin()) {
				$job = Project::findByUnique($job_id);
				$job_id = $job->id;
			}

			if ( !$job ) {
				abort(404);
			}

			if ( !$job->isSuspended() && $job->checkIsAuthor($user->id) ) {

				$status = intval($status);

				if ( $status == Project::STATUS_CLOSED || $status == Project::STATUS_CANCELLED ) {
					if ( $status == Project::STATUS_CLOSED ) {
						Notification::send(Notification::BUYER_JOB_CLOSED, 
						 	SUPERADMIN_ID, 
						 	$user->id,
						 	['job_title' => sprintf('%s', $job->subject)]
						);
					} else {
						Notification::send(Notification::BUYER_JOB_CANCELLED, 
						 	SUPERADMIN_ID, 
						 	$user->id,
						 	['job_title' => sprintf('%s', $job->subject)]
						);					
					}

					$buyer_name = $user->fullname();
					$project_name = $job->subject;
					$project_url = _route('job.view', ['id' => $job->id]);

					// Send email to freelancers applied to this job
					$active_applications = $job->applications()
											   ->whereIn('status', [
													ProjectApplication::STATUS_NORMAL,
													ProjectApplication::STATUS_ACTIVE
												])
											   ->get();

					if ( count($active_applications) ) {
						$search_job_url = route('search.job');

						foreach ( $active_applications as $application ) {
							if ( $status == Project::STATUS_CLOSED ) {
								if ( $application->user->userNotificationSetting->applied_job_is_modified_or_canceled ) {
									EmailTemplate::send($application->user, 'JOB_CLOSED', 1, [
										'USER' => $application->user->fullname(),
										'BUYER_NAME' => $buyer_name,
										'JOB_POSTING' => $project_name,
										'JOB_POSTING_URL' => $project_url,
										'SEARCH_JOB_URL' => $search_job_url,
									]);
								}

								$application->status = ProjectApplication::STATUS_HIRING_CLOSED;
							} else {
								if ( $application->user->userNotificationSetting->applied_job_is_modified_or_canceled ) {
									EmailTemplate::send($application->user, 'JOB_CANCELLED', 1, [
										'USER' => $application->user->fullname(),
										'BUYER_NAME' => $buyer_name,
										'JOB_POSTING' => $project_name,
										'JOB_POSTING_URL' => $project_url,
										'SEARCH_JOB_URL' => $search_job_url,
									]);
								}

								$application->status = ProjectApplication::STATUS_PROJECT_CANCELLED;
							}
							
							$application->is_archived = ProjectApplication::IS_ARCHIVED_YES;
							$application->save();
						}
					}

					// Send email to buyer
					if ( $status == Project::STATUS_CLOSED ) {
						EmailTemplate::send($user, 'JOB_CLOSED', 2, [
							'USER' => $user->fullname(),
							'JOB_POSTING' => $project_name,
							'JOB_POSTING_URL' => $project_url,
						]);

						$result['message'] = trans('message.buyer.job.post.success_close');
					} else {
						EmailTemplate::send($user, 'JOB_CANCELLED', 2, [
							'USER' => $user->fullname(),
							'JOB_POSTING' => $project_name,
							'JOB_POSTING_URL' => $project_url,
						]);

						$job->closeAllOpenApplications();

						$result['message'] = trans('message.buyer.job.post.success_cancel');
					}

					$job->cancelled_at = date('Y-m-d H:i:s');
					$job->status = $status;
					
					if ( $job->save() ) {
						$result['success'] = true;
					}

				} else if ( $status == Project::STATUS_DELETED ) {

					$job->status = $status;
					
					if ( $job->save() ) {
						$result['success'] = true;

						$job->delete();
					}					

					$result['message'] = trans('message.buyer.job.post.success_delete_draft');
				}

				$user->updateLastActivity();
			}
		} catch ( Exception $e ) {
			Log::error('JobController.php [change_status] - ' . $e->getMessage());
		}

		if ( $result['success'] && $result['message'] && !Auth::user()->isAdmin() ) {
			add_message($result['message'], 'success');
		}

		return response()->json($result);
	}

	/**
	* Change Job Status (job/{id}/change_public/{status}) [AJAX]
	*
	* @author nada
	* @since Feb 28, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function change_public(Request $request, $job_id, $public)
	{
		$result = [
			'success' => false,
			'status' => 0
		];

		$user = Auth::user();

		if ( !$user || !$user->isBuyer() ) {
			return response()->json($result);
		} else {
			try {
				$job = Project::find($job_id);
				if (!Auth::user()->isAdmin()) {
					$job = Project::findByUnique($job_id);
					$job_id = $job->id;
				}

				if ( !$job ) {
					abort(404);
				}

				if ( !$job->isSuspended() && $job->checkIsAuthor($user->id) && $public != null ) {
					$job->is_public = $public;
					
					if ( $job->save() ) {
						$user->updateLastActivity();

						$result['success'] = true;
						$result['status'] = $public;
					}
				}
			} catch (Exception $e) {
				Log::error('JobController.php [change_public] - ' . $e->getMessage());
			}
		}

		return response()->json($result);
	}

	/**
	* Show freelancers to invite to the job
	*
	* @author Ro Un Nam
	* @since May 19, 2017
	*/
	public function invite_freelancers(Request $request, $job_id, $page = '', $user_id = null) {
		$controller = new JobDetailController();
		return $controller->invite_freelancers($request, $job_id, $page, $user_id);
	}

	/**
	* Interviews for the job
	*
	* @author Ro Un Nam
	* @since May 30, 2017
	*/
	public function interviews(Request $request, $job_id, $page = '', $user_id = null) {
		$controller = new JobDetailController();
		return $controller->interviews($request, $job_id, $page, $user_id );
	}

	/**
	* Hire & Offers for the job
	*
	* @author Ro Un Nam
	* @since May 30, 2017
	*/
	public function hire_offers(Request $request, $job_id, $user_id = null) {
		$controller = new JobDetailController();
		return $controller->hire_offers($request, $job_id, $user_id);
	}

	/**
	* Overview for the job
	*
	* @author Ro Un Nam
	* @since May 31, 2017
	*/
	public function overview(Request $request, $job_id, $user_id = null) {
		$controller = new JobDetailController();
		return $controller->overview($request, $job_id, $user_id);
	}

	/**
	* Hire User (job/hire/{uid})
	*
	* @author nada
	* @since Mar 16, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function hire_user(Request $request, $user_id) {
		$user = Auth::user();

		try {
			$contractor = User::findByUnique($user_id);

            $user_id = $contractor->id;

            if (!$contractor->isFreelancer() || $contractor->trashed() || $contractor->isSuspended())
            	abort(404);

			if ( $request->ajax() ) {
				$_ajax = [
					'status' => 'success',
					'error'  => '',
				];

				try {
					$job = Project::findOrFail($request->input('id'));

					if ( !$job || !$job->checkIsAuthor($user->id) ) {
						throw new Exception(trans('message.buyer.job.not_authorized'));
					}

					$data = view('pages.buyer.job.section.hire_job_info', [
						'job' => $job
					])->render();

					$_ajax['job_info'] = $data;

					$user->updateLastActivity();

				} catch(Exception $e) {
					$_ajax['status'] = 'error';
					$_ajax['error'] = 1;
					
					Log::error($e->getMessage());
				}

				return response()->json($_ajax);
			}

			if ( $request->isMethod('post') ) {
				$job_id = $request->input('job');
				$job = Project::findOrFail($job_id);

				return redirect()->to(_route('job.hire', ['id' => $job->id, 'uid' => $user_id]));
			}

			$jobs = Project::where('client_id', $user->id)
						   ->where('status', Project::STATUS_OPEN)
						   ->where('accept_term', Project::ACCEPT_TERM_YES)
						   ->orderBy('subject')
						   ->get();

			return view('pages.buyer.job.hire_user', [
				'page'  => 'buyer.job.hire_user',
				'jobs'  => $jobs, 
				'contractor' => $contractor,
			]);
		} catch ( Exception $e ) {
			Log::error('JobController.php - [hire_user] - ' . $e->getMessage());

			return redirect()->route('job.all_jobs');
		}
	}

	/**
	* Apply Job Page
	*
	* @author Ro Un Nam
	* @since Jun 14, 2017
	* @param  Request $request
	* @return Response
	*/
	public function job_apply(Request $request, $job_id) {
		$user = Auth::user();

		if ($user->isSuspended())
			abort(404);

		$job = Project::find($job_id);
		if (!$user->isAdmin()) {
			$job = Project::findByUnique($job_id);
		}
		$job_id = $job->id;

		$youAlreadyApplied = ProjectApplication::where('user_id', $user->id)
											   ->where('project_id', $job_id)
											   ->where('status', '<>', ProjectApplication::STATUS_HIRING_CLOSED)
											   ->first();

		if ( $youAlreadyApplied ) {
			return redirect()->to(_route('job.view', ['id' => $job_id]));
		}

		try {

			if ( $job->isSuspended() ) {
				return back();
			}

			if ( !$job->isPublic() && !$user ) {
				return redirect()->route('user.login');
			} elseif ( $job->isPrivate() ) {
				if ( !$job->canViewPrivate($user) ) {
					add_message(trans('message.no_such_job_posting'), 'danger');
					return redirect()->route('search.job');
				}
			}

			// Check affiliate
			$is_affiliated = UserAffiliate::checkAffiliated($job->client_id, $user->id);

			// Need to check the connections
			if ( $user->stat ) {
				$available_connections = $user->stat->connects;
			} else {
				$available_connections = 0;
			}

			// If connection is out of the limit;
			if ( $available_connections < 1 ) {
				add_message( trans('job.connection_limit'), 'danger' );
				return redirect()->to(_route('job.view', ['id' => $job_id]));
			}

			$needed_connections = ProjectApplication::JOB_CONNECTIONS;

			// For Featured Job
			if ( $job->isFeatured() ) {
				$needed_connections = Settings::get('CONNECTIONS_FEATURED_PROJECT'); 
			}

			// For Hourly Job
			if ( $job->isHourly() ) {
				$billing_rate = $user->profile->rate;
				$ijobdesk_fee = Settings::getFee($billing_rate, $is_affiliated);
				$earning_rate = $billing_rate - $ijobdesk_fee;
			}

			if ( $request->isMethod('post') ) {

				$validator_check = [
					'billing_fixed_rate' => 'required|numeric',
					'earning_rate' 		 => 'required|numeric',
					'duration' 		 	 => 'required',
					'coverletter' 		 => 'required|max:5000'
				];

				if ($job->isHourly()) {
					$validator_check = [
						'billing_hourly_rate' => 'required|numeric',
						'earning_rate' 		  => 'required|numeric',
						'coverletter' 		  => 'required|max:5000'
					];
				}

				// Check if requires cover letter
				if ( !$job->req_cv ) {
					unset($validator_check['coverletter']);
				}

				// Validator
				$validator = Validator::make($request->all(), $validator_check);
				if ( $validator->fails() ) {
					$errors = $validator->messages();
					if ( $errors->all() ) {
						foreach ( $errors->all() as $error ) {
							add_message($error, 'danger');
						}
					}

					throw new Exception('Failed the validation for job #' . $job_id .' by user #' . $user->id);
				}

				if ( $job->isHourly() ) {
					$hourly_price = floatval($request->input('billing_hourly_rate'));
					if ( $hourly_price > MAX_HOURLY_PRICE ) {
						add_message(trans('message.freelancer.job.invite.error_max_hourly_price', ['amount' => formatCurrency(MAX_HOURLY_PRICE)]), 'danger');

						return redirect()->to(_route('job.apply', ['id' => $job_id]));
					}
				} else {
					$fixed_price = floatval($request->input('billing_fixed_rate'));
					if ( $fixed_price > MAX_FIXED_PRICE ) {
						add_message(trans('message.freelancer.job.invite.error_max_fixed_price', ['amount' => formatCurrency(MAX_FIXED_PRICE)]), 'danger');

						return redirect()->to(_route('job.apply', ['id' => $job_id]));
					}
				}

				$request->flash();

				if ( $request->input('featured') ) {
					$needed_connections = $needed_connections * ProjectApplication::FEATURED_PROPOSAL_TIMES;
				}

				// If connection is not enough,
				if ( $available_connections < $needed_connections ) {
					add_message( trans('job.connection_not_enough'), 'danger' );
					return redirect()->to(_route('job.apply', ['id' => $job_id]));
				}

				// Update the user connections
				if ( $user->stat ) {
					$user->stat->connects = $user->stat->connects - $needed_connections;
					$user->stat->save();
				}

				// Check if already received an invitation
				$invited = ProjectInvitation::where('project_id', $job_id)
											->where('sender_id', $job->client_id)
											->where('receiver_id', $user->id)
											->first();

				$applicant = new ProjectApplication;
				$applicant->project_id = $job_id;
				$applicant->user_id = $user->id;
				$applicant->provenance = $invited ? ProjectApplication::PROVENANCE_INVITED : ProjectApplication::PROVENANCE_NORMAL;
				$applicant->type = $job->type;

				if ( $invited ) {
					$applicant->project_invitation_id = $invited->id;
				}
				
				// Featured option should be an option when a freelancer applies to a job rather.
				$applicant->is_featured = $request->input('featured') ? 1 : 0;

				if ( $job->isHourly() ) {
					$applicant->price = $request->input('billing_hourly_rate'); 
				} else {
					$applicant->price = $request->input('billing_fixed_rate'); 
				}

				$applicant->cv = strip_tags($request->input('coverletter'));
				$applicant->duration = $request->input('duration');
				if ( !$applicant->duration ) {
					$applicant->duration = Project::DUR_MT6M;
				}

				if ( $applicant->save() ) {

					if ( $invited ) {
						$invited->status = ProjectInvitation::STATUS_ACCEPTED;
						$invited->answer = $applicant->cv;
						$invited->save();
					}

					// Application Files
					$buyer_name = $job->client->fullname();
					$freelancer_name = $user->fullname();
					$project_name = $job->subject;
					$project_url = _route('job.interviews', ['id' => $job->id]);

					// Send email to buyer
					$totalNewProposals = $job->totalNewProposalsCount();

					if ( $totalNewProposals && ($totalNewProposals == 1 || $totalNewProposals % 3 == 0 ) ) {
						EmailTemplate::send($job->client, 'NEW_PROPOSAL', 2, [
							'USER' 				=> $buyer_name,
							'NEW_PROPOSAL_CNT' 	=> $totalNewProposals,
							'JOB_POSTING' 		=> $project_name,
							'JOB_POSTING_URL' 	=> $project_url
						]);
					}

					add_message(trans('message.freelancer.job.apply.success_apply', ['job_title'=>$job->subject]), 'success');

					$user->updateLastActivity();

					return redirect()->route('job.my_proposals');
				}
			}

			$show_alert = 0;
			if ( $job->qualification_success_score && $user->stat && $job->qualification_success_score > $user->stat->job_success ) {
				$show_alert |= 1;
			}

			if ( $job->qualification_location && ($job->qualification_location != $user->contact->country->region && $job->qualification_location != $user->contact->country->sub_region) ) {
				$show_alert |= 2;
			}

			if ( $job->qualification_hours && $user->stat && $job->qualification_hours > $user->stat->hours ) {
				$show_alert |= 4;
			}

			$desc = nl2br($job->desc);
			$less_desc = substr($desc, 0, Project::VIEWABLE_TEXT_LENGTH);

			return view('pages.freelancer.job.job_apply', [
				'user' => $user,
				'page' => 'freelancer.job.job_apply', 
				'job' => $job,
				'job_id'=> $job_id,
				'connections' => $available_connections,
				'show_alert' => $show_alert,
				'desc' => $desc,
				'less_desc' => $less_desc,
				'needed_connections' => $needed_connections,
				'earning_rate' => isset($earning_rate) ? $earning_rate : 0,
				'billing_rate' => isset($billing_rate) ? $billing_rate : 0,
				'ijobdesk_fee' => isset($ijobdesk_fee) ? $ijobdesk_fee : 0,
				'rate' => Settings::getRate($is_affiliated),

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
			Log::error('JobController.php [job_apply] ' . $e->getMessage());

			return redirect()->route('job.my_proposals');
		}
	}

	/**
	* My Proposal Page (job/my-proposal)
	*
	* @author Ro Un Nam
	* @since Dec 27, 2017
	*/
	public function my_proposals(Request $request, $tab = 'active')
	{
		$user = Auth::user();

		try {
			$job_offers = Contract::where('contractor_id', $user->id)
								  ->where('status', Contract::STATUS_OFFER)
								  ->orderBy('updated_at', 'DESC')
								  ->get();
			if ($tab == 'active') {

				$active_jobs = ProjectApplication::leftJoin('projects', 'project_applications.project_id', '=', 'projects.id')
												->where('project_applications.user_id', $user->id)
												->where('project_applications.is_declined', ProjectApplication::IS_DECLINED_NO)
												->where('project_applications.status', ProjectApplication::STATUS_ACTIVE)
										        ->whereIn('projects.status', [Project::STATUS_OPEN, Project::STATUS_SUSPENDED])
										        ->select('project_applications.*')
										        ->orderBy('project_applications.updated_at', 'DESC')
										        ->get();

				$invite_jobs = ProjectInvitation::where('receiver_id', $user->id)
												->where('status', ProjectInvitation::STATUS_NORMAL)
												->orderBy('updated_at', 'DESC')
												->get();
													
				$my_proposals = ProjectApplication::where('user_id', $user->id)
													->where('status', ProjectApplication::STATUS_NORMAL)
													->where('is_declined', ProjectApplication::IS_DECLINED_NO)
													->orderBy('updated_at', 'DESC')
													->get();

				return view('pages.freelancer.job.my_proposals', [
					'page'  => 'freelancer.job.my_proposals', 
					'job_offers'    => $job_offers,
					'active_jobs'   => $active_jobs,
					'invite_jobs'   => $invite_jobs,
					'my_proposals'  => $my_proposals,
					'tab'   		=> $tab,
				]);
			} elseif ($tab == 'archived') {
				$per_page = Config::get('settings.freelancer.proposals.per_page');

				$archived_jobs = ProjectApplication::leftJoin('projects', 'project_applications.project_id', '=', 'projects.id')
								->where('project_applications.user_id', $user->id)
								->whereIn('project_applications.provenance', [
									ProjectApplication::PROVENANCE_NORMAL,
									ProjectApplication::PROVENANCE_INVITED
								])
								->where(function($query) {
									$query->whereIn('project_applications.status', [
										ProjectApplication::STATUS_HIRING_CLOSED,
										ProjectApplication::STATUS_PROJECT_CANCELLED, 
										ProjectApplication::STATUS_PROJECT_EXPIRED
									])
										->orWhere('project_applications.is_declined', '<>', ProjectApplication::IS_DECLINED_NO)
										->orWhere('project_applications.is_archived', ProjectApplication::IS_ARCHIVED_YES)
										->orWhere('projects.status', Project::STATUS_CLOSED);
								})
								->select('project_applications.*')
								->orderBy('project_applications.updated_at', 'DESC')
								->paginate($per_page);

				return view('pages.freelancer.job.my_proposals', [
					'page'  		=> 'freelancer.job.my_proposals',
					'archived_jobs' => $archived_jobs,
					'job_offers'    => $job_offers,
					'tab'   		=> $tab
				]);
			}
		} catch(Exception $e) {
			Log::error('JobController.php [my_proposals] ' . $e->getMessage());

			return redirect()->route('job.my_proposals');
		}
	}

	/**
	* Accept Offer Page (apply-offer)
	*
	* @author Ri Chol Min
	* @since Mar 16, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function apply_offer(Request $request, $contract_id) {
		$user = Auth::user();

		try {
			$contract = Contract::findOrFail($contract_id);
			
			if ( !$contract->checkCurrentFreelancer($user->id) ) {
				throw new Exception('Accessed to invalid offer #' . $contract_id .' by user #' . $user->id);
			}

			$job = $contract->project;

			if ( !$contract->isOffer() ) {
				throw new Exception('Accessed to invalid offer #' . $contract_id .' by user #' . $user->id);
			}

			if ( $job->isSuspended() ) {
				throw new Exception('Accessed to suspended job offer #' . $contract_id .' by user #' . $user->id);
			}

			$offer = ProjectOffer::where('contract_id', $contract_id)
								 ->where('sender_id', $contract->buyer_id)
								 ->where('receiver_id', $contract->contractor_id)
								 ->where('project_id', $job->id)
								 ->first();

			if ( !$offer ) {
				throw new Exception('Accessed to invalid offer #' . $contract_id .' by user #' . $user->id);
			}

			$total_price = 0;
			foreach ( $contract->milestones as $milestone ) {
				$total_price += $milestone->getPrice();
			}

			$desc = nl2br($job->desc);
			$less_desc = substr($desc, 0, Project::VIEWABLE_TEXT_LENGTH);

			if ( $request->isMethod('post') ) {

				// Validator
				$validator = Validator::make($request->all(), [
					'message' => 'required|max:5000'
				]);

				if ( $validator->fails() ) {
					$errors = $validator->messages();
					if ( $errors->all() )
						foreach ( $errors->all() as $error )
							add_message($error, 'danger');

					throw new Exception('Failed the validation for offer #' . $contract_id .' by user #' . $user->id);
				}

				if ( floatval($request->input('earning_rate')) > $contract->price ){
					return view('pages.freelancer.job.apply_offer', [
						'page'        => 'freelancer.job.apply_offer',
						'job'         => $job,
						'contract'    => $contract,
						'contract_id' => $contract_id,
						'error'       => "You couldn't increase price.",
						'errorflag'   => 'error',
						'input_rate'  => formatCurrency($request->input('earning_rate'))
					]);
				} else {
					// Send email to buyer
					$buyer_name = $contract->buyer->fullname();
					$freelancer_name = $user->fullname();
					$contract_title = $contract->title;				

					if ( $request->input('_action') == 'accept' ) {

						if ( $contract->buyer->userNotificationSetting->offer_is_accepted ) {
							EmailTemplate::send($contract->buyer, 'OFFER_ACCEPTED', 2, [
								'USER' => $buyer_name,
								'FREELANCER' => $freelancer_name,
								'MESSAGE' => $request->input('message'),
								'JOB_POSTING' => $contract->project->subject,
								'JOB_POSTING_URL' => _route('job.view', ['id' => $contract->project->id])
							]);
						}

						Notification::send(Notification::FREELANCER_ACCEPTED_OFFER, 
				            SUPERADMIN_ID,
				            $contract->buyer->id,
				            [
				            	'sender_name' => $contract->contractor->fullname(),
				            	'offer_title' => sprintf('%s', $contract_title)
				            ]
				        );

						if ( !$contract->start() ) {
							add_message(trans('message.freelancer.job.offer.error_accept'), 'danger');

							return view('pages.freelancer.job.apply_offer', [
								'page'        => 'freelancer.job.apply_offer',
								'job'         => $job,
								'contract'    => $contract,
								'offer' => $offer, 
								'total_price' => $total_price,
								'error'       => '',
								'errorflag'   => 'error',
								'input_rate'  => formatCurrency($request->input('earning_rate'))
							]);
						}

						$offer->accept();

						// Check proposal
						if ( !$contract->application ) {
							$proposal = new ProjectApplication;
							$proposal->status = ProjectApplication::STATUS_HIRED;
							$proposal->provenance = ProjectApplication::PROVENANCE_OFFER;
							$proposal->cv = strip_tags($request->input('message'));
							$proposal->project_id = $job->id;
							$proposal->user_id = $user->id;
							$proposal->type = $job->type;
							$proposal->price = $contract->price;
							$proposal->duration = $job->duration;
							
							if ( !$proposal->duration ) {
								$proposal->duration = Project::DUR_MT6M;
							}

							if ( $proposal->save() ) {
								$contract->application_id = $proposal->id;
								$contract->save();
							}
						} else {
							$proposal = $contract->application;
							$proposal->is_checked = 1;
							$proposal->status = ProjectApplication::STATUS_HIRED;
							$proposal->save();
						}

						if ( $request->input('message') != '' ) {
							$proposal->sendMessage(strip_tags($request->input('message')), $user->id, false);
						}

						add_message(trans('message.freelancer.job.offer.success_accept', ['job_title' => $job->subject]), 'success');
					
					} else if ( $request->input('_action') == 'reject' ){          
						$contract->reject($request);

						$offer->decline();

						EmailTemplate::send($contract->buyer, 'OFFER_DECLINED', 2, [
							'USER' => $buyer_name,
							'FREELANCER' => $freelancer_name,
							'MESSAGE' => $request->input('message'),
							'JOB_POSTING' => $contract->project->subject,
							'JOB_POSTING_URL' => _route('job.view', ['id' => $contract->project->id])
						]);

						Notification::send(Notification::FREELANCER_DECLINED_OFFER, 
				            SUPERADMIN_ID,
				            $contract->buyer->id,
				            [
				            	'sender_name' => $contract->contractor->fullname(),
				            	'offer_title' => sprintf('%s', $contract_title)
				            ]
				        );

						add_message(trans('message.freelancer.job.offer.success_reject', ['job_title' => $job->subject]), 'success');
					}

					$user->updateLastActivity();

					return redirect()->route('contract.all_contracts');
				}      
			}

			return view('pages.freelancer.job.apply_offer', [
				'page' => 'freelancer.job.apply_offer',
				'job' => $job,
				'contract' => $contract,
				'offer' => $offer, 
				'total_price' => $total_price,
				'j_trans'=> [
					'reject_offer' => trans('j_message.freelancer.job.reject_offer'), 
					'accept_offer' => trans('j_message.freelancer.job.accept_offer'), 
				]
			]);
		} catch ( Exception $e ) {
			Log::error('JobController.php [apply_offer] ' . $e->getMessage());

			return redirect()->route('job.my_proposals');
		}
	}

	/**
	* Accept Invite Page (accept-invite)
	*
	* @author Ro Un Nam
	* @since Dec 17, 2017
	*/
	public function accept_invite(Request $request, $invitation_id) {
		$user = Auth::user();

		$invitation = ProjectInvitation::findOrFail($invitation_id);
		if ( !$invitation ) {
			Log::error('JobController [accept_invite] - Invalid invitation #' . $invitation_id);
			return redirect()->route('job.my_proposals');
		}

		if ( !$invitation->isNormal() ) {
			Log::error('JobController [accept_invite] - Aready accepted or declined invitation #' . $invitation_id);
			return redirect()->route('job.my_proposals');
		}

		if ( !$invitation->is_receiver($user->id) ) {
			Log::error('JobController [accept_invite] - Trying to access invalid invitation #' . $invitation_id);
			return redirect()->route('job.my_proposals');
		}

		$job = $invitation->project;
		
		if ( $job->isSuspended() ) {
			return back();
		}		

		// Check affiliate
		$is_affiliated = UserAffiliate::checkAffiliated($job->client_id, $user->id);

		if ( $job->isHourly() ) {
			$billing_rate = $user->profile->rate;
			$ijobdesk_fee = Settings::getFee($billing_rate, $is_affiliated);
			$earning_rate = $billing_rate - $ijobdesk_fee;
		} else {
			$billing_rate = 0;
		}	

		if ( $invitation->status != ProjectInvitation::STATUS_NORMAL ) {
			Log::error('JobController [accept_invite] - Already accepted or declined invitation #' . $invitation_id);
			return redirect()->route('search.job');
		}

		if ( $request->isMethod('post') ) {

			$message = strip_tags($request->input('message'));

			// Send email to buyer
			$buyer_name = $job->client->fullname();
			$freelancer_name = $user->fullname();
			$project_name = sprintf('<a href="%s">%s</a>', _route('job.view', ['id' => $job->id]), $job->subject);

			if ( $request->input('_action') == 'decline' ) {
				$invitation->status = ProjectInvitation::STATUS_DECLINED;
				$invitation->answer = $message;
				$invitation->save();

				$user->updateLastActivity();

				// Send email to buyer
				EmailTemplate::send($job->client, 'INVITATION_DECLINED', 2, [
					'USER' => $buyer_name,
					'FREELANCER' => $freelancer_name,
					'MESSAGE' => $message,
					'JOB_POSTING' => $job->subject,
					'JOB_POSTING_URL' => _route('job.view', ['id' => $job->id]),
				]);

				add_message(trans('message.freelancer.job.invite.decline', ['job_title' => $job->subject]), 'danger');

				return redirect()->route('job.my_proposals');

			} else {

				if ( $job->isHourly() ) {
					$price = floatval($request->input('billing_hourly_rate'));
					if ( $price > MAX_HOURLY_PRICE ) {
						add_message(trans('message.freelancer.job.invite.error_max_hourly_price', ['amount' => formatCurrency(MAX_HOURLY_PRICE)]), 'danger');

						return redirect()->route('job.accept_invite', ['id' => $invitation_id]);  
					}
				} else {
					$price = floatval($request->input('billing_fixed_rate'));
					if ( $price > MAX_FIXED_PRICE ) {
						add_message(trans('message.freelancer.job.invite.error_max_fixed_price', ['amount' => formatCurrency(MAX_FIXED_PRICE)]), 'danger');

						return redirect()->route('job.accept_invite', ['id' => $invitation_id]);
					}
				}

				$invitation->status = ProjectInvitation::STATUS_ACCEPTED;
				$invitation->answer = $message;
				$invitation->save();

				$app = new ProjectApplication;
				$app->provenance = ProjectApplication::PROVENANCE_INVITED;
				$app->project_id = $job->id;
				$app->user_id = $invitation->receiver_id;
				$app->project_invitation_id = $invitation->id;
				$app->type = $job->type;
				$app->price = $price;				
				$app->duration = $request->input('duration');
				
				if ( $app->save() ) {

					$thread = $app->getMessageThread();

					$user->updateLastActivity();
					EmailTemplate::send($job->client, 'INVITATION_ACCEPTED', 2, [
						'USER' => $buyer_name,
						'FREELANCER' => $freelancer_name,
						'MESSAGE' => $message,
						'JOB_POSTING' => $job->subject,
						'JOB_POSTING_URL' => _route('job.view', ['id' => $job->id]),
					]);

					add_message(trans('message.freelancer.job.invite.accept', ['job_title' => $job->subject]), 'success');

					return redirect()->to(_route('job.application_detail', ['id' => $app->id]));
				} else {
					add_message(trans('message.freelancer.job.invite.decline', ['job_title' => $job->subject]), 'danger');

					return redirect()->route('job.accept_invite', ['id' => $invitation_id]);
				}
			}
		}

		return view('pages.freelancer.job.accept_invite', [
			'page' => 'freelancer.job.accept_invite',
			'job' => $job,
			'invitation' => $invitation,
			'billing_rate' => isset($billing_rate) ? $billing_rate : 0,
			'ijobdesk_fee' => isset($ijobdesk_fee) ? $ijobdesk_fee : 0,
			'earning_rate' => isset($earning_rate) ? $earning_rate : 0,
			'config' => Config::get('settings'),
			'rate' => Settings::getRate($is_affiliated),

			'j_trans' => [
				'please_enter_a_valid_number' => trans('common.please_enter_a_valid_number'), 
				'please_enter_a_value_less_than_or_equal_to_999' => trans('common.please_enter_a_value_less_than_or_equal_to_999'),
				'please_enter_a_value_less_than_or_equal_to_9999999' => trans('common.please_enter_a_value_less_than_or_equal_to_9999999'), 
				'please_enter_a_value_greater_than_or_equal_to_1' => trans('common.please_enter_a_value_greater_than_or_equal_to_1'),
				'MAX_FIXED_PRICE' => MAX_FIXED_PRICE,
				'MAX_HOURLY_PRICE' => MAX_HOURLY_PRICE,
			],
		]);
	}

	/**
	 * Hire freelancer
	 * @author KCG
	 * @since  2017/5/26
	 * @param Request request
	 * @param id Job ID
	 * @param uid Freelancer ID
	 * @param pid ProjectApplication ID
	 * @return response
	 */
	public function hire(Request $request, $id, $uid, $pid = 0) {
		$client = Auth::user();

		$job = Project::find($id);
		if (!Auth::user()->isAdmin()) {
			$job = Project::findByUnique($id);
			$id = $job->id;
		}

		if ( !$job ) {
			abort(404);
		}

		$user = User::findByUnique($uid);
        $uid = $user->id;

        if (!$user->isFreelancer() || $user->trashed() || $user->isSuspended())
        	abort(404);

		if ( !$job ) {
			return redirect()->route('job.all_jobs');
		}
		
		if ( $job->client_id != $client->id ) {
			return redirect()->route('job.all_jobs');
		}

		if ( $job->status == Project::STATUS_SUSPENDED ) {
			return back();
		}

		// Check if already sent an offer
		if ( ProjectOffer::isSent($job, $user) ) {
			if (Contract::isStarted($job, $user))
				add_message(trans('message.buyer.job.contract.already_hired'), 'danger');
			else
				add_message(trans('message.buyer.job.contract.already_offered', ['sb' => $user->fullname()]), 'danger');
			
			return redirect()->route('job.all_jobs');
		}

		// Check the limit of contractors
		// if (Contract::isOverLimit($job)) {
		// 	add_message(trans('message.buyer.job.contract.already_hired'), 'danger');
		// 	return redirect()->route('job.all_jobs');
		// }

		if ( $pid ) {
			$proposal = ProjectApplication::findByUnique($pid);
	        $pid = $proposal->id;

			if ( $proposal && $proposal->user->isSuspended() ) {
				return back();
			}
		}

		$billing_hourly_rate = $user->profile->rate;
		if ( isset($proposal) && $proposal ) {
			$billing_hourly_rate = $proposal->price;
		}

		// Check if already hired
		if ( Contract::isStarted($job, $user) ) {
			add_message(trans('message.buyer.job.contract.already_opened', ['sb' => $user->fullname()]), 'danger');
			return redirect()->route('job.all_jobs');
		}

		$balance = $client->myBalance();

		$page_submitted = false;

		// Submit page
		if ( $request->isMethod('post') ) {

			$page_submitted = true;
			$request->flash();

			$contract_type = $request->input('job_type');
			$validation_error = 0;
			
			if ( !$request->input('contract_title') ) {
				$validation_error = 1;
			}

			$total_fund_price = 0;
			$total_price = 0;

			if ( $contract_type == 0 ) {
				foreach ( $request->input('milestone_title') as $inx => $title ) {
					if ( !$title || 
						$request->input('milestone_price')[$inx] <= 0 ||
						$request->input('milestone_price')[$inx] > MAX_FIXED_PRICE )
					{
						$validation_error = 1;
					}

					if ( $request->input('milestone_fund_value')[$inx] ) {
						$total_fund_price += floatval($request->input('milestone_price')[$inx]);
					}

					$total_price += floatval($request->input('milestone_price')[$inx]);
				}
			} else if ( $contract_type == 1 ) {
				if ( $request->input('billing_rate') <= 0 ) {
					$validation_error = 1;
				}
			}
			
			if ( $validation_error == 1 ) {
				add_message(trans('message.buyer.contract.send_offer.failure_validation'), 'danger');
			} else {

				// Check the total price when checked fund
				if ( $client->myBalance() < $total_fund_price ) {
					add_message(trans('message.not_enough_balance'), 'danger');
				} else {
					$contract = new Contract;
					$contract->title = strip_tags($request->input('contract_title'));
					$contract->buyer_id = $client->id;
					$contract->contractor_id = $user->id;
					$contract->project_id = $job->id;
					$contract->type = $request->input('job_type');
					// $contract->started_at = date('Y-m-d H:i:s', strtotime($request->input('contract_start')));
					$contract->started_at = date('Y-m-d H:i:s');

					if ( isset($proposal) && $proposal ) {
						$contract->application_id = $proposal->id;
					}

					// Hourly
					if ( $contract->type == Contract::TYPE_HOURLY ) {
						$contract->price = round2Decimal(floatval($request->input('billing_rate')));
						$contract->limit = $request->input('week_limit');
						if ( $request->input('manual_time') == 1 ) {
							$contract->is_allowed_manual_time = 1;
						} else {
							$contract->is_allowed_manual_time = 0;
						}
					} else {
						$contract->price = round2Decimal($total_price);
					}

					$contract->status = Contract::STATUS_OFFER;

					// Check affiliate
					if ( UserAffiliate::checkAffiliated($contract->buyer_id, $contract->contractor_id) ) {
						$contract->is_affiliated = 1;
					}
					
					if ( $contract->save() ) {
						$offer = new ProjectOffer;
						$offer->sender_id = $client->id;
						$offer->receiver_id = $user->id;
						$offer->project_id = $job->id;
						$offer->contract_id = $contract->id;
						// $offer->start_date = date('Y-m-d', strtotime($request->input('contract_start')));
						$offer->start_date = date('Y-m-d');
						$offer->message = strip_tags($request->input('description'));
						$offer->save();

						// Fixed contract
						if ( !$contract->isHourly() ) {

							foreach ( $request->input('milestone_title') as $inx => $title ) {
								$milestone = new ContractMilestone;
								$milestone->contract_id = $contract->id; 
								$milestone->name = $title;
								
								$milestone->start_time = date('Y-m-d');
								if ( $request->input('milestone_end')[$inx] ) {
									$milestone->end_time = date('Y-m-d', strtotime($request->input('milestone_end')[$inx]));
								} else {
									$milestone->end_time = date('Y-m-d');
								}

								$milestone->price = round2Decimal($request->input('milestone_price')[$inx]);
								$milestone->save();

								if ( $request->input('milestone_fund_value')[$inx] ) {
									if ( TransactionLocal::fund($contract->id, $milestone->id) ) {
										$milestone->fund_status = ContractMilestone::FUNDED;
										$milestone->save();
									} else {
										add_message(trans('message.buyer.contract.milestones.fund_milestone_error', ['milestone' => $milestone->name]), 'danger');
									}
								}
							}
						}
						
						$notification_id = Notification::send(Notification::RECEIVED_JOB_OFFER, SUPERADMIN_ID, $user->id, 
							[
								'buyer' => $client->fullname(), 
								'project' => sprintf('%s', $job->subject)
							]
						);

						$contract->notification_id = $notification_id;
						$contract->save();

						// Send email to freelancer
						$freelancer_name = $contract->contractor->fullname();
						EmailTemplate::send($contract->contractor, 'OFFER_RECEIVED', 1, [
							'USER' => $freelancer_name,
							'USER_FULL' => $freelancer_name,
							'BUYER' => $contract->buyer->fullname(),
							'JOB_POSTING' => $contract->project->subject,
							'CONTRACT_TITLE' => $contract->title,
							'TERMS' => $contract->term_string(),
							'OFFER_URL' => route('job.apply_offer', ['id' => $contract->id]),
							'MESSAGE' => $offer->message,
						]);

						add_message(trans('message.buyer.contract.send_offer.success_offer', ['contract_title' => $contract->title]), 'success');

						$client->updateLastActivity();

						return redirect()->route('job.all_jobs');
					} else {
						add_message(trans('message.buyer.contract.send_offer.failure'), 'danger');
					}
				}
			}
		}

		$this->setPageTitle(trans('page.buyer.job.hire.title', ['job' => $job->subject]) . ' - ' . trans('page.title'));

		return view('pages.buyer.job.hire', [
			'page' => 'buyer.job.hire',
			'user' => $user,
			'client' => $client,
			'job' => $job,
			'proposal' => isset($proposal) ? $proposal : null,
			'balance' => $balance,
			'page_submitted' => $page_submitted,
			'billing_hourly_rate' => isset($billing_hourly_rate) ? $billing_hourly_rate : null,
			
			'j_trans' => [
				'required' => trans('common.validation.required'), 
				'confirm_create_one_milestone' => trans('job.confirm_create_one_milestone'),
				'please_enter_a_valid_number' => trans('common.please_enter_a_valid_number'), 
				'please_enter_a_valid_date' => trans('common.please_enter_a_valid_date'), 
				'please_enter_a_value_less_than_or_equal_to_n' => trans('common.please_enter_a_value_less_than_or_equal_to_n'),
				'milestones_total_amount_exceeds_estimate' => trans('job.milestones_total_amount_exceeds_estimate'), 
				'milestones_total_funding_exceeds_balance' => trans('job.milestones_total_funding_exceeds_balance'), 
				'MAX_FIXED_PRICE' => MAX_FIXED_PRICE,
				'MAX_HOURLY_PRICE' => MAX_HOURLY_PRICE,
				'billing_rate' => $billing_hourly_rate, 
				'ok' => trans('common.ok'),
				'cancel' => trans('common.cancel'),
			],
		]);
	}

	/**
	* Send invitation
	*/
	public static function do_send_invitation($project_id = 0, $sender_id = 0, $receiver_id = 0, $message = '') {
		$result = [
			'success' => false,
		];

		try {
			if ( !$project_id || !$sender_id || !$receiver_id ) {
				return $result;
			}

			$job = Project::find($project_id);
			if ( !$job->checkIsAuthor($sender_id) ) {
				throw new Exception('No permission to send invitation to project #' . $project_id);
			}

			if ( $job->status == Project::STATUS_CLOSED || $job->status == Project::STATUS_CANCELLED ) {

				throw new Exception('Project has been closed or cancelled. #' . $project_id);

			} else {

				// Check if already sent an invitation
				$invited = ProjectInvitation::where('project_id', $project_id)
											->where('sender_id', $sender_id)
											->where('receiver_id', $receiver_id)
											->first();

				if ( $invited ) {

					throw new Exception('User #' . $sender_id . ' has aready sent an invitation to project #' . $project_id . ' for user #' . $receiver_id);

				} else {

					$projectInvitation = new ProjectInvitation;
					$projectInvitation->sender_id = $sender_id;
					$projectInvitation->receiver_id = $receiver_id;
					$projectInvitation->project_id = $project_id;
					$projectInvitation->message = mb_substr($message, 0, 5000);
					
					if ( $projectInvitation->save() ) {
						
						// Check if freelancer has already sent a proposal
						$proposal = ProjectApplication::where('project_id', $project_id)
													->where('user_id', $receiver_id)
													->first();
						if ( $proposal ) {
							$proposal->provenance = ProjectApplication::PROVENANCE_INVITED;
							$proposal->project_invitation_id = $projectInvitation->id;
							$proposal->save();
						}

						$freelancer_name = $projectInvitation->receiver->fullname();
						$buyer_name = $projectInvitation->sender->fullname();

						$result['success'] = true;
						$result['message'] = trans('common.invitation_sent_to') . ' ' . $freelancer_name;
						$result['job_id'] = $project_id;						

						Notification::send(Notification::RECEIVED_INVITATION, 
							SUPERADMIN_ID,
							$receiver_id, 
							[
								'buyer_fullname' => $buyer_name, 
								'job_title' => sprintf('%s', $job->subject)
							]
						);

						$projectInvitation->sender->updateLastActivity();

						// Send email to freelancer
						EmailTemplate::send($projectInvitation->receiver, 'INVITATION_RECEIVED', 1, [
							'USER' => $freelancer_name,
							'BUYER_USER' => $buyer_name,
							'BUYER_NAME' => $buyer_name,
							'MESSAGE' => $message,
							'JOB_POSTING' => $job->subject,
							'JOB_TYPE' => $job->type_string(),
							'JOB_COND' => $job->isHourly() ? $job->affordable_rate_string() : $job->price_string(true),
							'JOB_DESC' => $job->desc,
							'JOB_POSTING_URL' => _route('job.view', ['id' => $job->id]),
							'INVITATION_URL' => route('job.accept_invite', ['id' => $projectInvitation->id])
						]);
					}
				}

			}
		} catch ( Exception $e ) {
			Log::error('JobController [do_send_invitation] - ' . $e->getMessage());
		}

		return $result;
	}

	/**
	* Send invitation to the freelancer via ajax
	*
	* @author Ro Un Nam
	* @since May 19, 2017
	*/
	public function send_invitation(Request $request) {
		$user = Auth::user();

		$json = ['success' => false];

		if ( $request->ajax() ) {
			$job_id = $request->input('job_id');
			$receiver_id = $request->input('user_id');
			$message = strip_tags($request->input('invite_message'));

			$json = self::do_send_invitation($job_id, $user->id, $receiver_id, $message);
		}

		return response()->json($json);
	}

	/**
	* Withdraw job offer by ajax
	*
	* @author Ro Un Nam
	* @since Aug 11, 2017
	*/
	public function withdraw_offer(Request $request) {
		$user = Auth::user();

		$json = ['success' => false];

		$id = $request->input('id');
		$contract = Contract::findOrFail($id);

		if ( $contract->project->status != Project::STATUS_SUSPENDED ) {

			if ( $contract->checkIsAuthor($user->id) ) {
				$contract->status = Contract::STATUS_WITHDRAWN;
				$contract->reason = $request->input('reason');
				$contract->closed_reason = $request->input('message');
				$contract->ended_at = date('Y-m-d H:i:s');

				if ( $contract->save() ) {
					$offer = ProjectOffer::where('contract_id', $contract->id)
										 ->where('sender_id', $contract->buyer_id)
										 ->where('receiver_id', $contract->contractor_id)
										 ->first();

					if ( $offer ) {
						$offer->withdraw();
					}

					$json['success'] = true;

					// Send email to freelancer
					EmailTemplate::send($contract->contractor, 'OFFER_WITHDRAWN', 1, [
						'USER' => $contract->contractor->fullname(),
						'BUYER' => $contract->buyer->fullname(),
						'MESSAGE' => $contract->reason,
						'JOB_POSTING' => $contract->project->subject,
						'JOB_POSTING_URL' => _route('job.view', ['id' => $contract->project->id])
					]);

					$user->updateLastActivity();
				}
			}

		}

		if ( $request->ajax() ) {
			return response()->json($json);
		}

		return redirect()->route('job.all_jobs');
	}

	/**
	* Read job offer by ajax
	*
	* @author Ro Un Nam
	* @since Sep 12, 2017
	*/
	public function read_offer(Request $request, $id) {
		$user = Auth::user();

		// Project Ajax Actions
		if ( $request->ajax() ) {
			UserNotification::where('id', $id)->update(['read_at' => date('Y-m-d H:i:s')]);

			return response()->json([]);
		} else {
			abort(404);
		}
	}
}