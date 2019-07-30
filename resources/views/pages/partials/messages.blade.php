<?php

use iJobDesk\Models\Message;

?>
<div id="message_list_{{ $id }}" class="message-list p-4">

@if (count($messages) != 0)
	<div id="scroll-panel" class="scrollspy-panel slim-scroll" data-enable-load="{{ $limit < $message_count?1:0 }}">
		<div class="loading">{!! render_block_ui_default_html() !!}</div>
		@if (($limit < $message_count && !empty($first_load)) || ($message_count > Message::PER_PAGE && count($messages) == Message::PER_PAGE))
			<a class="load-more-messages">{{ trans('message.load_more_messages') }}</a>
		@endif
		
		<?php $date = null; ?>
		@for ($i = count($messages) - 1; $i >= 0; $i--)
			@if ($date != format_date('Y-m-d', $messages[$i]->created_at))
			<div class="message-date-seperator"><span>{{ trans('common.weekdays.' . format_date('N', $messages[$i]->created_at)) }} {!! format_date($format_date2, $messages[$i]->created_at) !!}</span></div>
				<?php $date = format_date('Y-m-d', $messages[$i]->created_at); ?>
			@endif
			@include('pages.partials.message.row', ['message' => $messages[$i]])
		@endfor
	</div>
@else
	<div class="not-found-result">{{ trans('message.no_messages') }}</div>
@endif

@include('pages.partials.message.form')

</div>