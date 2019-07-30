<?php
/**
 * @author KCG
 * @since Jan 22, 2018
 */

namespace iJobDesk\Observers;

use Auth;
use Log;

use iJobDesk\Models\Ticket;
use iJobDesk\Models\User;
use iJobDesk\Models\Notification;
use iJobDesk\Models\EmailTemplate;

class TicketObserver {
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
     * @param  Ticket  $ticket
     * @return void
     */
    public function saving($ticket) {
    	$me = Auth::user();

        // When assigning to another users.
        if ($ticket->isDirty('admin_id') && $ticket->admin_id != $me->id) {
            $admin_id = $ticket->admin_id;

            Notification::send(Notification::ADMIN_TICKET_ASSIGNED, 
                SUPERADMIN_ID, 
                $ticket->admin_id,
                [ 
                    'TICKET_ID'   => $ticket->id, 
                    'TICKET_NAME' => $ticket->subject,
                    'admin_name'  => $ticket->assigner->fullname()
                ]
            );

            EmailTemplate::send($admin_id, 
                'ADMIN_TICKET_ASSIGNED', 
                0,
                [
                    'ticket_id'     => $ticket->id,
                    'ticket_name'   => $ticket->subject,
                    'assigner_id'   => $ticket->assigner_id,
                    'assigner_name' => $ticket->assigner->fullname(),
                    'assignee_id'   => $ticket->admin_id,
                    'assignee_name' => $ticket->admin->fullname()
                ]
            );
        }

        // When ticket is closed...
        if ( $ticket->isDirty('status') && $ticket->isClosed() ) {

            $receivers = [];

            // Dispute Ticket
            if ( $ticket->contract ) {
                $receivers[] = $ticket->contract->buyer;
                $receivers[] = $ticket->contract->contractor;
            } else {
                if ( $ticket->user_id != $ticket->admin_id ) {
                    $receivers[] = $ticket->user;
                } else if ( $ticket->receiver_id ) {
                    $receivers[] = $ticket->receiver;
                }
            }

            // If assigned to ticket manager
            if ( $ticket->admin_id ) {
                $receivers[] = $ticket->admin;

	            $admin_user = $ticket->admin;
            }

           	if ( $receivers ) {
           		foreach ( $receivers as $receiver ) {
	                $ticket_url = '';
	                if ( $receiver->isAdmin() ) {
	                	$role_identifier = $receiver->role_identifier();
	            		$ticket_url = route('admin.'.$role_identifier.'.ticket.detail', ['id' => $ticket->id]);
	                } else {
	                	if ( $ticket->contract_id ) {
	                		$ticket_url = _route('contract.contract_view', ['id' => $ticket->contract_id], true, null, $receiver);
	                	} else {
                            $ticket_url = _route('ticket.detail', ['id' => $ticket->id], true, null, $receiver);
                        }
	                }

                    Notification::send('TICKET_CLOSED', SUPERADMIN_ID, $receiver->id, [
                        'TICKET_ID'   => $ticket->id,
                        'TICKET_NAME' => $ticket->subject
                    ]);

                    if (!$receiver->isAdmin()) {
                        EmailTemplate::send($receiver, 'TICKET_CLOSED', 0, [
                            'USER'          => $receiver->fullname(),
                            'TICKET_NAME'   => $ticket->subject,
                            'TICKET_URL'    => $ticket_url
                        ]);
                    } else {
                        // Mail to Ticket Manager
                        EmailTemplate::send($receiver, 
                            'ADMIN_TICKET_SOLVED', 
                            0,
                            [
                                'TICKET_ID'     => $ticket->id,
                                'TICKET_NAME'   => $ticket->subject,
                                'ADMIN'         => $receiver->fullname(),
                                'USER'          => $receiver->fullname(),
                                'TICKET_URL'    => $ticket_url,
                                'REASON_TYPE'   => Ticket::toString('result', $ticket->archive_type),
                                'REASON_COMMENT'=> strip_tags(nl2br($ticket->reason), '<br>'),
                            ]
                        );
                    }
	            }
           	}

            $ticket->ended_at = date('Y-m-d H:i:s');

            if ($ticket->type == Ticket::TYPE_ID_VERIFICATION) {
                $options = Ticket::getOptions('id_verification_result');

                $_POST['_reason'] = isset($options[$ticket->archive_type]) ? isset($options[$ticket->archive_type]) : '';
                
                if ($ticket->archive_type == Ticket::RESULT_IDV_SUCCESS || $ticket->archive_type == Ticket::RESULT_IDV_NORMAL) {

                    if ($ticket->archive_type == Ticket::RESULT_IDV_SUCCESS)
                        $ticket->user->id_verified = 1;

                    $ticket->user->status = User::STATUS_AVAILABLE;
                    $ticket->user->save();

                    // Update user points
                    $ticket->user->point->updateIDVerified();

                    if ($ticket->archive_type == Ticket::RESULT_IDV_SUCCESS)
                        // Mail to User
                        EmailTemplate::send($ticket->user, 
                            'ID_VERIFICATION_SUCCESS', 
                            0,
                            [
                                'USER'     => $ticket->user->fullname()
                            ]
                        );
                } else {
                    $last_action = $ticket->user->lastSuspensionAction();
                    if ( $last_action ) {
                        $last_action->reason = $_POST['_reason'];
                        $last_action->save();
                    }
                    
                    // Mail to User
                    EmailTemplate::send($ticket->user, 
                        'ID_VERIFICATION_FAIL', 
                        0,
                        [
                            'USER'     => $ticket->user->fullname()
                        ]
                    );
                }
            } else if ( $ticket->type == Ticket::TYPE_DISPUTE ) {
                // Update user points
                $ticket->contract->contractor->point->updateDispute();
            }
        }

		if ($ticket->isClosed()) {
			$ticket->closer_id = $me->id;
		}

        if ($ticket->isDirty('admin_id'))
            $ticket->assigned_at    = date('Y-m-d H:i:s');
	}

