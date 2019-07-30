<?php
/**
* @author Ro Un Nam
* @since May 26, 2017
*/
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\User;
use iJobDesk\Models\File;
use iJobDesk\Models\Contract;
use iJobDesk\Models\ProjectOffer;

$proposal = ProjectApplication::find($proposal->id);
$user = $proposal->user;
$project = $proposal->project;

$qualified_tooltip = '';

$qualified = true;

if ( $project->qualification_success_score ) {
	if ( $user->stat->job_success < $project->qualification_success_score ) {
		$qualified = false;

		$qualified_tooltip .= trans('common.job_success_score') . ': ' . trans('job.at_least_n_percents', ['n' => $project->qualification_success_score]);
	}
}

if ( $project->qualification_hours ) {
	if ( $user->stat->hours < $project->qualification_hours ) {
		$qualified = false;
		
		if ( $qualified_tooltip ) {
			$qualified_tooltip .= "<br />";
		}

		$qualified_tooltip .= trans('common.hours_worked') . ': ' . trans('job.at_least_n_hours', ['n' => $project->qualification_hours]);
	}
}

if ( $project->qualification_location ) {
	if ( ($user->contact->country->region != $project->qualification_location) && ($user->contact->country->sub_region != $project->qualification_location) ) {
		$qualified = false;

		if ( $qualified_tooltip ) {
			$qualified_tooltip .= "<br />";
		}

		$qualified_tooltip .= trans('common.location') . ': ' . $project->qualification_location;
	}
}

?>

