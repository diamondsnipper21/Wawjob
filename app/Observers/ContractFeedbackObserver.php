<?php
/**
 * @author KCG
 * @since Jan 29, 2018
 */

namespace iJobDesk\Observers;

use Auth;

use iJobDesk\Models\Contract;
use iJobDesk\Models\UserIgnoredWarning;

class ContractFeedbackObserver {
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
     * @param  ContractFeedback  $feedback
     * @return void
     */
    public function saved($feedback) {
    	$user = Auth::user();

    	if (!$user->isSuper()) {
            $old_feedback = $feedback->getOriginal();
            if (empty($old_feedback['id'])) { // created newly.
                $user->removeIgnoredWarnings(UserIgnoredWarning::TYPE_LEAVE_FEEDBACK, $feedback->contract_id);
            }
    	}
	}
}