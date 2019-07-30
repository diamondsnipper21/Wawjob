<?php
/**
 * @author KCG
 * @since Jan 29, 2018
 */

namespace iJobDesk\Observers;

use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\EmailTemplate;

class AdminMessageObserver {
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
     * @param  Settings
     * @return void
     */
    public function created($message) {
    	if ( $message->id ) {

    		$superAdmins = User::getSuperAdmins();

            $sender = $message->sender;

            if ( $message->isTicketMessage() ) {
            	$ticket = $message->ticket;

                $slug   	= 'ADMIN_TICKET_SEND_TO_SUPER_ADMIN';
                $for  		= User::ROLE_USER_SUPER_ADMIN;
                $receiver 	= $ticket->admin;
                if ($sender->isSuper()) {
                	if (!$receiver || !$receiver->isTicket())
	    				$receiver = $ticket->assigner;
                }

                if (!$receiver)
                	return false;

                $role_identifier = $receiver->role_identifier();
                $ticket_url = route('admin.'.$role_identifier.'.ticket.detail', ['id' => $ticket->id]);

                $params = [
                	'SUPER_ADMIN_NAME' 		=> $receiver->fullname(),
					'TICKET_MANAGER_NAME' 	=> $sender->fullname(),
					'TICKET_URL' 			=> $ticket_url,
					'TICKET_ID' 			=> $ticket->id,
					'TICKET_NAME' 			=> $ticket->subject,
					'MESSAGE' 				=> strip_tags(nl2br($message->message), '<br />')
				];

                if ($sender->isTicket())
	    			EmailTemplate::sendToSuperAdmin($slug, $for, $params);
	    		elseif ($sender->isSuper()) {
	    			EmailTemplate::send($receiver, $slug, $for, $params);
	    		}
            } elseif ( $message->isTodoMessage() ) {
				$sender = $message->sender;
				$assigners = $message->todo->getAssignersAttribute();

				foreach ( $assigners as $assigner ) {
					if ( $assigner->id == $sender->id ) {
						continue;
					}

					EmailTemplate::send($assigner, 'SEND_ADMIN_MESSAGE', 0, [
						'USER' => $assigner->fullname(),
						'SENDER' => $sender->fullname(),
						'MESSAGE' => $message->message,
                        'SHORT_MSG' => substr(strip_tags($message->message), 0, 50),
					]);
				}
            } elseif ( $message->isCommonMesssage() ) {
                $user = Auth::user();
                $thread = $message->thread;

                $receiver_ids = explode_bracket($thread->to);

                $receivers = [];
                foreach ($receiver_ids as $receiver_id) {
                    $receiver = User::find($receiver_id);

                    EmailTemplate::send($receiver, 'SEND_ADMIN_MESSAGE', 0, [
                        'USER'      => $receiver->fullname(),
                        'SENDER'    => $user->fullname(),
                        'MESSAGE'   => $message->message,
                        'SHORT_MSG' => substr(strip_tags($message->message), 0, 50),
                    ], null, null, 'support@ijobdesk.com', $user->fullname());
                }
            }
		}
	}
}