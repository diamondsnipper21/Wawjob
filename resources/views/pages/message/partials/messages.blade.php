<?php
/**
 * @author KCG
 * @since Feb 22, 2018
 */

use iJobDesk\Models\File;

?>

@include('pages.partials.messages', [
    'id' 				=> $thread->id, 
    'messages' 			=> $messages, 
    'type' 				=> File::TYPE_MESSAGE, 
    'class' 			=> 'Message', 
    'can_send' 			=> $thread->canSendMessage(), 
    'totals' 			=> $message_count,
    'limit'             => $message_limit,
    'container'         => $container??null,
    'form_elements'		=> [
    	'thread_id'		=> $thread->id,
        'per_page'      => $per_page,
        'keywords'      => $keywords,
        'tab'           => $tab,
        'action'        => 'SEND_MESSAGE'
    ]
])