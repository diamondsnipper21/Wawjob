<?php
	use iJobDesk\Models\Ticket;
?>

@section('additional-css')
<link href="{{ url('assets/plugins/metronic/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css"/>
@endsection

<div class="portlet light tasks-widget">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-share font-blue-steel hide"></i>
			<span class="caption-subject font-blue-steel bold">My Tickets</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
			<ul class="task-list my-tickets">
				@foreach ($tickets as $ticket)
				<li>
					<div class="task-title">
						<span class="label-color-icon label-{{ strtolower(array_search($ticket->type, Ticket::getOptions('type'))) }}" data-toggle="tooltip" data-placement="right" title="{{ array_search($ticket->type, Ticket::getOptions('type')) }}">
                    		<i class="fa {{ Ticket::iconByType($ticket->type) }}"></i>
                    	</span>
                    	&nbsp;
						<span class="task-title-sp"><a href="{{ route('admin.ticket.ticket.detail', ['id' => $ticket->id]) }}">{{ $ticket->subject }}</a></span>
					</div>
				</li>
				@endforeach
				
				@if ($tickets->isEmpty())
				<li>
					<div class="task-title"><span class="task-title-sp">No Ticket</span></div>
				</li>
				@endif

			</ul>
		</div>
		<div class="scroller-footer">
			<div class="btn-arrow-link pull-right">
				<a href="{{ route('admin.ticket.ticket.list') }}">See All Tickets</a>
				<i class="icon-arrow-right"></i>
			</div>
		</div>
	</div>
</div>