<?php
/**
 * @author KCG
 * @since Jan 22, 2018
 */

namespace iJobDesk\Observers;

use Auth;
use DB;
use Exception;
use Log;

use Illuminate\Http\Request;

use iJobDesk\Models\User;
use iJobDesk\Models\ActionHistory;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\Todo;
use iJobDesk\Models\Notification;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\UserIgnoredWarning;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\UserToken;

use iJobDesk\Http\Controllers\JobController;

class UserObserver {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  User  $user
     * @return void
     */
    public function saved($user) {
    	$current_user = Auth::user();

    	if ( !$user->isAdmin() ) { // action history
			if ( $user->isDirty('status') && $user->status != User::STATUS_DELETED ) {

				$action = new ActionHistory();
				$action->doer_id 	= $current_user ? $current_user->id : $user->id;
				$action->type 		= ActionHistory::TYPE_USER;
				$action->target_id 	= $user->id;
				$action->reason  	= !empty($_POST['_reason'])?$_POST['_reason']:'';

				if ($user->status == User::STATUS_SUSPENDED) {
					$action->action_type = 'Suspend';
					$action->description = 'Have been suspended by @#doer_link#';
				} elseif ($user->status == User::STATUS_FINANCIAL_SUSPENDED) {
					$action->action_type = 'Suspend Financial';
					$action->description = 'Have been financial suspended by @#doer_link#';
				} elseif ($user->status == User::STATUS_AVAILABLE) {
					$action->action_type = 'Activate';
					$action->description = 'Have been activated by @#doer_link#';
				}

				if ($action->action_type && $action->description)
					$action->save();
			}
    	}

    	// Email users when suspend, financial suspension and activate.
    	if ($user->isDirty('status')) {
    		$old_status = $user->getOriginal('is_auto_suspended');

			// if user has been activated, email to him.
			if ($user->status == User::STATUS_AVAILABLE && $old_status = User::STATUS_NOT_AVAILABLE) {
	            EmailTemplate::send($user, 'ACCOUNT_ACTIVATED', 0, [
	            	'USER' 	=> $user->fullname()
	            ]);
			}

			// if user has been suspended, email to him.
			if ($user->status == User::STATUS_SUSPENDED) {
	            EmailTemplate::send($user, 'ACCOUNT_SUSPENDED', 0, [
	            	'USER' 	=> $user->fullname(),
	                'REASON' => $_POST['_reason']
	            ]);
			}

			// if user has been suspended financially, email to him.
			if ($user->status == User::STATUS_FINANCIAL_SUSPENDED) {
	            EmailTemplate::send($user, 'ACCOUNT_FINANCIAL_SUSPENDED', 0, [
	            	'USER' 	=> $user->fullname(),
	                'REASON' => $_POST['_reason']
	            ]);
			}
    	}

		if ( $user->isDirty('login_blocked') ) {
			$action = new ActionHistory();
			$action->doer_id 	= $current_user ? $current_user->id : $user->id;
			$action->type 		= ActionHistory::TYPE_USER;
			$action->target_id 	= $user->id;
			$action->reason  	= !empty($_POST['_reason'])?$_POST['_reason']:'';

			if ( $user->isLoginBlocked() ) {
				$action->action_type = 'Login Blocked';
				$action->description = 'Have been login blocked by @#doer_link#';
			} else {
				$action->action_type = 'Login Enabled';
				$action->description = 'Have been login enabled by @#doer_link#';
			}

			$action->save();

			if ( $user->isLoginBlocked() ) {
				$token = $user->generateToken(UserToken::TYPE_LOGIN_BLOCKED);

				$login_url = route('user.login', ['token' => $token]);

				$change_pwd_url = route('user.login', ['token' => $token, 'to' => route('user.change_password')]);

				if ( $user->try_login >= User::TOTAL_TRY_LOGINS ) {
					// Send notification to super admins
					Notification::sendToSuperAdmin('LOGIN_BLOCKED_WRONG_PWD', SUPERADMIN_ID, [ 
		                    'USER_NAME'   => $user->fullname()
		                ]
		            );
                    // Send email to user
					EmailTemplate::send($user, 'ACCOUNT_BLOCKED_BY_PASSWORD', 0, [
						'USER' => $user->firstname(),
						'RESTORE_LOGIN_URL' => $login_url,
                        'RESTORE_CHANGE_PWD_URL' => $change_pwd_url
					]);					
				} else if ( $user->try_question >= User::TOTAL_TRY_SECURITY_ANSWER ) {
					// Send notification to super admins
					Notification::sendToSuperAdmin('LOGIN_BLOCKED_WRONG_SQ', SUPERADMIN_ID, [ 
		                    'USER_NAME'   => $user->fullname()
		                ]
		            );

					// Send email to user
					EmailTemplate::send($user, 'ACCOUNT_BLOCKED_BY_SECURITY_QUESTION', 0, [
						'USER' => $user->firstname()
					]);
				}
			}
		}

		// Password Changed
		if ($user->getOriginal('password') && $user->isDirty('password')) {
            EmailTemplate::send($user, 'PASSWORD_CHANGED', 0, [
                'USER' => $user->fullname(),
                'CONTACT_US_URL' => route('frontend.contact_us')
            ]);
		}
	}