    /**
     * Handle the event.
     *
     * @param  Ticket  $ticket
     * @return void
     */
    public function saved($ticket) {
        $me = Auth::user();
        
        // When creating new ticket, notify all ticket managers including super managers.
        if (!$ticket->getOriginal('id')) {
            $admin_users = User::getAdminUsers([
                User::ROLE_USER_SUPER_ADMIN,
                User::ROLE_USER_TICKET_MANAGER
            ]);

            // Send notification and email to admin users.
            foreach ($admin_users as $admin_user) {
                $role_identifier = $admin_user->role_identifier();

                Notification::send('TICKET_CREATED', 
                    SUPERADMIN_ID, 
                    $admin_user->id,
                    [
                        'TICKET_ID'   => $ticket->id,
                        'TICKET_NAME' => $ticket->subject
                    ]
                );

                EmailTemplate::send($admin_user, 
                    'ADMIN_TICKET_CREATED', 
                    0,
                    [
                        'USER'       => $admin_user->fullname(),
                        'TICKET_NAME'=> $ticket->subject,
                        'CREATOR'    => $ticket->user->fullname(),
                        'ADMIN'      => $admin_user->fullname(),
                        'MESSAGE'    => strip_tags(nl2br($ticket->content), '<br>'),
                        'TICKET_URL' => route('admin.'.$role_identifier.'.ticket.detail', ['id' => $ticket->id]),
                    ]
                );
            }

            // Sending an notification and email to ticket user
            if ( $ticket->user_id != $ticket->admin_id ) {
                Notification::send('TICKET_CREATED', 
                    SUPERADMIN_ID, 
                    $ticket->user->id,
                    [
                        'TICKET_ID'   => $ticket->id,
                        'TICKET_NAME' => $ticket->subject
                    ]
                );
                EmailTemplate::send($ticket->user, 
                    'TICKET_CREATED', 
                    0,
                    [
                        'TICKET_NAME'    => $ticket->subject,
                        'USER'           => $ticket->user->fullname(),
                        'TICKET_URL'     => _route('ticket.detail', ['id' => $ticket->id], true, null, $ticket->user),
                    ]
                );
            } else if ( $ticket->receiver_id ) {
            	Notification::send('TICKET_CREATED', 
                    SUPERADMIN_ID, 
                    $ticket->receiver->id,
                    [
                        'TICKET_ID'   => $ticket->id,
                        'TICKET_NAME' => $ticket->subject
                    ]
                );
                EmailTemplate::send($ticket->receiver, 
                    'TICKET_CREATED', 
                    0,
                    [
                        'TICKET_NAME'    => $ticket->subject,
                        'USER'           => $ticket->receiver->fullname(),
                        'TICKET_URL'     => _route('ticket.detail', ['id' => $ticket->id], true, null, $ticket->receiver),
                    ]
                );
            }
        }
    }
}