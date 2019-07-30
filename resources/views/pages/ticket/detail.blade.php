<?php
/**
 * Retrieve Message list 
 *
 * @author  - so gwang
 */

use iJobDesk\Models\User;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\File;
?>

@extends('layouts/default/index')

@section('content')
<div id="ticket_detail">
	<div class="title-section mb-4">
		<div class="row">
			<div class="col-md-9">
				<span class="title">
					{{ $ticket->user->fullname() }} 
					- 
					{{ $ticket->subject }} 
				</span>
				<br />
				<span class="date">
					@if (Ticket::toString('type', $ticket->type))
					{{ Ticket::toString('type', $ticket->type) }} 
					@endif
					- 
					{{ Ticket::toString('status', $ticket->status) }}
					- {{ format_date(null, $ticket->created_at) }}
				</span>
			</div>
			<div class="col-md-3">
				<div class="back-link-label text-right margin-bottom-20">
					<a href="{{ route('ticket.list', ['tab' => 'opening']) }}"><i class="fa fa-angle-left"></i> {{ trans('common.back_to_list') }}</a>
				</div>
			</div>
		</div>
		
		<div class="content my-4">
			@if ($ticket->type == Ticket::TYPE_ID_VERIFICATION)
				<div class="id-verification pl-4 pr-4">
					{!! $content !!}
				</div>
			@else
				{!! render_more_less_desc($content) !!}
			@endif
		</div>

        <div class="attachments">
        	{!! render_files($ticket->files) !!}
        </div>
        <div class="clearfix"></div>
	</div>

    @include('pages.partials.messages', [
                    'id' => $ticket->id, 
                    'messages' => $messages, 
                    'type' => $ticket->file_type(), 
                    'class' => 'Ticket', 
                    'can_send' => !$ticket->isClosed(), 
                    'totals' => $message_count,
                    'limit' => $message_limit
    ])
</div>
@endsection