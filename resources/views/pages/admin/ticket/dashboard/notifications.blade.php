<?php
	use iJobDesk\Models\Notification;
	use iJobDesk\Models\UserNotification;
?>
<div class="portlet light portlet-notifications">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-share font-blue-steel hide"></i>
			<span class="caption-subject font-blue-steel bold">Notifications</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
			<ul class="notifications">
				@foreach ($unread_notifications as $notification)
				<li class="notification">
					<div class="label label-priority label-{{ strtolower(array_search($notification->ninfo->priority, Notification::options('priority'))) }}" data-toggle="tooltip" title="{{ array_search($notification->ninfo->priority, Notification::options('priority')) }}" data-placement="right">
						<i class="fa {{ UserNotification::iconByPriority($notification->ninfo->priority) }}"></i>
					</div>
					<div class="desc">
						{!! nl2br($notification->notification) !!}
					</div>
					<div class="date">{{ ago($notification->notified_at) }}</div>
					<div class="notification-action">
						<a href="{{ route('admin.ticket.dashboard.delete_notification', ['id' => $notification->id]) }}"><i class="fa fa-remove"></i></a>
					</div>
				</li>
				@endforeach
			</ul>
		</div>
		<div class="scroller-footer">
			<div class="btn-arrow-link pull-right">
				<a href="{{ route('admin.ticket.notifications') }}">See All Notifications</a>
				<i class="icon-arrow-right"></i>
			</div>
		</div>
	</div>
</div>