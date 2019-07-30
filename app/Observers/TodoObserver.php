<?php
/**
 * @author KCG
 * @since Feb 1, 2018
 */

namespace iJobDesk\Observers;

use Auth;
use Log;

use iJobDesk\Models\Todo;
use iJobDesk\Models\User;
use iJobDesk\Models\Notification;
use iJobDesk\Models\EmailTemplate;

class TodoObserver {
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
     * @param  Todo  $todo
     * @return void
     */
    public function saving($todo) {
    	$me = Auth::user();

        $old_assigner_ids = $todo->getOriginal('assigner_ids');
        // Create new or assigned newly
        if ($todo->isDirty('assigner_ids')) {
            $assigners = $todo->assigners;
            
            foreach ($assigners as $assigner) {
                // send notification and email to assigner newly.
                if (!$old_assigner_ids || strpos($old_assigner_ids, "[$assigner->id]") === FALSE) {
                    $role_identifier = $assigner->role_identifier();

                    Notification::send('TODO_ASSIGNED', 
                        SUPERADMIN_ID, 
                        $assigner->id,
                        [
                            'SUBJECT'   => $todo->subject,
                            'CREATOR'   => $todo->creator->fullname()
                        ]
                    );

                    EmailTemplate::send($assigner->id, 
                        'TODO_ASSIGNED',
                        0,
                        [
                            'USER'           => $assigner->fullname(),
                            'TODO_URL'       => route('admin.'.$role_identifier.'.todo.detail', ['id' => $todo->id]),
                            'TODO_TITLE'     => $todo->subject,
                            'CREATOR'        => $todo->creator->fullname()
                        ]
                    );
                }
            }
        }

        // When Re-Opening, send emails to assigners
        $old_status = $todo->getOriginal('status');
        if ($old_status && $todo->isDirty('status')) {
            $assigners = $todo->assigners;

            foreach ($assigners as $assigner) {
                $role_identifier = $assigner->role_identifier();

                if ( $todo->status == Todo::STATUS_OPEN ) {
                    Notification::send('TODO_REOPEN', 
                        SUPERADMIN_ID, 
                        $assigner->id,
                        [
                            'SUBJECT'   => $todo->subject
                        ]
                    );

                    EmailTemplate::send($assigner->id, 
                        'TODO_REOPEN',
                        0,
                        [
                            'USER'           => $assigner->fullname(),
                            'TODO_URL'       => route('admin.'.$role_identifier.'.todo.detail', ['id' => $todo->id]),
                            'TODO_TITLE'     => $todo->subject,
                            'CREATOR'        => $todo->creator->fullname()
                        ]
                    );
                } else if ( $todo->status == Todo::STATUS_COMPLETE ) {
					Notification::send('TODO_CLOSED', 
					    SUPERADMIN_ID, 
					    $assigner->id,
					    [
					        'SUBJECT'   => $todo->subject
					    ]
					);

					EmailTemplate::send($assigner->id, 
					    'TODO_CLOSED',
					    0,
					    [
					        'USER'           => $assigner->fullname(),
					        'TODO_URL'       => route('admin.'.$role_identifier.'.todo.detail', ['id' => $todo->id]),
					        'SUBJECT'        => $todo->subject
					    ]
					);
                }
            }
        }

        return true;
	}
}