    /**
     * Handle the event.
     *
     * @param  User  $user
     * @return void
     */
    public function saving($user) {
    	if ($user->isAdmin())
    		return true;

		$old_is_auto_suspended = $user->getOriginal('is_auto_suspended');
		$is_auto_suspended     = $user->is_auto_suspended;

		if ($user->isDirty('status')) {
			$old_status = $user->getOriginal('status');

    		if ($user->status == User::STATUS_SUSPENDED) {
				$user->removeIgnoredWarnings(UserIgnoredWarning::TYPE_SUSPENDED);
    		}

    		if ($user->status == User::STATUS_FINANCIAL_SUSPENDED) {
				$user->removeIgnoredWarnings(UserIgnoredWarning::TYPE_FINANCIAL_SUSPENDED);
    		}

    		if ($old_status == User::STATUS_SUSPENDED || $old_status == User::STATUS_FINANCIAL_SUSPENDED) {
    			$is_auto_suspended = null;
    			if ($user->totalDisputedContracts(true) != 0) {
    				// if this user is freelancer and status will be not suspension, it will be financial suspension, because of this user has disputed contract...
    				if ($user->status != User::STATUS_SUSPENDED && $user->isFreelancer())
    					$user->status = User::STATUS_FINANCIAL_SUSPENDED;
    				// if try to suspend user(NOT FINANCIAL SUSPENSION), current status will be suspension, NOT old status
    				elseif ($user->status != User::STATUS_SUSPENDED)
    					$user->status = $old_status;

    				$is_auto_suspended = 1;
    				if ($old_is_auto_suspended === 0 && $user->is_auto_suspended !== null) {
    					if ($old_status == User::STATUS_SUSPENDED)
    						$is_auto_suspended = 0;
    				}
    			} else {
    				if ($old_is_auto_suspended === 0 && $user->is_auto_suspended !== null)
						$is_auto_suspended = 0;

					if ($is_auto_suspended === 0 && $old_status == User::STATUS_SUSPENDED)
						$user->status 	   = $old_status;
    			}
			} elseif ($user->status == User::STATUS_SUSPENDED || $user->status == User::STATUS_FINANCIAL_SUSPENDED) {
				// if this contractor has been suspended by admin, this field will keep to original(manunal suspension by admin)
				if ($old_is_auto_suspended === 0 && $user->is_auto_suspended == 1) {
					$is_auto_suspended = 0;
				}
			}
		} else {
			if ($user->status == User::STATUS_SUSPENDED || $user->status == User::STATUS_FINANCIAL_SUSPENDED) {
				if ($old_is_auto_suspended === 0 && $is_auto_suspended == 1)
					$is_auto_suspended = 0;
			}
		}

		$user->is_auto_suspended = $is_auto_suspended;

		// If this user is suspended, all contracts will be suspended. [ONLY notifications and emails]
		if ($user->isDirty('status') && $user->status == User::STATUS_SUSPENDED) {
			$params = [
				'status' => [
					Contract::STATUS_OPEN,
					Contract::STATUS_PAUSED
				]
			];

			if ( $user->isBuyer() ) {
				$params['buyer_id'] = $user->id;
			} else {
				$params['contractor_id'] = $user->id;
			}

			$contracts = Contract::getContracts($params);
			foreach ($contracts as $contract) {
				$reasons = [
		            'me'         => trans('contract.contract_suspension_reason_by_your_suspension', ['user' => $user->fullname()]),
		            'buyer'      => trans('contract.contract_suspension_reason_by_client_suspension', ['user' => $user->fullname()]),
		            'freelancer' => trans('contract.contract_suspension_reason_by_freelancer_suspension', ['user' => $user->fullname()]),
		        ];
				$contract->suspend($reasons, $user, false);
			}
		}
    }

