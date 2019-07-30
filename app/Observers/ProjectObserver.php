<?php
/**
 * @author KCG
 * @since Jan 22, 2018
 */

namespace iJobDesk\Observers;

use Auth;
use iJobDesk\Models\Project;
use iJobDesk\Models\ActionHistory;

class ProjectObserver {
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
     * @param  Project  $project
     * @return void
     */
    public function saved($project) {
    	$current_user = Auth::user();

		if ($current_user && $current_user->isAdmin() && $project->isDirty('status') && $project->status != Project::STATUS_DELETED) {
			$action = new ActionHistory();
			$action->doer_id 	= $current_user->id;
			$action->type 		= ActionHistory::TYPE_JOB;
			$action->target_id 	= $project->id;
			$action->reason  	= !empty($_POST['_reason'])?$_POST['_reason']:'';

			if ($project->status == Project::STATUS_SUSPENDED) {
				$action->action_type = 'Suspend';
				$action->description = 'Have been suspended by @#doer_link#';
			}
			elseif ($project->status == Project::STATUS_OPEN) {
				$action->action_type = 'Activate';
				$action->description = 'Have been activated by @#doer_link#';
			}

			if ($action->action_type && $action->description)
				$action->save();
		}
	}

    /**
     * Handle the event.
     *
     * @param  Project  $project
     * @return void
     */
    public function deleted($project) {
    	$current_user = Auth::user();

		if ($current_user && $current_user->isAdmin()) {
			$action = new ActionHistory();
			$action->doer_id 	= $current_user->id;
			$action->type 		= ActionHistory::TYPE_JOB;
			$action->action_type = 'DELETE';
			$action->target_id 	= $project->id;
			$action->reason  	= !empty($_POST['_reason'])?$_POST['_reason']:'';
			$action->description= 'Have been deleted by @#doer_link#';

			$action->save();
		}
	}
}