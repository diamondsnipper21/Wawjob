<?php
	use iJobDesk\Models\ProjectApplication;
	use iJobDesk\Models\ProjectInvitation;
	use iJobDesk\Models\Project;
	$user_invited = $user->hasInvited($job->id);
?>
<div class="user-item clearfix">
	@include('pages.partials.user')

	<div class="user-action">
		@if ( $sub_page == 'invited' )
		<div class="invitation-meta">
			<div class="meta">
				@if ( $user_invited['status'] == ProjectInvitation::STATUS_ACCEPTED )
				<span class="round-ribbon label-accepted">{{ trans('common.accepted') }}</span>
				@elseif ( $user_invited['status'] == ProjectInvitation::STATUS_NORMAL )
				<span class="round-ribbon label-pending">{{ trans('common.pending') }}</span>
				@elseif ( $user_invited['status'] == ProjectInvitation::STATUS_DECLINED )
				<span class="round-ribbon label-declined">{{ trans('common.declined') }}</span>
				@elseif ( $user_invited['status'] == ProjectInvitation::STATUS_ACTIVE )
				<span class="round-ribbon label-warning">{{ trans('common.active') }}</span>
				@endif
			</div>
			
			<div class="meta"><strong>{{ trans('common.sent') }}</strong>: {{ format_date('M d, Y H:i A', $user_invited['created_at']) }}</div>
			
			@if ( $user_invited['status'] == ProjectInvitation::STATUS_ACCEPTED )
				<div class="meta"><strong>{{ trans('common.accepted') }}</strong>: {{ format_date('M d, Y H:i A', $user_invited['updated_at']) }}</div>
			@elseif ( $user_invited['status'] == ProjectInvitation::STATUS_DECLINED )
				<div class="meta"><strong>{{ trans('common.declined') }}</strong>: {{ format_date('M d, Y H:i A', $user_invited['updated_at']) }}</div>
				<div class="meta"><strong>{{ trans('common.reason') }}</strong>: {{ $user_invited['reason'] }}</div>
			@endif
		</div>
		@else
			<div class="btn-wrap">
				@if ( $user_invited )
					<label class="border"><i class="fa fa-check"></i>&nbsp;{{ trans('common.invitation_sent') }}</label>
				@else
					@if ( $user->hasHiring($job->id) )
						<label class="border"><i class="fa fa-check"></i>&nbsp;{{ trans('common.hired') }}</label>
					@else
						<button type="button" class="btn btn-primary btn-invite {{ !$job->isAvailableInvite() ? 'disabled' : '' }}" data-json="{{ $job->getInvitationJson($user) }}">{{ trans('common.invite_to_job') }}</button>
					@endif
				@endif
			</div>

			<div class="btn-wrap">
			@if ( !$user->isSaved && !$job->isSuspended() )
				<button type="button" class="btn btn-normal btn-save" data-url={{ _route('search.user.save') }} data-id="{{ $user->id }}"><i class="fa fa-heart-o"></i>&nbsp;{{ trans('common.save') }}</button>
			@else
				<label class="border"><i class="fa fa-heart"></i>&nbsp;{{ trans('common.saved') }}</label>
			@endif
			</div>
		@endif
	</div><!-- .user-action -->
</div><!-- .user-item -->