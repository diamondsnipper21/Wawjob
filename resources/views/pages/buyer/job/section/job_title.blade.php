<?php
/**
* Section for the job title on the buyer
* @author Ro Un Nam
* @since May 25, 2017
*/
use iJobDesk\Models\Project;
?>
<div class="title-section">
	<div class="row">
		<div class="col-sm-9 col-xs-12">
			<div class="title break">
				{{ $job->subject }}

				@if ( $job->isPrivate() )
				<i class="icon-lock" data-toggle="tooltip" title="{{ trans('job.only_freelancers_invited_can_find_this_job') }}"></i>
				@elseif ( $job->isProtected() )
				<i class="icon-shield" data-toggle="tooltip" title="{{ trans('job.only_ijobdesk_users_can_find_this_job') }}"></i>
				@endif

				@if ( $job->isFeatured() )
				<span class="label-featured round-ribbon">{{ trans('common.featured') }}</span>
				@endif
				@if ( $job->isClosed() )
				<span class="label-closed round-ribbon">{{ trans('common.closed') }}</span>
				@elseif ( $job->isCancelled() )
				<span class="label-cancelled round-ribbon">{{ trans('common.cancelled') }}</span>
				@elseif ( $job->isSuspended() )
				<span class="label-suspended round-ribbon">{{ trans('common.suspended') }}</span>
				@endif
			</div>
		</div>
		
		<div class="col-sm-3 col-xs-12 job-info">
			<div class="job-action pull-right">
				<a class="btn btn-link action-link dropdown-toggle {{ $current_user->isSuspended() || $job->isSuspended() ? 'disabled' : '' }}" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
					<i class="icon-settings"></i> {{ trans('common.action') }} <i class="icon-arrow-down"></i>
				</a>

				<ul class="dropdown-menu">
					@if ( $job->isClosed() || $job->isCancelled() )
						<li>
		                    <a class="btn btn-link" data-status="view" href="{{ _route('job.edit.repost', ['id' => $job->id, 'action'=>'repost']) }}">
		                        <i class="icon-note"></i> {{ trans('common.repost') }}
		                    </a>
		                </li>
					@else
						<li>
							<a class="btn btn-link" data-status="edit" href="{{ _route('job.edit', ['id' => $job->id]) }}">
								<i class="icon-pencil"></i> {{ trans('common.edit') }}
							</a>
						</li>
						<li>
							<a class="btn btn-link" data-status="close" data-url="{{ _route('job.change_status.ajax', ['id' => $job->id, 'status'=>Project::STATUS_CLOSED]) }}">
								<i class="icon-close"></i> {{ trans('common.close') }}
							</a>
						</li>
						<li>
							<a class="btn btn-link" data-status="cancel" data-url="{{ _route('job.change_status.ajax', ['id' => $job->id, 'status' => Project::STATUS_CANCELLED]) }}">
								<i class="icon-ban"></i> {{ trans('common.cancel') }}
							</a>
						</li>
						<li class="dropdown-submenu">
							<a class="btn btn-link visibility" data-status="visibility">
								<i class="icon-eye"></i> {{ trans('common.visibility') }} &nbsp;<i class="icon-arrow-right"></i>
							</a>
							<ul class="visibility-status">
								<li>
									<a class="btn btn-link {{ $job->isPublic() ? 'disabled' : '' }}" data-status="public" data-url="{{ _route('job.change_public.ajax', ['id' => $job->id, 'public'=>Project::STATUS_PUBLIC]) }}" title="Visible to Everybody">
										<i class="icon-share-alt"></i> {{ trans('common.public') }}
									</a>
								</li>
								<li>
									<a class="btn btn-link {{ $job->isProtected() ? 'disabled' : '' }}" data-status="protected" data-url="{{ _route('job.change_public.ajax', ['id' => $job->id, 'public'=>Project::STATUS_PROTECTED]) }}" title="Visible only to {{ config('app.name') }} Freelancers">
										<i class="icon-shield"></i> {{ trans('common.protected') }}
									</a>
								</li>
								<li>
									<a class="btn btn-link {{ $job->isPrivate() ? 'disabled' : '' }}" data-status="private" data-url="{{ _route('job.change_public.ajax', ['id' => $job->id, 'public' => Project::STATUS_PRIVATE]) }}" title="Visible only to {{ config('app.name') }} Invited Freelancers">
										<i class="icon-lock"></i> {{ trans('common.private') }}
									</a>
								</li>
							</ul>
						</li>
						
					@endif
				</ul>
			</div>
		</div>
	</div>
</div><!-- .title-section -->