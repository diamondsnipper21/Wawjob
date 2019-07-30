
<?php $message->markedAsRead(); ?>

<div class="message-row-container">
	<div id="message-{{$message->id}}" class="message-row {{ $message->sender_id == $current_user->id?'me':'' }} {{ $message->isUnread()?'unread':'' }}" data-url="{{ route('message.unread', ['id' => $message->id, 'type' => $type]) }}">
		<div class="sender-date">
			{!! format_date('M j, Y', $message->created_at) !!}&nbsp;&nbsp;{{ format_date('g:i A', $message->created_at) }}<span class="ago">{{ ago($message->created_at) }}</span>
		</div>
		<div class="sender-image">
			<a href="{{ _route('user.profile', [$message->sender_id]) }}" target="_blank" title="{{ $message->sender->fullname() }}">
				<img class="img-circle" src="{{ avatar_url($message->sender) }}" width="48" height="48" /><br />
				<span class="sender-name">{!! $message->sender->getUserNameWithIcon() !!}</span>
			</a>
		</div>
		<div class="sender-message-date">
			{!! format_date('H:i', $message->created_at) !!} {{ trans('common.' . format_date('a', $message->created_at)) }}
		</div>
		<div class="sender-message">
			<div class="message">
				<div class="message-inner">
					{!! strip_tags(nl2br($message->message), '<br>') !!}
				</div>
				{!! render_files($message->files) !!}
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>