<div class="proposal-item{{ $proposal->is_liked == 1 ? ' liked' : '' }}{{ $proposal->is_liked == -1 ? ' disliked' : '' }}{{ $proposal->is_featured ? ' featured' : '' }}{{ $proposal->messageThread ? ' interviewing' : '' }} user-item clearfix" data-id="{{ $proposal->id }}" data-status="{{ $proposal->status }}">

	<div class="user-avatar">
		<a href="{{ _route('user.profile', [$user->id]) }}"><img alt="{{ $user->fullname() }}" class="img-circle" src="{{ avatar_url($user) }}" width="100" height="100"></a>
	</div>

	<div class="user-info">
		<div class="row">
			<div class="col-sm-9">
				<h4 class="name">
					<a href="{{ _route('user.profile', [$user->id]) }}">{{ $user->fullname() }}</a>
				</h4>
				<div class="user-title">{{ $user->profile->title }}</div>

				@if ( $user->stat )
				<div class="row-1 row">
					<div class="col-sm-5">
						<div class="feedback">
							<div class="score" data-toggle="tooltip" title="{{ number_format($user->stat->score, 1) }}">
								<div class="stars" data-value="{{ $user->stat ? $user->stat->score / 5 * 100:0 }}%"></div>
							</div>

							<div class="reviews">
								<strong>{{ $user->stat->total_reviews}}</strong>&nbsp;
								{{ trans('common.reviews') }}
							</div>

							<div class="clearfix"></div>
						</div>

						@if ( $user->contact->country )
						<div class="country mb-3">
							<img src="/assets/images/common/flags/{{ strtolower($user->contact->country->charcode) }}.png" class="flag"> <strong>{{ $user->contact->country->name }}</strong>
						</div>
						@endif
					</div>

					@if ( $user->stat->earning > 0 )
					<div class="col-sm-3">
						<div class="earned">
							<strong>{{ $currency_sign }}{{ formatEarned($user->stat->earning) }}</strong> 
							{{ trans('common.earned') }}
						</div>
					</div>
					@endif

					@if ( $user->stat->job_success > 0 )
					<div class="col-sm-4 text-center">
						<div class="profile-success-percent">
			                {{ trans('profile.success_percent', ['n' => $user->stat->job_success]) }}
			                <div style="width: {{ $user->stat->job_success }}%;"></div>
			            </div>
					</div>
					@endif
				</div><!-- .row -->
				@endif
			</div>
			<div class="col-sm-3 pr-4">
				<div class="box-bid border-light">
					<div class="sub-title p-1">{{ $proposal->is_featured ? trans('common.featured') . ' ' : '' }}{{ trans('common.bid') }}</div>
					<div class="p-3">
						<div class="user-price">
							@if ( $proposal->type == Project::TYPE_HOURLY )
								<strong>${{ formatCurrency($proposal->price) }}</strong> / {{ trans('common.hr') }}
							@else
								<strong>{{ formatCurrency($proposal->price, $currency_sign) }}</strong>
							@endif
						</div>

						@if ( $proposal->type != Project::TYPE_HOURLY && $proposal->is_checked )
						<div class="mt-2 text-center">
							<span class="ago">
								@if ( $proposal->duration_string() )
									{{ $proposal->duration_string() }}
								@endif
							</span>
						</div>
						@endif
						
						@if ( !$proposal->is_checked )
						<div class="mt-2 text-right">
							<span class="round-ribbon label-new">{{ trans('common.new') }}</span>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div><!-- .row -->
		
		@if ( !$proposal->isInvited() && $proposal->cv )
		<div class="user-cover-letter mb-2 row-4">
			<span class="heading">{{ trans('common.cover_letter') }}</span>

			<div class="letter">
				{!! render_more_less_desc($proposal->cv) !!}
			</div>

			<div class="files">
				{!! render_files($proposal->files) !!}
			</div>
		</div>
		@endif
		
	</div><!-- .user-info -->

	@if ( !$current_user->isAdmin() )
	<div class="user-action border-light-left">
		@if ( !$current_user->isSuspended() )
		<div class="btn-top border-light-bottom">
			<div class="row">
				<div class="col-sm-6 col-xs-4">
					<button type="button" class="btn btn-like{{ $proposal->is_liked == 1 ? ' dislike' : ' like' }}{{ $current_user->isSuspended() || $job->isSuspended() ? ' disabled' : '' }}" title="{{ $proposal->is_liked == 1 ? trans('common.like') : trans('common.dislike') }}">
						<i class="fa fa-heart" aria-hidden="true"></i>
						<i class="fa fa-heart-o" aria-hidden="true"></i>
					</button>
				</div>

				<div class="col-sm-6 col-xs-8 text-right">
					@if ( !$proposal->isArchived() )
						@if ( !$proposal->isDeclined() )
						<button type="button" class="btn btn-archive" {{ $current_user->isSuspended() || $job->isSuspended() ? 'disabled' : '' }} data-toggle="tooltip" data-placement="top" title="{{ trans('common.archive') }}"><i class="fa fa-folder-o" aria-hidden="true"></i></button>
						@endif
					@else
						<button type="button" class="btn btn-unarchive" {{ $current_user->isSuspended() || $job->isSuspended() ? 'disabled' : '' }} data-toggle="tooltip" data-placement="top" title="{{ trans('common.undo') }}"><i class="fa fa-folder-open-o" aria-hidden="true"></i></button>	
					@endif
					@if ( !$proposal->isDeclined() )
						<button type="button" class="btn btn-decline" {{ $current_user->isSuspended() || $job->isSuspended() ? 'disabled' : '' }} data-toggle="tooltip" data-placement="top" title="{{ trans('common.decline') }}"><i class="hs-admin-trash"></i></button>
					@endif
				</div>
			</div>
		</div><!-- .btn-top -->

		<div class="btn-wrap border-light-bottom">
			<div class="row">
				<div class="col-sm-6" title="{{ $current_user->isSuspended() ? trans('common.user_suspended') : ($proposal->user->isSuspended() ? trans('common.freelancer_suspended') : ($job->isSuspended() ? trans('common.project_suspended') : ($proposal->isHired() ? trans('common.hired') : (ProjectOffer::isSent($job, $proposal->user) ? trans('common.offer_sent') : '' )))) }}">
					<a href="{{ _route('job.hire', ['id' => $proposal->project_id, 'uid' => $proposal->user->id, 'pid' => $proposal->id]) }}" class="btn btn-primary btn-hire {{ $current_user->isSuspended() || $proposal->user->isSuspended() || $job->isSuspended() || $proposal->isHired() || ProjectOffer::isSent($job, $proposal->user) ? 'disabled' : '' }}">{{ trans('common.hire') }}</a>
				</div>

				<div class="col-sm-6">
				@if ( $proposal->messageThread )
					<a class="btn btn-normal {{ $job->isSuspended() || $proposal->user->isSuspended() ? 'disabled' : '' }}" href="{{ _route('message.list', ['id' => $proposal->messageThread->id]) }}" target="_blank">{{ trans('common.send_message')}}</a>
				@else
					<a class="btn btn-normal btn-send-message {{ $current_user->isSuspended() || $job->isSuspended() || $proposal->user->isSuspended() ? 'disabled' : '' }}" data-toggle="modal" data-target="#messagesModal" data-project="{{ $proposal->project_id }}" data-user="{{ $proposal->user_id }}" data-proposal="{{ $proposal->id }}" data-user-name="{{ $proposal->user->fullname() }}" data-user-title="{{ $proposal->user->profile->title }}" data-user-url="{{ _route('user.profile', ['uid' => $proposal->user->id]) }}" data-user-avatar="{{ avatar_url($proposal->user) }}">{{ trans('common.interview') }}</a>
				@endif
				</div>
			</div>
		</div><!-- .btn-wrap -->

		<div class="status">
			<div class="row">
				<div class="col-sm-6">
					@if ( !$qualified )
					<div class="mb-2 qualification">
						<span class="status-dot label-not-qualified"></span> {{ trans('common.not_qualified') }}<i class="icon icon-question ml-2" data-toggle="tooltip" data-html="true" title="{!! $qualified_tooltip !!}"></i>
					</div>
					@endif

					@if ( $proposal->isInvited() )
						<span class="status-dot label-invited"></span> {{ trans('common.invited') }}
					@elseif ( $proposal->isCancelled() )
						<span class="status-dot label-cancelled"></span> {{ trans('common.cancelled') }}
					@elseif ( $proposal->isClosed() )
						<span class="status-dot label-closed"></span> {{ trans('common.closed') }}
					@elseif ( $proposal->isExpired() )
						<span class="status-dot label-expired"></span> {{ trans('common.expired') }}
					@endif
				</div>

				<div class="col-sm-6">
					@if ( $proposal->isDeclined() )
						<div class="mb-2">
						@if ( $proposal->isDeclinedByFreelancer() )
							<span class="status-dot label-withdraw"></span> {{ trans('common.withdrawn') }}
						@elseif ( $proposal->isDeclinedByBuyer() )
							<span class="status-dot label-declined"></span> {{ trans('common.declined') }}
						@endif
						</div>
					@else						
						@if ( $proposal->isHired() )
						<div class="mb-2">
							<span class="status-dot label-info"></span> {{ trans('common.hired') }}
						</div>
						@elseif ( $proposal->isActive() )
						<div class="mb-2">
							<span class="status-dot label-interview"></span> {{ trans('common.interview') }}
						</div>
						@endif				
					@endif
				</div>
			</div>
		</div>

		<div class="box-decline">
			<button type="button" class="close">&times;</button>
			<div class="box-title">
				{{ trans('common.decline') }} <a href="{{ _route('user.profile', [$proposal->user->id]) }}">{{ $proposal->user->fullname() }}</a>{{ trans('common.s_proposal') }}
			</div>
			<div class="box-user-info">
				<div class="row">
					<div class="col-sm-2 avatar">
						<a href="{{ _route('user.profile', [$proposal->user->id]) }}"><img alt="{{ $proposal->user->fullname() }}" class="img-circle pull-left" src="{{ avatar_url($proposal->user) }}" width="30" height="30"></a>
					</div>
					<div class="col-sm-10 info">
						<a href="{{ _route('user.profile', [$proposal->user->id]) }}">{{ $proposal->user->fullname() }}</a>
						<span>{{ $proposal->user->profile->title }}</span>
					</div>
				</div>
			</div>
			@if ( $job->status != Project::STATUS_SUSPENDED )
			<div class="box-message">
				<form class="form-horizontal form-decline" method="post" action="{{ _route('job.interviews', ['id' => $job->id, 'page' => $sub_page]) }}">
  					<input type="hidden" name="_token" value="{{ csrf_token() }}">
  					<input type="hidden" name="id" value="{{ $proposal->id }}">
  					<input type="hidden" name="action" value="decline">
  					<div class="box-ctrl">
						<label>{{ trans('common.reason') }}</label>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="0" checked="checked">
								{{ trans('job.reason_prefer_other_style') }}
							</label>
						</div>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="1">
								{{ trans('job.reason_too_high_price') }}
							</label>
						</div>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="2">
								{{ trans('job.reason_no_desirable_experience') }}
							</label>
						</div>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="3">
								{{ trans('common.other') }}
							</label>
						</div>
					</div>
					<div class="box-ctrl">
						<label>{{ trans('common.message') }} (<span>{{ trans('common.optional') }}</span>)</label>
						<textarea name="decline_message" class="form-control"></textarea>
					</div>
					<button type="button" class="btn btn-primary btn-submit-decline">{{ trans('common.decline_proposal') }}</button>
					<a class="btn btn-link btn-cancel-decline">{{ trans('common.cancel') }}</a>
				</form>
			</div>
			@endif
			
		</div><!-- .box-decline -->
		@else
			@if ( $proposal->isDeclined() && $proposal->isDeclinedByFreelancer() )
				<div class="declined-reason">
					{{ $proposal->withdrawn_reason_string() }}
				</div>
			@endif
		@endif

	</div><!-- .user-action -->
	@endif
</div><!-- .proposal-item -->