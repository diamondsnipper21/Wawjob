<?php
/**
 * @author KCG
 * @since Feb 22, 2018
 */
?>
@foreach ($threads as $key => $thread_list)
<div id="threads_{{ $key }}" class="threads {{ $tab == $key?'active':'' }}">
	<div class="tab-content slim-scroll">
		@foreach ($thread_list as $thread)
		<?php
			$sender 	= null;
			$freelancer = $thread->application->user;
			$buyer 	    = $thread->application->project->client;

			if ($current_user->isBuyer())
				$sender = $freelancer;
			else
				$sender = $buyer;
		?>
		<div class="thread {{ $thread->id == $thread_id?'active':'' }} {{ $thread->unreads?'new-message':'' }} thread-{{ $thread->id }}" data-id="{{ $thread->id }}" data-url="{{ _route('message.list', ['id' => $thread->id]) }}">
			<div class="thread-left-section">
				<img src="{{ avatar_url($sender) }}" class="img-responsive img-circle thread-sender-avatar" />
			</div>
			<div class="thread-right-section">
				<div class="thread-sender-name">{{ $sender->fullname() }}</div>
				<div class="thread-last-date">
					@if (!$thread->messages->isEmpty())
					<?php $last_message = $thread->messages->last(); ?>
						@if (is_today($last_message->created_at))
							{{ format_date('g:i', $last_message->created_at) }} {{ trans('common.' . format_date('a', $last_message->created_at)) }}
						@else
							{{ format_date($format_date, $last_message->created_at) }}
						@endif
					@else
						-
					@endif
				</div>
				<div class="clearfix"></div>
				<div class="thread-title">{{ $thread->subject }}</div>
			</div>
			<div class="clearfix"></div>

			<div class="thread-unreads {{ $thread->unreads?'':'hide' }}">{{ $thread->unreads }}</div>

			<!-- Tools -->
			<div class="tools">
				@if ($key == 'inbox')
				<i class="fa fa-archive archive" title="{{ trans('common.archive') }}"></i>
				@endif
				@if ($key == 'archive' || ($key == 'all' && $thread->isArchived()))
				<i class="fa fa-inbox move-to-inbox" title="{{ trans('common.move_to_inbox') }}"></i>
				@endif
			</div>
		</div>
		@endforeach

		<div class="no-threads no-data py-4 {{ !$thread_list->isEmpty()?'hide':'' }}">
			{{ trans('common.no_threads') }}
		</div>

		@if (!$thread_list->isEmpty() && $thread_list->nextPageUrl())
		<form method="post" action="{{ $thread_list->nextPageUrl() }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<input type="hidden" name="thread_id" value="{{ $thread_id }}" />
			<input type="hidden" name="per_page" value="{{ $per_page }}" />
			<input type="hidden" name="keywords" value="{{ $keywords }}" />
			<input type="hidden" name="tab" value="{{ $tab }}" />
			<input type="hidden" name="action" value="LOAD_THREADS" />
		</form>
		<div class="loading">{!! render_block_ui_default_html() !!}</div>
		@endif
	</div>
</div>
@endforeach