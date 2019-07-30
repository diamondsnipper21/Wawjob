<?php
/**
 * Message Rooms 
 *
 * @author- KCG
 */

use iJobDesk\Models\User;
use iJobDesk\Models\Contract;

?>
<div id="messages_container" class="col-sm-9 pl-0">
	@if ($thread->application)
	<?php
		$application= $thread->application;
		$project 	= $application->project;
		$contract   = $application->contract;

		$sender     = $thread->sender;
		$freelancer = $application->user;
		$buyer 	    = $project->client;
		if ($current_user->isBuyer())
			$sender = $freelancer;
		else
			$sender = $buyer;
	?>

	<form method="post">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="thread_id" value="{{ $thread_id }}" />
		<input type="hidden" name="keywords" value="{{ $keywords }}" />
		<input type="hidden" name="tab" value="{{ $tab }}" />
		<input type="hidden" name="action" value="LOAD_THREAD" />
	</form>
	<div class="thread-short-info border-box border-left-0 border-top-0 border-right-0 pb-0 pt-2 px-2">
		<div class="row">
			<div class="col-sm-12">
				<a href="{{ $sender->isFreelancer()?_route('user.profile', ['uid' => $sender->id]):'javascript:void(0)' }}" class="thread-sender-name" target="_blank">{{ $sender->fullname() }}</a>
				&nbsp;&nbsp;&nbsp;
				<span><i class="icon-clock"></i>&nbsp;{{ format_date('g:i', date('Y-m-d H:i:s'), $sender) }} {{ trans('common.' . format_date('a', date('Y-m-d H:i:s'), $sender)) }} {{ trans('common.now') }}</span>

				@php
					$subject = $thread->subject;
					if ($contract && !$contract->isOffer())
						$subject = $contract->title;
				@endphp
				
				@if ($current_user->isFreelancer()) {{-- Job Application Detail --}}
				<a href="{{ _route('job.application_detail', ['id' => $thread->application_id]) }}" class="thread-project pull-right" target="_blank">{{ $subject }}</a>
				@elseif (Contract::isHired($project, $application->user)) {{-- Hire / Offers --}}
				<a href="{{ _route('job.hire_offers', ['id' => $project->id]) }}" class="thread-project pull-right" target="_blank">{{ $subject }}</a>
				@else {{-- Review Proposals --}}
				<a href="{{ _route('job.interviews', ['id' => $project->id]) }}" class="thread-project pull-right" target="_blank">{{ $subject }}</a>
				@endif
			</div>
		</div>
	</div>
	<div class="row">
		<div id="messages" class="col-sm-9 pr-0">
			@include('pages.message.partials.messages', ['container' => '#messages_container'])
		</div>
		<div id="attachments" class="col-sm-3 pl-0 pr-0">
			@include('pages.message.partials.attachments')
		</div>
	</div>
	@endif
</div>