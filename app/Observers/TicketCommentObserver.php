<?php
/**
 * @author KCG
 * @since Jan 29, 2018
 */

namespace iJobDesk\Observers;

use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\EmailTemplate;

class TicketCommentObserver {
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
    public function saved($comment) {
    	if (!$comment->getOriginal('id')) {
            $current_user = Auth::user();

    		$sender = $comment->sender;
            $ticket = $comment->ticket;

            $receivers = [];

            if ($ticket->isDispute()) {
                // Sent from admin
                if ( $sender->isAdmin() ) {
                    $receivers[] = $ticket->contract->buyer;
                    $receivers[] = $ticket->contract->contractor;

                    // If assigned to ticket manager
                    if ( $ticket->admin_id && $ticket->admin_id != $current_user->id ) {
                        $receivers[] = $ticket->admin;
                    }                   
                // Sent from user
                } else {
                    if ( $current_user->isFreelancer() ) {
                        $receivers[] = $ticket->contract->buyer;
                    } else if ( $current_user->isBuyer() ) {
                        $receivers[] = $ticket->contract->contractor;
                    }

                    // If assigned to ticket manager
                    if ( $ticket->admin_id ) {
                        $receivers[] = $ticket->admin;
                    }
                }

                if ( $receivers ) {
                    // Send message for dispute ticket.
                    foreach ( $receivers as $receiver ) {
                        EmailTemplate::send($receiver, 'SEND_MESSAGE_DISPUTE', 0, [
                            'USER'           => $receiver->fullname(),
                            'SENDER_NAME'    => $sender->fullname(),
                            'CONTRACT_TITLE' => $ticket->contract->title,
                            'CONTRACT_URL'   => _route('contract.contract_view', ['id' => $ticket->contract->id], true, null, $receiver),
                            'MESSAGE'        => strip_tags(nl2br($comment->message), '<br>'),
                            'SHORT_MSG'      => substr(strip_tags($comment->message), 0, 50),
                            'MESSAGE_URL'    => _route('contract.contract_view', ['id' => $ticket->contract->id], true, null, $receiver)
                        ]);
                    }
                }
            } else {
                $email_slug = '';
                if ( $sender->isAdmin() ) {
                    if ( $ticket->user_id != $ticket->admin_id ) {
                        $receivers[] = $ticket->user;
                    } else {
                        $receivers[] = $ticket->receiver;
                    }

                    // If assigned to ticket manager
                    if ( $ticket->admin_id && $ticket->admin_id != $current_user->id ) {
                        $receivers[] = $ticket->admin;
                    }
                } else {
                    if ( $ticket->admin_id ) {
                        $receivers[] = $ticket->admin;
                    }
                    $email_slug = 'TICKET_MESSAGE';
                    $for        = 0;
                }

                foreach ( $receivers as $receiver ) {
                    $email_slug = 'TICKET_MESSAGE';
                    $for        = 0;

                    $ticket_url = _route('ticket.detail', ['id' => $ticket->id], true, null, $receiver);

                    if ($receiver->isAdmin()) {
                        $email_slug = 'SUPER_ADMIN_TICKET_MESSAGE';
                        $for        = User::ROLE_USER_SUPER_ADMIN;

                        $role_identifier = $receiver->role_identifier();
                        $ticket_url = route('admin.'.$role_identifier.'.ticket.detail', ['id' => $ticket->id]);
                    }

                    EmailTemplate::send($receiver, $email_slug, $for, [
                        'TICKET_TITLE'   => $ticket->subject,
                        'ADMIN'          => $receiver->fullname(),
                        'USER'           => $receiver->fullname(),
                        'SENDER_NAME'    => $sender->fullname(),
                        'MESSAGE'        => strip_tags(nl2br($comment->message), '<br>'),
                        'TICKET_URL'     => $ticket_url
                    ]);
                }
            }
        }
	}
}