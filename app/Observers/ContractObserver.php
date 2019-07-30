<?php
/**
 * @author KCG
 * @since Jan 22, 2018
 */

namespace iJobDesk\Observers;

use Auth;

use iJobDesk\Models\Contract;
use iJobDesk\Models\ActionHistory;
use iJobDesk\Models\UserIgnoredWarning;

class ContractObserver {
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
     * @param  Contract  $contract
     * @return void
     */
    public function saved($contract) {
    	$current_user = Auth::user();

		if ( $current_user && (($current_user->isAdmin() && $contract->status != Contract::STATUS_DELETED) ||
			$contract->status == Contract::STATUS_SUSPENDED) ) {
			$action = new ActionHistory();
			$action->doer_id 	= $current_user->id;
			$action->type 		= ActionHistory::TYPE_CONTRACT;
			$action->target_id 	= $contract->id;
			$action->reason  	= !empty($_POST['_reason'])?$_POST['_reason']:'';

			if ($contract->status == Contract::STATUS_SUSPENDED) {
				$action->action_type = 'Suspend';
				$action->description = 'Have been suspended by @#doer_link#';
			} elseif ($contract->status == Contract::STATUS_OPEN) {
				$action->action_type = 'Activate';
				$action->description = 'Have been activated by @#doer_link#';

			} elseif ($contract->status == Contract::STATUS_CLOSED) {
				$action->action_type = 'Close';
				$action->description = 'Have been closed by @#doer_link#';
			}

			if ($action->action_type && $action->description)
				$action->save();
		} elseif ($current_user) {
    		if ($contract->isDirty('milestone_changed') && $contract->milestone_changed == Contract::MILESTONE_CHANGED_YES) {
    			$contractor = $contract->contractor;
				$contractor->removeIgnoredWarnings(UserIgnoredWarning::TYPE_CHANGED_MILESTONE, $contract->id);
    		}

    		// If contract closed or cancelled, message room for this will be archived automatically.
    		if ($contract->isDirty('status') && ($contract->isClosed() || $contract->isCancelled())) {
    			$application = $contract->application;
    			$message_thread = $application->messageThread;
    			$message_thread->archived();
    			$message_thread->save();
    		}
		}
	}

    /**
     * Handle the event.
     *
     * @param  Contract  $contract
     * @return void
     */
    public function deleted($contract) {
    	$current_user = Auth::user();

		if ($current_user->isAdmin()) {
			$action = new ActionHistory();
			$action->doer_id 	= $current_user->id;
			$action->type 		= ActionHistory::TYPE_CONTRACT;
			$action->action_type = 'DELETE';
			$action->target_id 	= $contract->id;
			$action->reason  	= !empty($_POST['_reason'])?$_POST['_reason']:'';
			$action->description= 'Have been deleted by @#doer_link#';

			$action->save();
		}
	}
}