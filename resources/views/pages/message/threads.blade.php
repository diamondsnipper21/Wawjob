<?php
/**
 * Message Rooms 
 *
 * @author- KCG
 */

use iJobDesk\Models\User;
?>

@extends('layouts/default/index', ['fullwidth' => true])
@section('content')
<script type="text/javascript">
	var message_base_url = '{{ _route('message.list') }}';
</script>
<div id="message_threads_container" class="row">
	<div id="message_threads" class="col-sm-3">
		<div class="message-threads-inner">
			<form method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				
				<input type="hidden" name="thread_id" value="{{ $thread_id }}" />
				<input type="hidden" name="per_page" value="{{ $per_page }}" />
				<input type="hidden" name="tab" value="{{ $tab }}" />
				<input type="hidden" name="action" value="LOAD_THREADS" />

				<!-- Search Box -->
				<div class="input-group">
					<input name="keywords" class="form-control" type="text" placeholder="{{ trans('common.search_for_threads') }}" value="{{ $keywords }}" />
					<span class="input-group-addon">
						<i class="icon-magnifier"></i>
					</span>
				</div>
			</form>

			<div class="tabs clearfix">
				<div class="tab"><a href="#threads_inbox" class="{{ $tab == 'inbox'?'active':'' }}">{{ trans('common.inbox') }}</a></div>
				<div class="tab"><a href="#threads_unread" class="{{ $tab == 'unread'?'active':'' }}">{{ trans('common.unread') }}</a></div>
				<div class="tab"><a href="#threads_archive" class="{{ $tab == 'archive'?'active':'' }}">{{ trans('common.archived') }}</a></div>
				<div class="tab"><a href="#threads_all" class="{{ $tab == 'all'?'active':'' }}">{{ trans('common.all') }}</a></div>
			</div>
			<div class="clearfix"></div>

			<!-- Threads -->
			<div id="threads">
				@include('pages.message.partials.senders')
			</div>
		</div>
	</div>
	@include('pages.message.partials.message_container')
</div>
@endsection