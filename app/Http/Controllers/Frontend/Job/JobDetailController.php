<?php namespace iJobDesk\Http\Controllers\Frontend\Job;

use iJobDesk\Http\Controllers\Controller;
use iJobDesk\Http\Controllers\JobController;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Auth;
use DB;
use Log;
use Storage;
use Config;
use Session;
use Exception;
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
use iJobDesk\Models\Settings;

class JobDetailController extends Controller {

	/**
	* View Job Page (job/{id})
	*
	* @author nada
	* @since Feb 23, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function index(Request $request, $job_id) {
		$user = Auth::user();

		try {

			$job = Project::find($job_id);

			if (!$user || !$user->isAdmin()) {
				$job = Project::findByUnique($job_id);
				$job_id = $job->id;
			}

			if ( !$job ) {
				abort(404);
			}

			if ( $job->client->isSuspended() ) {
				return redirect()->route('search.job');
			}

			if ( $job->isSuspended() ) {
				return redirect()->route('search.job');
			}

			if ( $job->isClosed() ) {
				/*
				if ( $user->isFreelancer() ) {
					$freelancer_ids = ProjectApplication::where('project_id', $job_id)
														->where('project_applications.is_declined',  ProjectApplication::IS_DECLINED_NO)
														->where('project_applications.is_archived', ProjectApplication::IS_ARCHIVED_NO)
														->pluck('user_id')
														->toArray();
					
					if ( !in_array($user->id, $freelancer_ids) ) {
						return redirect()->route('search.job');
					}
				} else if ( $user->isBuyer() ) {
					if ( !$job->checkIsAuthor($user->id) ) {
						return redirect()->route('search.job');
					}
				}
				*/
			} elseif ( $job->isDraft() && $user->id != $job->client_id ) {
				return redirect()->route('search.job');
			}

			if ( !$job->isDraft() && $job->accept_term != Project::ACCEPT_TERM_YES ) {
				return redirect()->route('search.job');
			}

			if ( !$user && !$job->isPublic() ) {
				return redirect()->route('user.login');
			} elseif ( $job->isPrivate() ) {
				if ( !$job->canViewPrivate($user) ) {
					add_message(trans('message.no_such_job_posting'), 'danger');
					return redirect()->route('search.job');
				}
			}

			$cur_contracts = Contract::where('buyer_id', $job->client_id)
						        	->whereIn('status', [
						        		Contract::STATUS_OPEN, 
						        		Contract::STATUS_PAUSED,
						        		Contract::STATUS_SUSPENDED,
						        	])->get();

			/* Ended Contracts */
			$limit = 5;
			$page = 1;
        	$end_contracts = Contract::endedBuyerContracts($job->client_id);
        	$ended_contract_totals = $end_contracts->count();
        	$end_contracts = $end_contracts->skip($limit * ($page - 1))
						  				   ->take($limit)
						  				   ->get();
        	$ended_contract_more = ($ended_contract_totals > $limit * ($page - 1) + count($end_contracts));

			$opended_job_count = Project::where('client_id', $job->client_id)
										->where('status', Project::STATUS_OPEN)
										->count();
			
			$total_spents_amount = 0;

			$total_spents = ContractMeter::leftJoin('contracts', 'contract_meters.contract_id', '=', 'contracts.id')
										->where('contracts.buyer_id', $job->client_id)
										->get();

			foreach($total_spents as $spent) {
				$total_spents_amount = $total_spents_amount + $spent->total_amount;
			}

			$active_count = count($cur_contracts);

			$hired_count = Contract::where('buyer_id', $job->client_id)->count();

			if ( $user && $user->stat ) {
				$available_connections = $user->stat->connects;
			} else {
				$available_connections = 0;
			}
			
			// Needed job connections
			$needed_connections = ProjectApplication::JOB_CONNECTIONS;
			if ( $job->isFeatured() ) {
				$needed_connections = Settings::get('CONNECTIONS_FEATURED_PROJECT');
			}

			$desc = nl2br($job->desc);
			$less_desc = substr($desc, 0, Project::VIEWABLE_TEXT_LENGTH);

			$applied = false;
			$application = null;
			if ( $user ) {
				$application = ProjectApplication::where('project_id', $job->id)
											 	 ->where('user_id', $user->id)
											 	 ->where('status', '<>', ProjectApplication::STATUS_HIRING_CLOSED)
											 	 ->first();
				$applied = $application != null;
			}

			$this->setPageTitle(trans('page.job.job_detail.title', ['job' => $job->subject]) . ' - ' . trans('page.title'));

			return view('pages.job.job_detail', [
				'page' => 'job.job_detail', 
				'job' => $job,
				'cur_contracts' => $cur_contracts,

            	'end_contracts' => $end_contracts,
            	'ended_contract_more' => $ended_contract_more,

				'opened_job_count' => $opended_job_count,
				'total_spents_amount' => $total_spents_amount,
				'active_count' => $active_count,
				'hired_count' => $hired_count,
				'available_connections' => $available_connections,
				'needed_connections' => $needed_connections,
				'desc' => $desc,
				'less_desc' => $less_desc,
				'client' => $job->client,

				'applied' => $applied,
				'application' => $application,
				
				'saved' => $job->isSaved(),
				'j_trans' => [
					'saved_job' => trans('common.saved'), 
				]
			]);

		} catch(ModelNotFoundException $e) {
			return redirect()->route('search.job');
		}
	}

	public function load_ended_contracts($request, $buyer_id, $page = 1) {
		$end_contracts = Contract::endedBuyerContracts($buyer_id);

		$totals = $end_contracts->count();
		
		$limit = 5;
		$end_contracts = $end_contracts->skip($limit * ($page - 1))
					  				   ->take($limit)
					  				   ->get();

		$html = '';
		foreach ($end_contracts as $contract) {
			$html .= view('pages.job.detail.feedback', ['contract' => $contract])->render();
		}

		$more = $totals > $limit * ($page - 1) + count($end_contracts);
		if ($more)
			$html .= '<a href="'.route('job.detail.feedbacks', ['user_id' => $buyer_id, 'page' => $page  + 1]).'" class="load-more-messages">'.trans('ticket.load_more').'</a>';

		$json = ['html' => $html];
		return response()->json($json);
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
	public function edit(Request $request, $job_id, $action = '') {
		$user = Auth::user();
		if ( !$user ) {
			return redirect()->route('user.login');
		}

		$settings = Config::get('settings');

		$project = new Project;

		try {
			if ( !empty($job_id) ) {
				$project = Project::find($job_id);
				if (!Auth::user()->isAdmin()) {
					$project = Project::findByUnique($job_id);
					$job_id = $project->id;
				}

				if ( !$project ) {
					abort(404);
				}
				
				if ( !$project->checkIsAuthor($user->id) ) {
					return redirect()->route('job.all_jobs');
				}
				if ( $project->status == Project::STATUS_SUSPENDED ) {
					return redirect()->to(_route('job.overview', ['id' => $job_id]));
				}
			}

			$project_id = $project->id;

			$page_submitted = false;

			if ( $request->input('submitted') ) {
				$page_submitted = true;
			}

			if ( $request->isMethod('post') ) {

				$page_submitted = true;
				try {

					$error = false;

					// Flash data to the session.
					$request->flashOnly(
						'action',
						'category', 
						'title', 
						'description', 
						'job_type', 
						'job_term',
						'job_skills',
						'contract_limit',
						'experience_level',
						'duration', 
						'workload', 
						'affordable_rate',
						'price', 
						'term',
						'qualification_success_score',
						'qualification_hours',
						'qualification_location',
						'cv_required', 
						'job_public',
						'job_featured'
					);

					// Validate All Fields
					$validator = null;

					if ($request->input('action') != 'save_draft') {
						if ( $request->input('job_type') == Project::TYPE_HOURLY ) { // case of Hourly Job
							$validator = Validator::make($request->all(), [
								'category' 			=> 'required',
								'title' 			=> 'required:max:200',
								'description' 		=> 'required|max:5000',
								'term' 				=> 'required',
								'contract_limit' 	=> 'required',
								'experience_level' 	=> 'required',
								'workload' 			=> 'required',
								'duration' 			=> 'required'
							]);
						} else {
							$validator = Validator::make($request->all(), [
								'category' 			=> 'required',
								'title' 			=> 'required:max:200',
								'description' 		=> 'required|max:5000',
								'term' 				=> 'required',
								'contract_limit' 	=> 'required',
								'price' 			=> 'required|numeric',
								'experience_level' 	=> 'required',
								'duration' 			=> 'required'
							]);
						}
					}

					$data = $request->all();
					$data['submitted'] = 1;

					if ( $validator && $validator->fails() ) {
						$errors = $validator->messages();
						if ( $errors->all() ) {
							foreach ( $errors->all() as $error ) {
								add_message($error, 'danger');
							}
						}

						/*
						if ( $job_id ) {
							return redirect()->to(_route('job.edit', ['id' => $job_id]));
						} else {
							return redirect()->route('job.create', $data);
						}
						*/

						$error = true;
					}

					// Featured job
					if ( $request->input('job_featured') ) {
						
						// Pay featured job fee
						$featured_fee = Settings::getFeaturedJobFee();

						if ( $featured_fee > 0 ) {
							
							// Check the sender's wallet
							if ( $user->wallet->amount < $featured_fee ) {

								add_message(trans('message.not_enough_balance'), 'danger');
								
								$error = true;

							}
						}
					}

					///////////// Validation ////////////
					if ( $request->input('job_type') == Project::TYPE_FIXED ) {
						$price = priceRaw($request->input('price'));
						if ( $price <= 0 ) {
							// throw new Exception(trans('message.error_empty_price'));

							$error = true;
						}
					}

					if ( !$error ) {

						// Check if the job is saved as draft
						if ( $request->input('action') == 'save_draft' ) {
							$project->status = Project::STATUS_DRAFT;
						} else if ( $request->input('action') == 'post_job' || $request->input('action') == 'repost_job' ) {
							$project->status = Project::STATUS_OPEN;
							$project->created_at = date('Y-m-d H:i:s');
						}

						$project->client_id = $user->id;
						$project->category_id = $project->category_id = $request->input('category');

						$project->subject = $request->input('title');

						$project->desc = strip_tags($request->input('description'));
						$project->type = $request->input('job_type');

						$project->affordable_rate = $request->input('affordable_rate');

						if ( $project->type == Project::TYPE_HOURLY ) {
							$project->workload = $request->input('workload');
							$project->price = 0;

							if ( !$project->workload ) {
								$project->workload = Project::WL_NS;
							}
						} else {
							$project->workload = '';
							$project->price = $price;
						}

						$project->duration = $request->input('duration');
						if ( !$project->duration ) {
							$project->duration = Project::DUR_MT6M;
						}

						$project->term = $request->input('term');
						$project->experience_level = $request->input('experience_level');
						$project->qualification_success_score = $request->input('qualification_success_score');
						$project->qualification_hours = $request->input('qualification_hours');
						$project->qualification_location = $request->input('qualification_location');

						// Initialize fields
						if ( !$project->experience_level ) {
							$project->experience_level = 0;
						}

						$project->contract_limit = $request->input('contract_limit');

						$project->req_cv = $request->input('cv_required') ? $request->input('cv_required') : 0;
						$project->is_public = $request->input('job_public');

						if ( $project->save() ) {
							$contracts = Contract::where('contracts.project_id', $job_id)
												 ->get();

							foreach ($contracts as $key => $contract) {
								$contract->title = $request->input('title');
								$contract->save();
							}

							// Project Skills
							ProjectSkill::where('project_id', $project->id)->delete();
							if ( $request->input('job_skills') ) {
								$skill_ids = $request->input('job_skills');
								$inx = 0;
								foreach ( $skill_ids as $skill_id ) {
									if ( $skill_id ) {
										$projectSkill = new ProjectSkill;
										$projectSkill->project_id = $project->id;
										$projectSkill->skill_id = $skill_id;
										$projectSkill->order = $inx;
										$projectSkill->save();
										$inx++;
									}
								}
							}

							// Featured job
							if ( $request->input('job_featured') ) {

								if ( $featured_fee > 0 ) {
									
									// Check the sender's wallet
									if ( $user->wallet->amount >= $featured_fee ) {

										$payment = TransactionLocal::payFee($user->id, TransactionLocal::TYPE_FEATURED_JOB, $featured_fee, $project->id);

										if ( !$payment ) {
											add_message(trans('message.buyer.job.post.failed_payment_for_featured'), 'danger');
										} else {
											$project->is_featured = $request->input('job_featured');
											$project->save();
										}

									}
								}
							}

							$buyer_name = $user->fullname();
							$project_name = $project->subject;
							$project_url = _route('job.view', ['id' => $project->id], true, null, $user);
							
							if ( empty($job_id) && $request->input('action') != 'save_draft' ) {

								add_message(trans('message.buyer.job.post.success_create', ['job_title' => $project->subject]), 'success');

								// Send email to buyer
								EmailTemplate::send($user, 'JOB_POSTED', 2, [
									'USER' => $buyer_name,
									'JOB_POSTING' => $project_name,
									'JOB_POSTING_URL' => $project_url,
								]);

								// Send invitation
								if ( $request->input('invite_to') ) {
									JobController::do_send_invitation($project->id, $user->id, $request->input('invite_to'));
								}

								// Store new project flag in session
								Session::put('new_project', $project->id);

							} else if ( $request->input('action') == 'post_job' ) {
								
								// Send email to freelancers applied to this job
								if ( $project->applications->count() ) {
									foreach ( $project->applications as $application ) {
										EmailTemplate::send($application->user, 'JOB_UPDATED', 1, [
											'USER' => $application->user->fullname(),
											'JOB_POSTING' => $project_name,
											'JOB_POSTING_URL' => $project_url,
										]);
									}
								}

								add_message(trans('message.buyer.job.post.success_update', ['job_title' => $project->subject]), 'success');

								// Send email to buyer
								EmailTemplate::send($user, 'JOB_UPDATED', 2, [
									'USER' => $buyer_name,
									'JOB_POSTING' => $project_name,
									'JOB_POSTING_URL' => $project_url,
								]);

							}  else if ( $request->input('action') == 'repost_job' ) {

								add_message(trans('message.buyer.job.post.success_repost', ['job_title' => $project->subject]), 'success');

								// Repost a job notification
								Notification::send(Notification::BUYER_JOB_REPOSTED, 
								 	SUPERADMIN_ID, 
								 	$user->id,
								 	['job_title' => sprintf('%s', $project->subject)]
								);

								// Send email to buyer
								EmailTemplate::send($user, 'JOB_REPOSTED', 2, [
									'USER' => $buyer_name,
									'JOB_POSTING' => $project_name,
									'JOB_POSTING_URL' => $project_url,
								]);

							}

							if ( $request->input('action') == 'save_draft' ) {
								add_message(trans('message.buyer.job.post.success_draft'), 'success');
								return redirect()->route('job.all_jobs', ['type' => 'draft']);
							}

							$user->updateLastActivity();

							return redirect()->to(_route('job.overview', ['id' => $project->id]));

						}

					}
					
				} catch (Exception $e) {
					Log::error('JobController.php [edit_job] : ' . $e->getMessage());
					return redirect()->route('job.create');
				}
			}
		} catch ( Exception $e ) {
			Log::error('JobController.php [edit_job] : ' . $e->getMessage());
			return redirect()->route('job.create');
		}

		// Get the files
		$files = [];
		if ( $project->files ) {
			foreach ( $project->files as $file ) {
				$files[] = $file->id;
			}
		}
		$files = implode(',', $files);

		$this->setPageTitle(!empty($project_id) ? trans('page.buyer.job.edit.title') : trans('page.buyer.job.create.title') . ' - ' . trans('page.title'));

		return view('pages.buyer.job.edit', [
			'page' => 'buyer.job.edit',

			'page_submitted' => $page_submitted,
			'invite_to' => $request->input('invite_to') ? $request->input('invite_to') : 0,
			'job'  => $project, 
			'files' => $files,
			'action' => $action,
			'project_id' => $project_id,
			
			'j_trans' => [
				'show_qualifications' => trans('job.show_qualifications'), 
				'hide_qualifications' => trans('job.hide_qualifications'),         
				'close_job' => trans('j_message.buyer.job.status.close_job'), 
				'change_public' => trans('j_message.buyer.job.status.change_public'), 
				'status' => [
					'private' => strtolower(trans('common.private')), 
					'public'  => strtolower(trans('common.public')),
					'protected' => strtolower(trans('common.protected')),
				],
				'MAX_FIXED_PRICE' => MAX_FIXED_PRICE,
			]
		]);
	}

	/**
	* Overview for the job
	*
	* @author Ro Un Nam
	* @since May 31, 2017
	*/
	public function overview(Request $request, $job_id, $user_id = null)
	{
		$job = Project::find($job_id);
		if (!Auth::user()->isAdmin()) {
			$job = Project::findByUnique($job_id);
			$job_id = $job->id;
		}

		if ( !$job ) {
			abort(404);
		}

		if (empty($user_id)) {
			$user = Auth::user();
			if ($user->isAdmin()) {
				$user = User::find($job->client_id);
			}
		} else {
			$user = User::find($user_id);
		}

		try {
			if ( !$job->checkIsAuthor($user->id) ) {
				throw new Exception();
			}

			if (!Auth::user()->isAdmin())
				if ( $job->status == Project::STATUS_DRAFT && $user->id == $job->client_id ) {
					return redirect()->to(_route('job.edit', ['id' => $job->id]));
				} elseif ( $job->status == Project::STATUS_DRAFT && $user->id != $job->client_id ) {
					return back();
				}

			// Check accept terms
			if ( $request->isMethod('post') && $request->input('job_accept_term') == '1' ) {
				
				$job->accept_term = Project::ACCEPT_TERM_YES;
				if ( !$job->save() ) {
					$error = trans('job.error_job_accept_term');
					add_message($error, 'danger');
				}

				$user->updateLastActivity();

				return redirect()->to(_route('job.overview', ['id' => $job_id]));
			}

			// Get the total count
			$counts = [
				'review_proposals' => 0,
				'interviews' => 0,
				'hires' => 0,
				'offers' => 0,
			];

			$counts['review_proposals'] = $job->totalProposalsCount();

			$counts['interviews'] = $job->totalInterviewsCount();

			$counts['hires'] = Contract::totalHiresCount($job_id);

			$counts['offers'] = Contract::totalOffersCount($job_id);

			$desc = nl2br($job->desc);
			$less_desc = substr($desc, 0, Project::VIEWABLE_TEXT_LENGTH);

			// Check the flag about new project
			$new_project = Session::get('new_project');
			Session::forget('new_project');

			$user = ViewUser::find($user->id);

			$this->setPageTitle(trans('page.buyer.job.overview.title', ['job' => $job->subject]) . ' - ' . trans('page.title'));
			
			return view('pages.buyer.job.overview', [
				'page' => (Auth::user()->isAdmin() ? 'super.'.(!empty($user_id)?'user.buyer.':'').'job.overview' : 'buyer.job.overview'),
				'user' => $user,
				'job' => $job,
				'desc' => render_more_less_desc($job->desc),
				'counts' => $counts,
				'new_project' => $new_project,
				'j_trans' => [
					'close_job' 	 => trans('j_message.buyer.job.status.close_job'), 
					'cancel_job' 	 => trans('j_message.buyer.job.status.cancel_job'), 
					'change_public'  => trans('j_message.buyer.job.status.change_public'), 
					'status' => [
						'private' 	 => strtolower(trans('common.private')), 
						'public'  	 => strtolower(trans('common.public')),
						'protected'  => strtolower(trans('common.protected')),
					],
				]				
			]);
		} catch(Exception $e) {
			return redirect()->route('job.all_jobs');
		}
	}

	/**
	* Show freelancers to invite to the job
	*
	* @author Ro Un Nam
	* @since May 19, 2017
	*/
	public function invite_freelancers(Request $request, $job_id, $page = '', $user_id = null) {
		$job = Project::find($job_id);
		if (!Auth::user()->isAdmin()) {
			$job = Project::findByUnique($job_id);
			$job_id = $job->id;
		}

		if ( !$job ) {
			abort(404);
		}

		if (empty($user_id)) {
			$user = Auth::user();
			if ($user->isAdmin()) {
				$user = User::find($job->client_id);
			}
		} else {
			$user = User::find($user_id);
		}

		$filtered = false;

		try {
			// Check the page access
			if ( $page && !in_array($page, ['past', 'saved', 'invited']) ) {
				throw new Exception();
			}

			$job = Project::findOrFail($job_id);
			if ( !$job->checkIsAuthor($user->id) ) {
				throw new Exception();
			}

			if (!Auth::user()->isAdmin())	
				if ( $job->status == Project::STATUS_DRAFT && $user->id == $job->client_id ) {
					return redirect()->to(_route('job.edit', ['id' => $job->id]));
				} elseif ( $job->status == Project::STATUS_DRAFT && $user->id != $job->client_id ) {
					return back();
				}

			// Check accept terms
			if ( $request->isMethod('post') && $request->input('job_accept_term') == '1' ) {
				
				$job->accept_term = Project::ACCEPT_TERM_YES;
				if ( !$job->save() ) {
					$error = trans('job.error_job_accept_term');
					add_message($error, 'danger');
				}

				$user->updateLastActivity();

				return redirect()->to(_route('job.invite_freelancers', ['id' => $job_id]));
			}

			$per_page = Config::get('settings.freelancer.per_page');
			
			// Filtering
			$filtered = false;
			$params = [];

			$params['keyword'] = $request->input('q');
			$params['category'] = $request->input('c');
			$params['job_success'] = $request->input('js');
			$params['hourly_rate'] = $request->input('hr');
			$params['hours_billed'] = $request->input('hb');
			$params['feedback'] = $request->input('f');
			$params['activity'] = $request->input('a');
			$params['english_level'] = $request->input('el');
			$params['locations'] = $request->input('l');
			$params['title'] = $request->input('t');
			$params['languages'] = $request->input('ln');

			if ( $params['category'] || $params['job_success'] || $params['hourly_rate'] || $params['hours_billed'] || $params['feedback'] || $params['activity'] || $params['english_level'] || $params['locations'] || $params['title'] || $params['languages'] ) {
				
				$filtered = true;
			}

			$request->flash();

			$urlParams = [
				'q' => $params['keyword'],
				'c' => $params['category'],
				'js' => $params['job_success'],
				'hr' => $params['hourly_rate'],
				'hb' => $params['hours_billed'],
				'f' => $params['feedback'],
				'a' => $params['activity'],
				'el' => $params['english_level'],
				'l' => $params['locations'],
				't' => $params['title'],
				'ln' => $params['languages']
			];

			// Get the total count
			$counts = [
				'review_proposals' => 0,
				'interviews' => 0,
				'hires' => 0,
				'offers' => 0,
			];

			$counts['review_proposals'] = $job->totalProposalsCount();

			$counts['interviews'] = $job->totalInterviewsCount();

			$counts['hires'] = Contract::totalHiresCount($job_id);

			$counts['offers'] = Contract::totalOffersCount($job_id);

			$total_invited_freelancers = count(User::searchFreelancers([], $per_page, 'invited', $job_id));

			// Get the freelancers
			$freelancers = User::searchFreelancers($params, $per_page, $page, $job_id);

			// Check the flag about new project
			$new_project = Session::get('new_project');
			Session::forget('new_project');

			$user = ViewUser::find($user->id);

			$this->setPageTitle(trans('page.buyer.job.invite_freelancers.title', ['job' => $job->subject]) . ' - ' . trans('page.title'));

			return view('pages.buyer.job.invite_freelancers', [
				'page' => (Auth::user()->isAdmin() ?'super.'.(!empty($user_id)?'user.buyer.':'').'job.invitation' : 'buyer.job.invite_freelancers'),
				'user' => $user,
				'sub_page' => $page,
				'job' => $job,
				'freelancers' => $freelancers,
				'counts' => $counts,
				'total_invited_freelancers' => $total_invited_freelancers,
				'filtered' => $filtered,
				'params' => $urlParams,
				'page_route' => _route('job.invite_freelancers', ['id' => $job_id]),
				'new_project' => $new_project,
				'j_trans' => [
					'close_job' => trans('j_message.buyer.job.status.close_job'), 
					'cancel_job' => trans('j_message.buyer.job.status.cancel_job'), 
					'change_public' => trans('j_message.buyer.job.status.change_public'), 
					'status' => [
						'private' => strtolower(trans('common.private')), 
						'public'  => strtolower(trans('common.public')),
						'protected'  => strtolower(trans('common.protected')),
					],
					'job_invite' => trans('common.invite_x_to_the_job'),
					'job_message' => trans('common.message'),
					'job_place_holder_invitation_message' => trans('job.place_holder_invitation_message_js'),
					'job_characters_left' => trans('common.characters_left'),
					'job_send_invitation' => trans('common.send_invitation'),
					'invitation_sent' => trans('common.invitation_sent'),
				]
			]);
		} catch(Exception $e) {
			Log::error('[invite_freelancers() in JobController.php] ' . $e->getMessage());
			return redirect()->route('job.all_jobs');
		}
	}

	/**
	* Proposals for the job
	*
	* @author Ro Un Nam
	* @since May 30, 2017
	*/
	public function interviews(Request $request, $job_id, $page = '', $user_id = null)
	{
		$job = Project::find($job_id);
		if (!Auth::user()->isAdmin()) {
			$job = Project::findByUnique($job_id);
			$job_id = $job->id;
		}

		if ( !$job ) {
			abort(404);
		}

		if (empty($user_id)) {
			$user = Auth::user();
			if ($user->isAdmin()) {
				$user = User::find($job->client_id);
			}
		} else {
			$user = User::find($user_id);
		}

		try {
			$job = Project::findOrFail($job_id);

			$qualification_success_score = $job->qualification_success_score;
			$qualification_hours = $job->qualification_hours;
			$qualification_location = $job->qualification_location;

			if ( !$job->checkIsAuthor($user->id) ) {
				throw new Exception();
			}

			// Check the page access
			if ( $page && !in_array($page, ['archived']) ) {
				throw new Exception();
			}
			if (!Auth::user()->isAdmin())
				if ( $job->status == Project::STATUS_DRAFT && $user->id == $job->client_id ) {
					return redirect()->to(_route('job.edit', ['id' => $job->id]));
				} elseif ( $job->status == Project::STATUS_DRAFT && $user->id != $job->client_id ) {
					return back();
				}

			// Get the total count
			$counts = [
				'review_proposals' => 0,
				'interviews' => 0,
				'hires' => 0,
				'offers' => 0,
			];

			$counts['review_proposals'] = $job->totalProposalsCount();

			$counts['interviews'] = $job->totalInterviewsCount();

			$counts['hires'] = Contract::totalHiresCount($job_id);

			$counts['offers'] = Contract::totalOffersCount($job_id);

			// Check accept terms
			if ( $request->isMethod('post') && $request->input('job_accept_term') == '1' ) {

				$job->accept_term = Project::ACCEPT_TERM_YES;
				if ( !$job->save() ) {
					$error = trans('job.error_job_accept_term');
					add_message($error, 'danger');
				}

				$user->updateLastActivity();

				return redirect()->to(_route('job.interviews', ['id' => $job_id]));
				
			}

			// Project Ajax Actions
			if ( $request->ajax() && !Auth::user()->isAdmin()) {
				$json = ['success' => false];

				if ( $job->isSuspended() )  {
					return response()->json($json);
				}

				$action = $request->input('action');

				if ( $action == 'read' ) {
					$ids = $request->input('ids');

					if ( $ids ) {
						foreach ( $ids as $id ) {
							$project_application = ProjectApplication::findOrFail($id);
							$project_application->is_checked = 1;
							if ( $project_application->save() ) {
								$json['success'] = true;
							}
						}
					}

					return response()->json($json);
				}

				$id = $request->input('id');

				$project_application = ProjectApplication::findOrFail($id);
				if ( $project_application ) {
					switch ($action) {
						case 'like':
							$project_application->is_liked = 1;
							$project_application->is_checked = 1;
							if ( $project_application->save() ) {
								$json['success'] = true;
							}

							break;

						case 'dislike':
							$project_application->is_liked = -1;
							$project_application->is_checked = 1;
							if ( $project_application->save() ) {
								$json['success'] = true;
							}

							break;

						case 'archive':
							$project_application->is_archived = ProjectApplication::IS_ARCHIVED_YES;
							$project_application->is_checked = 1;
							if ( $project_application->save() ) {
								$json['success'] = true;
							}

							break;

						case 'unarchive':
							$project_application->is_archived = ProjectApplication::IS_ARCHIVED_NO;
							$project_application->is_checked = 1;
							if ( $project_application->save() ) {
								$json['success'] = true;
							}

							break;

						case 'decline':
							$project_application->is_declined = ProjectApplication::IS_CLIENT_DECLINED;
							$project_application->is_checked = 1;
							$project_application->reason = $request->input('decline_message');
							$project_application->decline_reason = $request->input('reason');
							if ( $project_application->save() ) {
								$json['success'] = true;

								// Send email to freelancer
								$freelancer_name = $project_application->user->fullname();

								EmailTemplate::send($project_application->user, 'PROPOSAL_REJECTED', 1, [
									'USER' => $freelancer_name,
									'JOB_POSTING' => $project_application->project->subject,
									'JOB_POSTING_URL' => _route('job.view', ['id' => $project_application->project->id], true, null, $project_application->user),
									'REASON' => $project_application->declined_reason_string(),
									'MESSAGE' => $project_application->reason,
									'SEARCH_JOB_URL' => route('search.job'),
								]);
							}

							break;

						case 'send_message':
							$message = strip_tags($request->input('message'));
							
							// Send email to freelancer
							if ( $project_application->status == ProjectApplication::STATUS_ACTIVE ) {
								// $project_name = sprintf('<a href="%s">%s</a>', _route('job.view', ['id' => $project_application->project->id]), $project_application->project->subject);

								// EmailTemplate::send($project_application->user, 'INTERVIEW_RECEIVED', 1, [
								// 	'USER' => $project_application->user->fullname(),
								// 	'PROJECT' => $project_name,
								// ]);
							}

							$message_id = $project_application->sendMessage($message, $user->id);

							if ( $message_id ) {
								$json['user'] = $user->fullname();
								$json['avatar'] = avatar_url($user);
								$json['message'] = $message;
								$json['time'] = ago(date('Y-m-d H:i:s'));
								$json['owner'] = 1;
								$json['success'] = true;
								$json['message_thread_id'] = $project_application->messageThread->id;
							}

							break;

						case 'more':

							$project_application->is_checked = 1;
							if ( $project_application->save() ) {
								$json['success'] = true;
							}

							break;

						default:
							break;
					}

					$user->updateLastActivity();
				}
				
				return response()->json($json);
			}

			$per_page = Config::get('settings.buyer.proposals.per_page');
			
			$sort = $request->input('sort') ? $request->input('sort') : '';
			$show = $request->input('show') ? $request->input('show') : '';

			// Get total count of archived proposals
			$count_archived = ProjectApplication::where('project_id', $job_id)
												->where(function($query) {
										          	$query->whereIn('status', [
														ProjectApplication::STATUS_PROJECT_CANCELLED,
														ProjectApplication::STATUS_HIRING_CLOSED,
														ProjectApplication::STATUS_PROJECT_EXPIRED
													])
									          		->orWhere('is_declined', '<>', ProjectApplication::IS_DECLINED_NO)
									          		->orWhere('is_archived', ProjectApplication::IS_ARCHIVED_YES);
										        });
			if ( $show == 'interviewing' ) {
				$count_archived = $count_archived->where('status', ProjectApplication::STATUS_ACTIVE);
			} else if ( $show == 'shortlisted' ) {
				$count_archived = $count_archived->where('is_liked', 1);
			}
			$count_archived = $count_archived->select('id')->count();

			// Get total count of active proposals
			$count_active = ProjectApplication::where('project_id', $job_id)
												->where('is_declined', ProjectApplication::IS_DECLINED_NO)
												->where('is_archived', ProjectApplication::IS_ARCHIVED_NO);
			if ( $show == 'interviewing' ) {
				$count_active = $count_active->where('status', ProjectApplication::STATUS_ACTIVE);
			} else if ( $show == 'shortlisted' ) {
				$count_active = $count_active->whereIn('status', [
												ProjectApplication::STATUS_NORMAL,
												ProjectApplication::STATUS_ACTIVE,
												ProjectApplication::STATUS_HIRED,
											])->where('is_liked', 1);
			} else {
				$count_active = $count_active->whereIn('status', [
												ProjectApplication::STATUS_NORMAL,
												ProjectApplication::STATUS_ACTIVE,
												ProjectApplication::STATUS_HIRED,
											]);
			}
			$count_active = $count_active->select('id')->count();
			
			// Get the interviews
			if ( $page == 'archived' ) {
				// Get the archived proposals
				$proposals = ProjectApplication::where('project_id', $job_id)
												->where(function($query) {
										          	$query->whereIn('status', [
														ProjectApplication::STATUS_PROJECT_CANCELLED,
														ProjectApplication::STATUS_HIRING_CLOSED,
														ProjectApplication::STATUS_PROJECT_EXPIRED
													])
									          		->orWhere('is_declined', '<>', ProjectApplication::IS_DECLINED_NO)
									          		->orWhere('is_archived', ProjectApplication::IS_ARCHIVED_YES);
										        })
												->orderBy('is_featured', 'DESC');
			} else {
				// Get the active interviews
				$proposals = ProjectApplication::where('project_id', $job_id)
												->whereIn('status', [
													ProjectApplication::STATUS_NORMAL,
													ProjectApplication::STATUS_ACTIVE,
													ProjectApplication::STATUS_HIRED,
												])
												->where('is_declined', ProjectApplication::IS_DECLINED_NO)
												->where('is_archived', ProjectApplication::IS_ARCHIVED_NO)
												->orderBy('is_featured', 'DESC')
												->orderBy('status', 'DESC');
			}

			if ( $sort == '' || $sort == 'oldest' || $sort == 'lowest_price' ) {
				if ( $show == 'shortlisted' ) {
					$proposals = $proposals->where('is_liked', 1);
				} else if ( $show == 'interviewing' ) {
					$proposals = $proposals->where('status', ProjectApplication::STATUS_ACTIVE);
				}

				if ( $sort == '' ) {
					$proposals = $proposals->orderBy('created_at', 'DESC');
				} else if ( $sort == 'oldest' ) {
					$proposals = $proposals->orderBy('created_at', 'ASC');
				} else {
					$proposals = $proposals->orderBy('price', 'ASC');
				}

				$proposals = $proposals->paginate($per_page);
			} else if ( $sort == 'best_match' ) {

				$columns = "
						p.id, 
						p.project_id, 
						p.user_id,
						p.provenance,
						p.type, 
						p.price,
						p.cv,
						p.status,
						p.is_declined,
						p.is_archived,
						p.is_featured,
						p.is_checked,
						p.is_liked, 
						p.memo, 
						p.decline_reason, 
						p.reason, 
						p.duration, 
						p.created_at, 
						p.updated_at, 
						us.job_success,
						(IF(us.job_success < " . $qualification_success_score . ", 0, 1) + IF(us.hours < " . $qualification_hours . ", 0, 1) + IF(c.region = '" . $qualification_location . "', 1, 0)) AS total
				";
				$countColumns = "COUNT(*) AS count";

				if ( $page == 'archived' ) {
					$query = "
			        	SELECT
			        		{{columns}}
						FROM project_applications p
						LEFT JOIN user_stats us ON p.user_id = us.user_id
						LEFT JOIN user_contacts uc ON p.user_id = uc.user_id
						LEFT JOIN countries c ON uc.country_code = c.charcode
						WHERE p.project_id = " . $job->id . " 
							AND (p.status IN (" . ProjectApplication::STATUS_PROJECT_CANCELLED . "," . ProjectApplication::STATUS_HIRING_CLOSED . "," . ProjectApplication::STATUS_PROJECT_EXPIRED . ")  
							AND (p.is_declined != " . ProjectApplication::IS_DECLINED_NO . " 
								OR p.is_archived = " . ProjectApplication::IS_ARCHIVED_YES . ")";

				} else {
					$query = "
			        	SELECT
			        		{{columns}}
						FROM project_applications p
						LEFT JOIN user_stats us ON p.user_id = us.user_id
						LEFT JOIN user_contacts uc ON p.user_id = uc.user_id
						LEFT JOIN countries c ON uc.country_code = c.charcode
						WHERE p.project_id = " . $job->id . " 
							AND (p.status = " . ProjectApplication::STATUS_NORMAL . " 
								OR p.status = " . ProjectApplication::STATUS_ACTIVE . " 
								OR p.status = " . ProjectApplication::STATUS_HIRED . ") 
							AND p.is_declined = " . ProjectApplication::IS_DECLINED_NO . " 
							AND p.is_archived = " . ProjectApplication::IS_ARCHIVED_NO;

				}

				if ( $show == 'shortlisted' ) {
					$query .= " AND p.is_liked = 1";
				} else if ( $show == 'interviewing' ) {
					$query .= " AND p.status = " . ProjectApplication::STATUS_ACTIVE;
				}

		        $proposals_count = DB::select(str_replace('{{columns}}', $countColumns, $query))[0]->count;

		        $pagination_page = $request->input('page', 1);    

		        $query .= "
		        	ORDER BY p.is_featured DESC, total DESC, p.status DESC, p.created_at ASC
					LIMIT " . ($pagination_page - 1) * $per_page . ", " . $per_page;

		        $proposals = DB::select(str_replace('{{columns}}', $columns, $query));

		        if ($proposals_count == 1 && $proposals[0]->id == null) {
		        	$proposals = [];
		        	$proposals_count = 0;
		        }

		        $proposals = new Paginator($proposals, $proposals_count, $per_page, $pagination_page, [
		            'path'  => $request->url(),
		            'query' => $request->query(),
		        ]);
			}

			// Check the flag about new project
			$new_project = Session::get('new_project');
			Session::forget('new_project');

			$user = ViewUser::find($user->id);

			$this->setPageTitle(trans('page.buyer.job.interviews.title', ['job' => $job->subject]) . ' - ' . trans('page.title'));

			// Handle urls
			$url_params = [
				'id' => $job->id, 
				'user_id' => $user_id,
			];

			if ( $sort ) {
				$url_params['sort'] = $sort;
			}

			if ( $show) {
				$url_params['show'] = $show;
			}

			$url_active = _route('job.interviews', $url_params);

			$url_params['page'] = 'archived';

			$url_archived = _route('job.interviews_page', $url_params);
			
			return view('pages.buyer.job.interviews', [
				'page' => (Auth::user()->isAdmin() ?'super.'.(!empty($user_id)?'user.buyer.':'').'job.interview' : 'buyer.job.interviews'),
				'user' => $user,
				'user_id' => $user_id,
				'sub_page' => $page,
				'url_active' => $url_active,
				'url_archived' => $url_archived,
				'job' => $job,
				'sort' => $sort,
				'show' => $show,
				'proposals' => $proposals,
				'count_active' => $count_active,
				'count_archived' => $count_archived,
				'counts' => $counts,
				'qualification_success_score' => $qualification_success_score,
				'qualification_hours' => $qualification_hours,
				'qualification_location' => $qualification_location,
				'new_project' => $new_project,
				'j_trans' => [
					'close_job' => trans('j_message.buyer.job.status.close_job'), 
					'cancel_job' => trans('j_message.buyer.job.status.cancel_job'), 
					'change_public' => trans('j_message.buyer.job.status.change_public'), 
					'status' => [
						'private' => strtolower(trans('common.private')), 
						'public'  => strtolower(trans('common.public')),
						'protected'  => strtolower(trans('common.protected')),
					],
					'archive' => trans('common.archive'),
					'undo' => trans('common.undo'),
					'like' => trans('common.like'),
					'dislike' => trans('common.dislike'),
					'no_active_proposals' => trans('job.no_active_proposals'),
					'no_archived_proposals' => trans('job.no_archived_proposals'),
					'no_shortlisted_proposals' => trans('job.no_shortlisted_proposals'),
					'no_interviews' => trans('job.no_interviews'),
					'send_message' => trans('common.send_message'),
					'interviews' => trans('common.interviews'),
				]				
			]);
		} catch(Exception $e) {
			Log::error('JobController.php [interviews] - ' . $e->getMessage());
			return redirect()->route('job.all_jobs');
		}
	}

	/**
	* Hire & Offers for the job
	*
	* @author Ro Un Nam
	* @since May 30, 2017
	*/
	public function hire_offers(Request $request, $job_id, $user_id = null)
	{
		$job = Project::find($job_id);
		if (!Auth::user()->isAdmin()) {
			$job = Project::findByUnique($job_id);
			$job_id = $job->id;
		}

		if ( !$job ) {
			abort(404);
		}

		if (empty($user_id)) {
			$user = Auth::user();
			if ($user->isAdmin()) {
				$user = User::find($job->client_id);
			}
		} else {
			$user = User::find($user_id);
		}

		try {
			$job = Project::findOrFail($job_id);
			if ( !$job->checkIsAuthor($user->id) ) {
				throw new Exception();
			}

			if (!Auth::user()->isAdmin())
				if ( $job->status == Project::STATUS_DRAFT && $user->id == $job->client_id ) {
					return redirect()->to(_route('job.edit', ['id' => $job->id]));
				} elseif ( $job->status == Project::STATUS_DRAFT && $user->id != $job->client_id ) {
					return back();
				}

			// Check accept terms
			if ( $request->isMethod('post') && $request->input('job_accept_term') == '1' ) {
				
				$job->accept_term = Project::ACCEPT_TERM_YES;
				if ( !$job->save() ) {
					$error = trans('job.error_job_accept_term');
					add_message($error, 'danger');
				}

				$user->updateLastActivity();

				return redirect()->to(_route('job.hire_offers', ['id' => $job_id]));
			}

			// Get the total count
			$counts = [
				'review_proposals' => 0,
				'interviews' => 0,
				'hires' => 0,
				'offers' => 0,
			];

			$counts['review_proposals'] = $job->totalProposalsCount();

			$counts['interviews'] = $job->totalInterviewsCount();

			$counts['hires'] = Contract::totalHiresCount($job_id);

			$counts['offers'] = Contract::totalOffersCount($job_id);

			// Get the offers
			$status = [
                Contract::STATUS_OFFER,
                Contract::STATUS_WITHDRAWN,
                Contract::STATUS_REJECTED,
            ];

			$offers = Contract::where('project_id', $job_id)
								->whereIn('status', $status)
								->orderBy('created_at', 'DESC')
								->get();

			// Get the contracts
			$status = [
                Contract::STATUS_OPEN,
                Contract::STATUS_PAUSED,
                Contract::STATUS_SUSPENDED,
                Contract::STATUS_CLOSED,
            ];

			$contracts = Contract::where('project_id', $job_id)
								->whereIn('status', $status)
								->orderBy('created_at', 'DESC')
								->get();

			$request->flash();

			// Check the flag about new project
			$new_project = Session::get('new_project');
			Session::forget('new_project');

			$user = ViewUser::find($user->id);

			$this->setPageTitle(trans('page.buyer.job.hire_offers.title', ['job' => $job->subject]) . ' - ' . trans('page.title'));
			
			return view('pages.buyer.job.hire_offers', [
				'page' => (Auth::user()->isAdmin() ?'super.'.(!empty($user_id)?'user.buyer.':'').'job.hire_offers' : 'buyer.job.hire_offers'),
				'user' => $user,
				'user_id' => $user_id,
				'job' => $job,
				'offers' => $offers,
				'contracts' => $contracts,
				'counts' => $counts,
				'new_project' => $new_project,
				'j_trans' => [
					'close_job' => trans('j_message.buyer.job.status.close_job'), 
					'cancel_job' => trans('j_message.buyer.job.status.cancel_job'), 
					'change_public' => trans('j_message.buyer.job.status.change_public'), 
					'status' => [
						'private' => strtolower(trans('common.private')), 
						'public'  => strtolower(trans('common.public')),
						'protected'  => strtolower(trans('common.protected')),
					],
					'you_have_no_hire_offers' => trans('job.you_have_no_hire_offers'),
					'withdrawn_by_you' => trans('common.withdrawn_by_you'),
				]				
			]);
		} catch(Exception $e) {
			return redirect()->route('job.all_jobs');
		}
	}
}