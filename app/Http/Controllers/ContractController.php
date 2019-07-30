<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Log;
use Storage;
use Config;
use Session;
use Exception;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\Role;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\Contract;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\ContractFeedback;
use iJobDesk\Models\ContractMeter;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\HourlyReview;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Notification;
use iJobDesk\Models\ProfileViewHistory;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\Settings;

use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\File;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Message;

class ContractController extends Controller {

	/**
	* Constructor
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* My All Contracts Page
	*
	* @author Ro Un Nam
	* @since Jun 16, 2017
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function all_contracts(Request $request, $tab = 'active')
	{
		$user = Auth::user();

		try {
			
			// Get all contracts
			$settings = Config::get('settings');

			$contracts = Contract::whereRaw(true);
			if ( $user->isFreelancer() ) {
				$per_page = $settings['freelancer']['contracts']['per_page'];
				$contracts->where('contractor_id', $user->id);
			} else {
				$per_page = $settings['buyer']['contracts']['per_page'];
				$contracts->where('buyer_id', $user->id);
			}

			$keywords = $request->input('keywords');
			if (!empty($keywords))
				$contracts->where('title', 'LIKE', '%' . $keywords . '%');

			if ($tab == 'active') {
				$contracts->whereIn('status', [
					Contract::STATUS_OPEN, 
					Contract::STATUS_PAUSED, 
					Contract::STATUS_SUSPENDED
				]);
			} else {
				$contracts->whereIn('status', [
					Contract::STATUS_CLOSED,
					Contract::STATUS_CANCELLED
				]);

				$contracts->orderBy(strtolower($user->role_name()) . '_need_leave_feedback', 'DESC');
				$contracts->orderBy('ended_at', 'DESC');
			}

			$contracts->orderBy('created_at', 'DESC');

			return view('pages.contract.my_contracts', [
				'page'        => 'contract.my_contracts',

				'tab'   		=> $tab,
				'keywords'   	=> $keywords,
				'contracts'   	=> $contracts->paginate($per_page),
				'total_closed_can_leave_feedback' => $user->totalClosedCanLeaveFeedback()
			]);

		} catch (Exception $e) {
			Log::error('ContractController.php [all_contracts] ' . $e->getMessage());
		}
	}

	public function my_freelancers(Request $request, $tab = 'hired') {
		view()->share('tab', $tab);

		if ($tab == 'saved')
			return $this->my_saved_freelancers_buyer($request);
		else
			return $this->my_hired_freelancers($request);
	}

	/**
	* My Freelancers (my-freelancers)
	*
	* @author nada
	* @since Apr 04, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function my_hired_freelancers(Request $request) {
		$user = Auth::user();

		$keywords = '';

		$contractors = [];
		$contractors_ids = [];
		$my_freelancers = [];

		$contracts = Contract::getContracts([
			'buyer_id' => $user->id,
		]);

		if ( $contracts ) {
			foreach ($contracts as $c) {
				if ( !$c->contractor )
					continue;

				$contractors[] = $c->contractor;
				$contractors_ids[] = $c->contractor_id;
			}
		}

		$filter_sort_by = 1;
		$my_freelancers = collect([]);

		if ($request->isMethod('post')) {

			if ($request->input('keywords')) {

				$keywords = $request->input('keywords');
				$query_recent_hired = User::leftJoin('contracts', 'users.id', '=', 'contracts.contractor_id')
										  ->leftJoin('user_contacts', 'users.id', '=', 'user_contacts.user_id')
										  ->groupBy('contractor_id')
										  ->orderBy('contracts.status', 'ASC')
										  ->orderBy('contracts.ended_at', 'DESC')
										  ->orderBy('contracts.started_at', 'DESC')
										  ->whereIn('users.id',$contractors_ids)
										  ->where(function($query) use ($keywords) {
									          $query->whereRaw('LOWER(user_contacts.first_name) LIKE "%' . trim(strtolower($keywords)) . '%"')
									          		->orWhereRaw('LOWER(user_contacts.last_name) LIKE "%' . trim(strtolower($keywords)) . '%"');
								          });

				$my_freelancers = $query_recent_hired->select('users.*')->get();
				
				$filter_sort_by = 0;
			}
			else {
				
				if ($request->input('sort_by') == 0) {
					$filter_sort_by = 1;
				}
				else {
					$filter_sort_by = $request->input('sort_by');
				}
			}
			
		}

		if ($filter_sort_by == 1) {
			$query_recent_hired = User::leftJoin('contracts', 'users.id', '=', 'contracts.contractor_id')
									  ->groupBy('contractor_id')
									  ->orderBy('contracts.status', 'ASC')
									  ->orderBy('contracts.ended_at', 'DESC')
									  ->orderBy('contracts.started_at', 'DESC')
									  ->whereIn('users.id',$contractors_ids);
			
			$my_freelancers = $query_recent_hired->select('users.*')->paginate(5);
		} 
		else if ($filter_sort_by == 2) {
			$query_job_success = User::leftJoin('user_stats', 'users.id', '=', 'user_stats.user_id')
									 ->orderBy('user_stats.job_success', 'DESC')
									 ->whereIn('users.id', $contractors_ids);

			$my_freelancers = $query_job_success->select('users.*')->get();
		}
		else if ($filter_sort_by == 3) {			
			$query_score = User::leftJoin('user_stats', 'users.id', '=', 'user_stats.user_id')
							   ->orderBy('user_stats.score', 'DESC')
							   ->whereIn('users.id', $contractors_ids);

			$my_freelancers = $query_score->select('users.*')->get();			
		}
		else if ($filter_sort_by == 4) {
			$query_availability = User::leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
									  ->orderBy('user_profiles.available', 'DESC')
									  ->whereIn('users.id', $contractors_ids);

			$my_freelancers = $query_availability->select('users.*')->get();			
		}
		else if ($filter_sort_by == 5) {			
			$query_high_rate = User::leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
								   ->orderBy('user_profiles.rate', 'DESC')
								   ->whereIn('users.id', $contractors_ids);

			$my_freelancers = $query_high_rate->select('users.*')->get();			
		}
		else if ($filter_sort_by == 6) {			
			$query_lower_rate = User::leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
									->orderBy('user_profiles.rate', 'ASC')
									->whereIn('users.id', $contractors_ids);

			$my_freelancers = $query_lower_rate->select('users.*')->get();			
		}

		foreach ($my_freelancers as $my_freelancer) {
			$contracts = Contract::where('contractor_id', '=', $my_freelancer->id)->where('buyer_id', '=', $user->id)->select('contracts.*')->get();
			$latest_contract = Contract::where('contractor_id', '=', $my_freelancer->id)->where('buyer_id', '=', $user->id)->orderBy('contracts.started_at', 'DESC')->select('contracts.*')->first();
			$my_freelancer->contracts = $contracts;
			$my_freelancer->latest_contract = $latest_contract;
		}	

		return view('pages.buyer.contract.my_freelancers', [
			'page'          => 'buyer.contract.my_freelancers',
			'freelancers' => $my_freelancers,
			'filter_sort_by' => $filter_sort_by,
			'keywords' => $keywords
		]);
	}

	/**
	* My saved Freelancers (my-freelancers)
	*
	* @author nada
	* @since Apr 04, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function my_saved_freelancers_buyer(Request $request) {
		$user = Auth::user();

		$keywords = $request->input('keywords', '');

		$savedhistorys = array();
		$savedhistorys = ProfileViewHistory::where('buyer_id', $user->id)->get();

		$freelancers = array();
		$user_ids = array();

		foreach ($savedhistorys as $savedhistory) {
			$user_id = $savedhistory->user_id;
			if ($user_id) {
				$freelancers[] = User::find($user_id);
				$user_ids[] = $user_id;
			}
		}

		if ($keywords) {
			$query_saved_hired = User::leftJoin('user_contacts', 'users.id', '=', 'user_contacts.user_id')
									->whereIn('users.id',$user_ids)
									->where(function($query) use ($keywords) {
							          	$query->whereRaw('LOWER(user_contacts.first_name) LIKE "%' . trim(strtolower($keywords)) . '%"')
							          			->orWhereRaw('LOWER(user_contacts.last_name) LIKE "%' . trim(strtolower($keywords)) . '%"');
							        });
	        $freelancers = $query_saved_hired->select('users.*')->get();			
		}

		return view('pages.buyer.contract.my_freelancers', [
			'page'          => 'buyer.contract.my_freelancers',
			'freelancers'   => $freelancers,
			'keywords' 		=> $keywords	
		]);
	}

	/**
	* Contract detail page (contract/@id)
	*
	* @author Ro Un Nam
	* @since Jun 05, 2017
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function contract_view(Request $request, $contract_id, $user_id = null) {
		if (empty($user_id)) {
			$user = Auth::user();
			if ($user->isAdmin()) {
				$user = User::find(Contract::find($contract_id)->buyer_id);
			}
		}
		else
			$user = User::find($user_id); // for admin

		try {
			$contract = Contract::find($contract_id);
			if (!Auth::user()->isAdmin()) { // if current user is not admin, contract is will be unique id, not primary key(ID)
				$contract = Contract::findByUnique($contract_id);
				$contract_id = $contract->id;
			}

			// Create Message Room if contract hasn't message room
			if ($contract->application)
				$thread = $contract->application->getMessageThread();

			if ( $user->isFreelancer() && !$contract->checkCurrentFreelancer($user->id) ) {
				throw new Exception();
			}

			if ( $user->isBuyer() && !$contract->checkIsAuthor($user->id) ) {
				throw new Exception();
			}

			if ( $contract->isOffer() ) {
				return redirect()->route('contract.all_contracts');
			}

			$balance = $user->myBalance();

			$total_paid = $contract->totalPaid();

			if ( $user->isFreelancer() ) {
				// Check the milestone changed
				if ( $contract->milestone_changed == '1' ) {
					$contract->milestone_changed = 0;
					$contract->save();
				}

				$total_paid_for_user = $contract->totalPaidForFreelancer();
				$total_paid_for_user_include_fee = $contract->totalPaidForFreelancerIncludeFee();
				$total_paid_pending = $contract->totalPaidPending();
			} else {
				$total_paid_for_user = $total_paid_for_user_include_fee = $total_paid;
				$total_paid_pending = 0;
			}

			if ( !$user->isSuspended() && !$contract->isSuspended() && $request->isMethod('post') && !$request->input('_class') ) { // _class: when going contract detail, it will display tab for message board more... in this case, it will use this variable.

				$action = $request->input('_action');

				// Currenet user is freelancer
				if ( $user->isFreelancer() ) {
					// Refund payment
					if ( $action == 'payment' ) {
						$amount = doubleval( $request->input('payment_amount') );
						$type   = $request->input('payment_type');
						$note   = $request->input('payment_note');

						// Check the sender's wallet
						$wallet = Wallet::account($user->id);

						if ( $wallet->amount < $amount ) {
							add_message(trans('message.not_enough_balance'), 'danger');
						} else {
							if ( $amount > 0 && $amount < $total_paid_for_user_include_fee ) {
								$res = TransactionLocal::refund([
									'cid'     => $contract_id, 
									'amount'  => $amount,
									'note'    => $note
								]);

								if ( $res['success'] ) {
									add_message(trans('message.freelancer.payment.contract.success_refund', ['amount' => formatCurrency($res['amount'])]), 'success');
								} else {
									if ( $res['message'] ) {
										add_message($res['message'], 'danger');
									} else {
										add_message(trans('message.freelancer.payment.failed'), 'danger');
									}
								}
							} else {
								add_message(trans('message.freelancer.payment.contract.failed_refund_amount_over_paid_amount'), 'danger');
							}
						}
					} else if ( $action == 'refund_fund' ) {
						$refund_result = TransactionLocal::refund_fund($contract_id, $request->input('_id'));
						if ( $refund_result['success'] ) {
							add_message(trans('message.freelancer.payment.contract.success_refund_fund'), 'success');
						} else {
							add_message(trans('message.freelancer.payment.contract.failed_refund_fund'), 'danger');
						}
					} else if ( $action == 'request_payment' ) {
						$milestone = ContractMilestone::findOrFail($request->input('_id'));
						$milestone->payment_requested = 1;
						$milestone->requested_at = date('Y-m-d H:i:s');

						if ( $milestone->save() ) {
							Notification::send(
								Notification::FREELANCER_REQUESTED_MILESTONE_PAYMENT, 
								SUPERADMIN_ID,
								$contract->buyer_id, 
								[
									'sender_name' => $user->fullname(), 
									'contract_title' => sprintf('%"', $contract->title),
									'milestone_name' => sprintf('%s', $milestone->name)
								]
							);

							EmailTemplate::send($contract->buyer, 'REQUEST_FUND', 2, [
								'USER' => $contract->buyer->fullname(),
								'FREELANCER' => $user->fullname(),
								'CONTRACT_TITLE' => $contract->title,
								'MILESTONE' => $milestone->name,
								'AMOUNT' => formatCurrency($milestone->getPrice()),
							]);

							add_message(trans('message.freelancer.payment.contract.success_request_payment'), 'success');
						} else {
							add_message(trans('message.freelancer.payment.contract.failed_request_payment'), 'danger');
						}
					}
				// Current user is buyer
				} else {
					$buyer_name = $contract->buyer->fullname();
					$freelancer_name = $contract->contractor->fullname();

					if ( $action == 'pause' ) {
						if ( !$contract->isPaused() ) {
							if ( $contract->pause() ) {
								add_message(trans('message.buyer.contract.paused'), 'success');
							}
						}
					} else if ( $action == 'restart' ) {
						if ( $contract->isPaused() ) {
							if ( $contract->restart() ) {
								add_message(trans('message.buyer.contract.restarted'), 'success');
							}
						}
					} else if ( $action == 'payment' ) {
						$amount = str_replace([',', ' '], ['', ''], $request->input('payment_amount'));
						$type   = $request->input('payment_type');
						$note   = $request->input('payment_note');

						if ( $amount > 0 ) {
							// Check the sender's wallet
							if ( $user->myBalance() < $amount ) {
								add_message(trans('message.not_enough_balance'), 'danger');

							} else {

								$res = TransactionLocal::pay([
									'cid'     => $contract_id, 
									'amount'  => $amount,
									'note'    => $note, 
								]);

								if ( $res['success'] ) {
									add_message(trans('message.buyer.payment.contract.success_paid', ['amount'=>formatCurrency($amount)]), 'success');
								} else {
									if ( $res['message'] ) {
										add_message($res['message'], 'danger');
									} else {
										add_message(trans('message.buyer.payment.failed'), 'danger');
									}
								}
							}
						} else {
							add_message(trans('message.error_empty_price'), 'danger');
						}
					} else if ( $action == 'weekly_limit' ) {
						$weekly_limit = intval($request->input('weekly_limit'));

						if ( $weekly_limit == -1 || ($contract->limit != -1 && $weekly_limit > $contract->limit) ) {
							$contract->limit = $weekly_limit;
							$contract->new_limit = 0;
							Notification::send(Notification::CONTRACT_WEEK_LIMIT_NO, 
								SUPERADMIN_ID,
								$contract->contractor_id, 
								[
									'buyer' => $buyer_name, 
									'contract' => sprintf('%s', $contract->title)
								]
							);
						} else {
							$contract->new_limit = $weekly_limit;

							Notification::send(Notification::CONTRACT_WEEK_LIMIT_HRS, 
								SUPERADMIN_ID,
								$contract->contractor_id, 
								[
									'buyer' => $buyer_name, 
									'contract' => sprintf('%s', $contract->title), 
									'limit' => $contract->limit
								]
							);
						}

						$contract->save();

						$contract->term_changed();

						add_message(trans('message.buyer.contract.changed_term'), 'success');
					} else if ( $action == 'edit_milestone' ) {
						$milestone_id = intval($request->input('_id'));

						if ( $milestone_id ) {
							$milestone = ContractMilestone::findOrFail($milestone_id);
						} else {
							$milestone = new ContractMilestone;
							$milestone->contract_id = $contract->id;
						}

						$milestone->name = $request->input('name');
						// $milestone->start_time = date('Y-m-d', strtotime($request->input('start_time')));
						$milestone->end_time = date('Y-m-d', strtotime($request->input('end_time')));
						$milestone->price = round2Decimal($request->input('price'));

						/*
						if ( $milestone->start_time > $milestone->end_time ) {
							add_message(trans('message.buyer.contract.milestones.error_milestone_date'), 'danger');
						} else {*/
							if ( $milestone->save() ) {
								if ( !$milestone_id ) {
									EmailTemplate::send($contract->contractor, 'MILESTONE_CREATED', 0, [
										'USER' => $contract->contractor->fullname(),
										'CONTRACT_TITLE' => $contract->title,
										'MILESTONE_TITLE' => $milestone->name,
									]);

									EmailTemplate::send($contract->buyer, 'MILESTONE_CREATED', 0, [
										'USER' => $contract->buyer->fullname(),
										'CONTRACT_TITLE' => $contract->title,
										'MILESTONE_TITLE' => $milestone->name,
									]);
								} else {
									EmailTemplate::send($contract->contractor, 'MILESTONE_UPDATED', 0, [
										'USER' => $contract->contractor->fullname(),
										'CONTRACT_TITLE' => $contract->title,
										'MILESTONE_TITLE' => $milestone->name,
									]);

									EmailTemplate::send($contract->buyer, 'MILESTONE_UPDATED', 0, [
										'USER' => $contract->buyer->fullname(),
										'CONTRACT_TITLE' => $contract->title,
										'MILESTONE_TITLE' => $milestone->name,
									]);
								}

								if ( $request->input('confirm_fund') ) {
									if ( $balance < $request->input('price') ) {
										add_message(trans('message.not_enough_balance'), 'danger');
									} else {
										if ( TransactionLocal::fund($contract->id, $milestone->id) ) {
											$milestone->fund_status = ContractMilestone::FUNDED;
											$milestone->save();
										} else {
											add_message(trans('message.buyer.contract.milestones.fund_milestone_error', ['milestone' => $milestone->name]), 'danger');
										}
									}
								}

								$contract->milestone_changed = Contract::MILESTONE_CHANGED_YES;
								$contract->save();

								if ( !$milestone_id ) {
									add_message(trans('message.buyer.contract.milestones.success_added_milestone'), 'success');
								} else {
									add_message(trans('message.buyer.contract.milestones.success_updated_milestone'), 'success');
								}
							} else {
								if ( !$milestone_id ) {
									add_message(trans('message.buyer.contract.milestones.failed_added_milestone'), 'danger');
								} else {
									add_message(trans('message.buyer.contract.milestones.failed_updated_milestone'), 'danger');
								}
							}
						// }
					} else if ( $action == 'delete_milestone' ) {
						$milestone = ContractMilestone::findOrFail($request->input('_id'));

						if ( $milestone->delete() ) {
							$contract->milestone_changed = Contract::MILESTONE_CHANGED_YES;
							$contract->save();

							add_message(trans('message.buyer.contract.milestones.success_deleted_milestone'), 'success');
						} else {
							add_message(trans('message.buyer.contract.milestones.failed_deleted_milestone'), 'danger');
						}
					} else if ( $request->input('AllowManualTime') == 1 ) {
						if ( $contract->is_allowed_manual_time ) {
							$contract->is_allowed_manual_time = 0;

							Notification::send(Notification::CONTRACT_NOT_ALLOWED_MANUAL_TIME, 
								SUPERADMIN_ID,
								$contract->contractor_id, 
								[
									'buyer' => $buyer_name, 
									'contract' => sprintf('%s', $contract->title)
								]
							);						
						} else {
							$contract->is_allowed_manual_time = 1;

							Notification::send(
								Notification::CONTRACT_ALLOWED_MANUAL_TIME, 
								SUPERADMIN_ID,
								$contract->contractor_id, 
								[
									"buyer" => $contract->buyer->fullname(), 
									"contract" => sprintf('%s', $contract->title)
								]
							);
						}

						$contract->save();

						$contract->term_changed();

						add_message(trans('message.buyer.contract.changed_term'), 'success');
					} else if ( $request->input('AllowOverTime') == 1 ) {
						if ( $contract->is_allowed_over_time ) {
							$contract->is_allowed_over_time = 0;
						} else {
							$contract->is_allowed_over_time = 1;
						}

						$contract->save();

						$contract->term_changed();

						add_message(trans('message.buyer.contract.changed_term'), 'success');
					} else if ( $action == 'fund' ) {
						$milestone = ContractMilestone::findOrFail($request->input('_id'));

						if ( $milestone->getPrice() > 0 ) {
							if ( $milestone->getPrice() > $balance ) {
								add_message(trans('message.not_enough_balance'), 'danger');
							} else {
								if ( !$milestone->transaction_id ) {
									if ( TransactionLocal::fund($contract_id, $milestone->id) ) {
										add_message(trans('message.buyer.contract.milestones.fund_milestone_success', ['milestone' => $milestone->name]), 'success');
									} else {
										add_message(trans('message.buyer.contract.milestones.fund_milestone_error', ['milestone' => $milestone->name]), 'danger');
									}
								}
							}
						} else {
							add_message(trans('message.buyer.contract.milestones.fund_milestone_error', ['milestone' => $milestone->name]), 'danger');
						}
					} else if ( $action == 'release') {
						$milestone = ContractMilestone::findOrFail($request->input('_id'));

						if ( !$milestone ) {
							throw new Exception('An error occured while releasing fund from invalid milestone.');
						}

						if ( $milestone->getPrice() > 0 ) {
							if ( !$milestone->isPending() && !$milestone->isReleased() ) {
								$result = TransactionLocal::release($contract_id, $milestone->id);
								if ( $result['success'] ) {
									add_message(trans('message.buyer.contract.milestones.release_milestone_success', ['milestone' => $milestone->name]), 'success');
								} else {
									if ( isset($result['message']) ) {
										add_message($result['message'], 'danger');
									} else {
										add_message(trans('message.buyer.payment.failed'), 'danger');
									}
								}
							}
						} else {
							add_message(trans('message.buyer.contract.milestones.fund_milestone_error', ['milestone' => $milestone->name]), 'danger');
						}

					}
				}

				$user->updateLastActivity();

				if (!Auth::user()->isAdmin())
					return redirect()->to(_route('contract.contract_view', ['id' => $contract_id]));
			}

			$milestones = $contract->milestones()->get();

			if ( $user->isFreelancer() ) {
				$transactions = TransactionLocal::where('contract_id', $contract->id)
												->where(function($query) {
													$query->where(function($query2) {
														$query2->whereIn('for', [
															TransactionLocal::FOR_FREELANCER, 
															TransactionLocal::FOR_IJOBDESK
														])
														->whereIn('type', [
															TransactionLocal::TYPE_FIXED,
															TransactionLocal::TYPE_HOURLY,
															TransactionLocal::TYPE_BONUS,
														]);
													})
													->orWhere(function($query2) {
														$query2->whereIn('for', [
															TransactionLocal::FOR_FREELANCER,
															TransactionLocal::FOR_IJOBDESK,
														])->where('type', TransactionLocal::TYPE_REFUND);
													});
												})
												->orderBy('id', 'desc')
												->get();
			} else {
				$transactions = TransactionLocal::where('contract_id', $contract->id)
												->where('for', TransactionLocal::FOR_BUYER)
												->whereIn('type', [
													TransactionLocal::TYPE_FIXED,
													TransactionLocal::TYPE_HOURLY,
													TransactionLocal::TYPE_BONUS,
													TransactionLocal::TYPE_REFUND
												])
												->orderBy('id', 'desc')
												->get();
			}

			// Dispute ticket
			$ticket = $contract->getOpenedDispute();
			
			$ticket_id = $ticket ? $ticket->id : null;

	        // Infinite Loading
	        // Dispute Comments
	        $query_builder = TicketComment::where('ticket_id', $ticket_id)
							              ->orderBy('created_at', 'DESC');

			Message::loadMessages($request, $query_builder); 

			if (Auth::user()->isAdmin()) {
				// Project Messages
				$query_builder = ProjectMessage::where('thread_id', $contract->application->messageThread->id)
	                                       	   ->orderBy('created_at', 'DESC');
	            Message::loadMessages($request, $query_builder, false, 'project_messages');
			}

	        // Closed or Solved Dispute Tickets
	        $solved_tickets = $contract->getSolvedDisputes();

			$user = ViewUser::find($user->id);
            $this_user = $user;

            list($last_week_from, $last_week_to) = weekRange('-1 weeks', 'Y-m-d');
            $last_week_from = date('M d', strtotime($last_week_from));
            $last_week_to = date('M d, Y', strtotime($last_week_to));

			// For only hourly contract
			$is_in_review = false;
			
			if ( $contract->isHourly() ) {
				$is_in_review = isInReview();

				$lastWeekReview = HourlyReview::getContractHourlyReview($contract->id);

				if ( $lastWeekReview ) {
					if ( !$lastWeekReview->isPending() || $lastWeekReview->isDisputed() ) {
						$is_in_review = false;
					}
				} else {
					$is_in_review = false;
				}
			}

			return view('pages.contract.contract_detail', [
				'page' => (Auth::user()->isAdmin() ? 'super.'.(!empty($user_id)?'user.commons.':'').'contract.detail' : 'contract.contract_detail'),
				'contract' => $contract,
				'balance' => $balance,
				'milestones' => $milestones,
				'transactions' => $transactions,
				'user' => $user,
				'total_paid' => $total_paid,
				'total_paid_for_user' => $total_paid_for_user,
				'total_paid_for_user_include_fee' => $total_paid_for_user_include_fee,
				'total_paid_pending' => $total_paid_pending,
				'total_funded' => $contract->totalFunded(),
				'total_bonus' => $contract->totalBonus(),
				'total_refunded' => $contract->totalRefunded(),
				'total_gross' => $contract->grossTotal(),
				'user_id' => $user_id,
				'this_user' => $this_user,
				'last_week_from' => $last_week_from,
				'last_week_to' => $last_week_to,
				'is_in_review' => $is_in_review,
				'j_trans' => [
					'notification_sent' => trans('contract.notification_sent'),
					'confirm_contract_pause' => trans('contract.confirm_contract_pause'),
					'confirm_contract_cancel' => trans('contract.confirm_contract_cancel'),
					'confirm_allow_manual_time' => trans('contract.confirm_allow_manual_time'),
					'confirm_disable_manual_time' => trans('contract.confirm_disable_manual_time'),
					'confirm_allow_over_time' => trans('contract.confirm_allow_over_time'),
					'confirm_disable_over_time' => trans('contract.confirm_disable_over_time'),
					'error_refund_amount_over_paid_amount' => trans('message.freelancer.payment.contract.failed_refund_amount_over_paid_amount'),
					'error_refund_amount_over_balance' => trans('message.freelancer.payment.contract.failed_refund_amount_over_balance'),
					'btn_ok' => trans('common.ok'),
					'btn_cancel' => trans('common.cancel'),
					'confirm_fund' => trans('contract.payment.confirm_fund'),
					'confirm_refund_escrow' => trans('contract.payment.confirm_refund_escrow'),
					'confirm_request_payment' => trans('contract.payment.confirm_request_payment'),
					'confirm_release' => trans('contract.payment.confirm_release'),
					'confirm_delete' => trans('contract.confirm_milestone_delete'),
					'error_payment_amount' => trans('message.not_enough_balance'),
					'validation_required' => trans('common.validation.required'),
					'validation_number' => trans('common.validation.number'),
					'validation_start_date' => trans('common.validation.start_date'),
					'validation_max' => trans('common.validation.max', ['max' => formatCurrency(MAX_MILESTONE_AMOUNT)]),
					'value_max_milestone_amount' => MAX_MILESTONE_AMOUNT
				],	

				'ticket' => $ticket,
				'last_dispute' => $contract->getLastSolvedDispute(),
				'solved_tickets' => $solved_tickets
			]);

		} catch(Exception $e) {
		 	Log::error('[ContractController::contract_view] ' . $e->getMessage());
		 	return redirect()->route('contract.all_contracts');
		}
	}

	/**
	* Contract Feedback Create 
	* @author Ro Un Nam
	* @since Jun 05, 2016
	* @version 1.0
	* @param  Request $request
	* @return Response
	*/
	public function feedback(Request $request, $contract_id) {
		$user = Auth::user();

		try {
			if ( $user->isSuspended() ) {
				throw new Exception();
			}

			$contract = Contract::find($contract_id);

			if ( !$contract ) {
				abort(404);
			}
			
			if ( $user->isBuyer() ) {
				if ( !$contract->checkIsAuthor($user->id) ) {
					throw new Exception();
				}
			} else {
				if ( !$contract->checkCurrentFreelancer($user->id) ) {
					throw new Exception();
				}
			}

			if ( $contract->isSuspended() ) {
				throw new Exception();
			}

			// Cancel contract if no payment has done
			if ( $user->isBuyer() && !$contract->canLeaveFeedback() && $contract->totalPaid() >= 0 ) {
				$contract->cancel();
				$user->updateLastActivity();

				add_message(trans('contract.contract_has_been_cancelled', ['title' => $contract->title]), 'success');

				return redirect()->to(_route('contract.contract_view', ['id' => $contract->id]));
			}

			if ( $contract->feedback ) {
				if ( !$contract->checkLeaveFeedbackAlert($user) ) {
					throw new Exception('ContractController@feedback: You can\'t leave feedback. User ID: ' . $user->id);
				}

				if ( $contract->feedback->buyer_feedback && $contract->feedback->freelancer_feedback ) {
					throw new Exception('ContractController@feedback: both buyer and freelancer had leave feedback. So you can\'t leave feedback. User ID: ' . $user->id);
				}
			}

			if ( $request->isMethod('post') ) {
				$contract->closeAndLeaveFeedback($request);
				$user->updateLastActivity();

				return redirect()->to(_route('contract.contract_view', ['id' => $contract->id]));
			}

			return view('pages.contract.feedback', [
				'page'      => 'contract.feedback',
				'contract'  => $contract,
			]);
		} catch(Exception $e) {
			Log::error('ContractController.php [feedback] ' . $e->getMessage());

			return redirect()->to(_route('contract.contract_view', ['id' => $contract_id]));
		}
	}
}