    /**
     * Handle the event.
     *
     * @param  User  $user
     * @return void
     */
    public function deleted($user) {
    }

    /**
     * Handle the event.
     *
     * @param  User  $user
     * @return void
     */
    public function deleting($user) {
    	$success = false;

    	if ($user->isAdmin())
    		$success = $this->deleting_admin($user);
    	else
    		$success = $this->deleting_user($user);

    	if ($success) {
		    $user->status = User::STATUS_DELETED;
		    $user->save();
    	}

    	return $success;
    }

    /**
     * Deleting buyer or freelancer
     */
    private function deleting_admin($user) {
    	$current_user = Auth::user();
    	$me			  = $current_user;

    	if (!$me->isSuper()) // Super administrator can delete admin users.
    		return false;

	    // Unassigning for this user on all ticket, todo and remove messages.
	    Ticket::where('admin_id', $user->id)
	    	  ->update(['admin_id' => null]);
	    Ticket::where('assigner_id', $user->id)
	    	  ->update(['assigner_id' => $me->id]);
	    
	    DB::update("UPDATE tickets SET reader_ids = REPLACE(reader_ids, '[$user->id]', '') WHERE reader_ids LIKE '%[$user->id]%'");

	    // TODO table
	    Todo::where('creator_id', $user->id)
	    	->update(['creator_id' => $me->id]);
	    DB::update("UPDATE todos SET assigner_ids = REPLACE(assigner_ids, '[$user->id]', '') WHERE assigner_ids LIKE '%[$user->id]%'");

    	// Admin Messages
	    // AdminMessage::where("sender_id = $user->id")
	    // 			->delete();

    	// // Admin Messages
	    // TicketComment::where("sender_id = $user->id")
	    // 			 ->delete();

    	return true;
    }

