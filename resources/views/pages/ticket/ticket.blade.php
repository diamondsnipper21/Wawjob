<?php
/**
* Retrieve Ticket list 
*
* @author  - so gwang
*/

use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;

?>
<div class="list-group-item" data-id="{{$ticket->id}}"> 
	<div class="row item">
		@if (count($ticket->files) == 1)
		<div class="col-sm-5 subject-file-section-1">
		@elseif (count($ticket->files) >= 2)
		<div class="col-sm-5 subject-file-section-2">
		@else
		<div class="col-sm-5 mt-3">
		@endif
			<a href="{{ _route('ticket.detail', ['id' => $ticket->id]) }}" class="subject h-color" data-id="{{$ticket->id}}">
				@if ( mb_strlen($ticket->subject) > 50 )
					{!! mb_substr($ticket->subject, 0, 50) !!} ... 
				@else
					{!! $ticket->subject !!}
				@endif
			</a>
		</div>
		<div class="col-sm-2 type-section text-center">
			{{ Ticket::toString('type', $ticket->type) }}
		</div>
		<div class="col-sm-1 status-section text-center">
			{{ Ticket::toString('status', $ticket->status) }}
		</div>
		<div class="col-sm-1 message-section text-center">
			<?php $unreads = TicketComment::unreadsCount($ticket->id); ?>
			@if ( $unreads != 0 )
				<a href="{{ _route('ticket.detail', ['id' => $ticket->id]) }}" class="unreads h-color badge" data-id="{{$ticket->id}}">
				{{ $unreads }}
				</a>
			@endif
		</div>
		<div class="col-sm-1 message-section text-center">
			{{ $ticket->comments_count()}}
		</div>

		<div class="job-info col-sm-2 job-action">
			<a class="btn btn-link action-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
				<i class="icon-settings"></i> {{ trans('common.action') }} <i class="icon-arrow-down"></i>
			</a>

			<ul class="dropdown-menu">
				<li>
					<a class="btn-link" data-status="view" href="{{ _route('ticket.detail', ['id' => $ticket->id]) }}">
						<i class="icon-link"></i> {{ trans('common.view') }}
					</a>
				</li>
				@if (( $ticket->status == Ticket::STATUS_OPEN || $ticket->status == Ticket::STATUS_ASSIGNED ) && $ticket->type != Ticket::TYPE_DISPUTE  && $ticket->type != Ticket::TYPE_ID_VERIFICATION && $ticket->user_id == $current_user->id )
				<li>
					<a class="btn-link close-link" data-status="close" data-url="{{ route('ticket.list') }}" data-id="{{ $ticket->id }}">
						<i class="icon-close"></i> {{ trans('common.close') }}
					</a>
				</li>
				@endif
			</ul>
		</div>
	</div>
</div>
