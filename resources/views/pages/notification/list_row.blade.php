<div class="notification-item {{ !$notification->read_at?'unread':'' }} nid{{ $notification->id }}" data-read-url="{{ route('notification.read', ['id' => $notification->id ]) }}" data-delete-url="{{ route('notification.delete', ['id' => $notification->id ]) }}">
	<div class="row">
		<div class="col-sm-10">
		{!! nl2br($notification->notification) !!}
		</div>
		<div class="col-sm-2">
		{{ $notification->notified_at }}
		<i class="fa fa-close notification-close"></i>
		</div>
	</div>
</div>