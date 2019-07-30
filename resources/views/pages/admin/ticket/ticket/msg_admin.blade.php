<?php
/**
* Ticket Detail Page on Ticket Manager
*
* @author KCG
* @since July 4, 2017
* @version 1.0
*/

use iJobDesk\Models\Ticket;
use iJobDesk\Models\User;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\File;

?>
@extends('layouts/admin/' . $role_id . (!empty($user)?'/user':''))

@section('content')

<script type="text/javascript">
</script>

<div id="admin_message_room" class="portlet light ticket-send-to-admn">
    <div class="portlet-title">
        <div class="row">
            <div class="caption col-md-10">
                <span class="caption-subject font-green-sharp bold">
                    <h3>Ticket - #{{ $ticket->id }} {{ $ticket->subject }}</h3>
                </span>
            </div>
            <div class="caption col-md-2">
                 <a href="{{ route('admin.'.$role_id.(!empty($user)?'.user':'').'.ticket.detail', ['id' => $ticket->id]) }}"
                  class="back-list">&lt; Back to ticket</a>
            </div>
        </div>
        <div class="desc">
            @if ($current_user->isTicket())
                Messages to Super Admin (Invisible to the public) 
            @elseif(Auth::user()->isSuper())
                Messages to Ticket Manager (Invisible to the public) 
            @endif
        </div>
    </div><!-- .portlet-title -->
    <div id="ticket_comments" class="portlet-body">
        
        {{ show_messages() }}

        @include('pages.partials.messages', [
                        'id' => $ticket->id, 
                        'messages' => $messages, 
                        'type' => File::TYPE_ADMIN_MESSAGE, 
                        'class' => 'Ticket', 
                         // 'can_send' => !$ticket->isClosed(), 
                        'can_send' => true, 
                        'totals' => $message_count,
                        'limit' => $message_limit
        ])
    </div><!-- .portlet-body -->
</div><!-- .portlet -->
@endsection