    /**
     * Deleting buyer or freelancer
     */
    private function deleting_user($user) {
    	$current_user = Auth::user();
    	$me			  = $current_user;

    	try {
	        $opended_job_postings 	= [];
	        $opened_applications 	= [];
	        $opened_contracts 		= [];

	        // Opened Job Postings
	        if ($user->isBuyer())
	            $opended_job_postings = Project::openedJobs($user)->get();

	        // Opened Applications
	        if ($user->isFreelancer())
	            $opened_applications = ProjectApplication::openedApplications($user);

	        // Opened Contracts
	        $params = [];
	        $params['buyer_id']         = $user->isBuyer()?$user->id:null;
	        $params['contractor_id']    = $user->isFreelancer()?$user->id:null;
	        $opened_contracts 			= Contract::getOpenedContracts($params);

	    	if (!$current_user->isAdmin()) {
		        if ($user->myBalance() != 0)
		            throw new Exception('You have still budget in your wallet. So you can\'t close your account.');

	    		if ($user->isSuspended())
	    			throw new Exception('You can\'t close your account because of suspension.');

		        if ($user->isFinancialSuspended())
		            throw new Exception('You can\'t close your account because of financial suspension.');


				if (count($opended_job_postings) != 0)
				    throw new Exception('You can\'t close your account before closing your all opened jobs');

				if (count($opened_applications) != 0)
				    throw new Exception('You can\'t close your account before closing your all active proposals');

		        if (count($opened_contracts) != 0)
				    throw new Exception('You can\'t close your account before closing your all opened contracts');
		    }

			$orig_reason = $_POST['_reason']??'';

	        if ($current_user->isAdmin()) { // Case: Delete user by super admin.
		        // if ($user->myBalance() != 0)
		        //     throw new Exception(sprintf('User(%s) have still budget in your wallet. So user(%s) can\'t close your account.', $user->fullname(), $user->fullname()));

				$action = new ActionHistory();
				$action->doer_id 	 = $current_user->id;
				$action->type 		 = ActionHistory::TYPE_USER;
				$action->action_type = 'DELETE';
				$action->target_id 	 = $user->id;
				$action->reason  	 = !empty($_POST['_reason'])?$_POST['_reason']:'';
				$action->description = 'Have been deleted by @#doer_link#';

				$action->save();

				// Close all opened tickets related to this user.
				$opened_tickets = Ticket::whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED])
									    ->where('user_id', $user->id)
									   	->get();

				foreach ($opened_tickets as $ticket) {
					$contract = $ticket->contract;

					if ($contract) { // Case: Dispute
						$archive_type = Ticket::RESULT_SOLVED_THEMSELVES;

						if ($user->isBuyer())
							$reason = sprintf('The %s(%s) have been banned permanently.', 'Buyer', $user->fullname());
						else
							$reason = sprintf('The %s(%s) have been banned permanently.', 'Freelancer', $user->fullname());

					} else {
						$archive_type = Ticket::RESULT_SOLVED_SUCCESS;
						$reason 	  = 'You have been banned permanently.';
					}

					$ticket->archive_type       = $archive_type;
		            $ticket->reason             = $reason;
		            $ticket->dispute_winner_id  = null;
		            $ticket->status             = Ticket::STATUS_SOLVED;
		            $ticket->admin_id           = (!empty($ticket->admin_id)?$ticket->admin_id:$me->id);
		            $ticket->assigner_id        = (!empty($ticket->assigner_id)?$ticket->assigner_id:$me->id);
		            $ticket->ended_at           = date('Y-m-d H:i:s');

		            $ticket->save();

		            // Send Notifications when this ticket is dispute
		            if ($ticket->isDispute()) {
		            	foreach ([$ticket->admin, $ticket->assigner] as $attender) {
		            		if ($attender->id == $user->id || $attender->id == $me->id) // Don't send email to me and deleted user.
		            			continue;

		            		Notification::send(
			                    'TICKET_CLOSED_WHEN_DELETING_ACCOUNT', 
			                    SUPERADMIN_ID,
			                    $attender, 
			                    [
			                    	'BANNED_USER' => $user->fullname(), 
			                    	'TICKET_ID'   => $ticket->id, 
			                    	'TICKET_NAME' => $ticket->subject
			                    ]
			                );
		            	}
		            }
				}

				// Close all opened contracts
				foreach ($opened_contracts as $contract) {
					if ($user->isBuyer())
						$reason = sprintf('The %s(%s) have been banned permanently.', 'Buyer', $user->fullname());
					else
						$reason = sprintf('The %s(%s) have been banned permanently.', 'Freelancer', $user->fullname());

		        	$_POST['_reason'] = $_REQUEST['_reason'] = $reason;
		        	$_POST['reason']  = $_REQUEST['reason']  = $reason;

					if ($contract->closeSelfAndApplications()) {
						Notification::send(
		                    'CONTRACT_CLOSED_WHEN_DELETING_ACCOUNT', 
		                    SUPERADMIN_ID,
		                    $user->isBuyer()?$contract->contractor_id:$contract->buyer_id, 
		                    ['BANNED_USER' => $user->fullname(), 'TITLE' => $contract->title]
		                );

		                $email_receiver = null;
			            // Send email to buyer
			            if ($contract->buyer_id != $me->id && $contract->buyer) {
			            	$email_receiver = $contract->buyer;
			            }

			            // Send email to freelancer
			            if ($contract->contractor_id != $me->id && $contract->contractor) {
			            	$email_receiver = $contract->contractor;
			            }

			            // Send email
			            if ($email_receiver) {
			                EmailTemplate::send($email_receiver, 'CONTRACT_CLOSED_WHEN_DELETING_ACCOUNT', 0, [
			                    'BANNED_USER' => $user->fullname(),
			                    'TITLE' => $contract->title,
			                    'PROJECT' => _route('contract.contract_view', ['id' => $contract->id], true, null, $email_receiver),
			                ]);
			            }
					}

		        	unset($_POST['_reason']);
		        	unset($_POST['reason']);
		        	unset($_REQUEST['_reason']);
		        	unset($_REQUEST['reason']);
				}

	        	// Close All Job Postings
	        	foreach ($opended_job_postings as $job_posting) {
					$controller = new JobController();
					$controller->change_status(new Request(), $job_posting->id, Project::STATUS_CLOSED, $user);
				}

				// Close All Applications
				foreach ($opened_applications as $application) {
					$application->status = ProjectApplication::STATUS_PROJECT_CANCELLED;
					$application->save();
				}
			}

			$_POST['_reason'] = $orig_reason;

			EmailTemplate::send($user, 'ACCOUNT_CLOSED', 0, [
	            'USER' 	=> $user->fullname()
	        ]);

	        if (!$user->isAdmin()) { // if user is freelancer or buyer, send email to super admins.
	        	EmailTemplate::sendToSuperAdmin('ACCOUNT_CLOSED', User::ROLE_USER_SUPER_ADMIN, [
	                'EMAIL' 	=> $user->email,
	                'REASON' 	=> $user->closed_reason_string(),
	                'COMMENT' 	=> $_POST['_reason'],
	            ]);
	        }

    	} catch (Exception $e) {
    		add_message($e->getMessage(), 'danger');
    		return false;
    	}

    	return true;
	}
}