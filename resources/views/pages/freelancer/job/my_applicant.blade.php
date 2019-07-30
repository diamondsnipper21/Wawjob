<?php
/**
 * Job Applicant Page (applicant/{id})
 *
 * @author  - Ri Chol Min
 */
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\File;
?>
@extends('layouts/default/index')

@section('content')

<script type="text/javascript">
    var rate = {{ $rate }};
</script>

{!! Breadcrumbs::render('proposal', $application) !!}

<div class="page-content-section shadow-box">
    {{ show_warnings() }}

	<div class="view-section job-content-section {{ $job->isHourly() ? 'hourly-job' : 'fixed-job' }}">
		<div class="row">
			<div class="col-md-9 col-sm-8 clearfix">
				<div class="pl-2 pr-2">
					<div class="job-top-section {{ $job->is_featured == 1 ? ' featured' : '' }}">
						<div class="title-section border-0">
							<span class="title break">{{ $job->subject }}</span>
							@if ( $job->isFeatured() )
							<span class="label-featured round-ribbon">{{ trans('common.featured') }}</span>
							@endif
						</div>
					</div>

					{{ show_messages() }}

	                <div class="section clearfix">
	                    <div class="job-category rounded-item pull-left">{{ parse_multilang($job->category->name, App::getLocale()) }}</div>
	                    <div class="past-time pull-left">{{ trans('common.posted' )}} {{ ago($job->created_at) }}</div>
	                </div>
	                <div class="section clearfix">
	                    @include ('pages.job.detail.top_summary')
	                </div>

					<div class="box-section margin-bottom-35">
						<div class="title mt-4 mb-2">{{ trans('common.description') }}</div>
						<div class="description pb-4 border-bottom">
							{!! render_more_less_desc($job->desc) !!}

							@if (count($job->files) != 0)
	                        <div class="attachments mt-4">
	                            <div class="title mb-0">{{ trans('common.attachments') }}</div>
	                            <div class="clearfix">
									{!! render_files($job->files) !!}
								</div>
	                        </div>
	                        @endif

							@if ( count($job->skills) )
	                        <div class="project-skills mt-3">
	                            <div class="title margin-bottom-10">
	                                <strong>{{ trans('common.required_skills') }}</strong>
	                            </div>
	                            <div class="clearfix">
	                                @foreach ( $job->skills as $skill )
	                                <span class="rounded-item">{{ parse_multilang($skill->name) }}</span>
	                                @endforeach
	                            </div>
	                        </div>
	                        @endif
						</div>

						<div class="title pt-4 pb-3">{{ trans('common.cover_letter') }}</div>
						<div class="description">
							<div class="cvletter">
							@if ( $application->isInvited() && $application->invitation )
								<div class="letter">
								{!! render_more_less_desc($application->invitation->answer, 180) !!}
								</div>
							@else
								<div class="letter">
								{!! render_more_less_desc($application->cv, 180) !!}
								</div>
							@endif
							</div>
						</div>
	                    <div class="attachments">
	                    	{!! render_files($application->files) !!}
	                    </div>
					</div>
				</div>
			</div>

			<div class="col-md-3 col-sm-4">
				<div class="client-applied">
					@if (!$application->project->client->trashed())
						@if ( in_array($application->status, [
							ProjectApplication::STATUS_PROJECT_CANCELLED, 
							ProjectApplication::STATUS_PROJECT_EXPIRED
						]) || $application->isDeclined() )
							@if ( $application->isDeclinedByFreelancer() )
							<label class="danger">{{ trans('common.withdrawn_by_you') }}</label>
							@elseif ( $application->isDeclinedByBuyer() )
							<label class="danger">{{ trans('common.declined_by_client') }}</label>
							@endif
						@else
						<div class="sub-section">
							<label>{{ trans('job.you_applied_this_job') }}</label>
							<div class="price">
								{{ formatCurrency($application->price, $currency_sign) . ($job->isHourly() ? '/' . trans('common.hr') : '') }}
							</div>
							@if ( !$job->isHourly() )
								<div class="duration">
								{{ trans('job.est_duration') }}: {{ $application->duration_string() }}
								</div>
							@endif
							<a class="btn btn-primary {{ $application->project->client->trashed() || $current_user->isSuspended() ? 'disabled' : '' }}" href="#modalChange" data-toggle="modal">{{ trans('common.revise_term') }}</a>
						</div>

						<div class="sub-section">
							<a class="btn btn-normal btn-withdraw {{ $application->project->client->trashed() || $current_user->isSuspended() ? 'disabled' : '' }}" href="#modalWithdraw" data-toggle="modal" data-backdrop="static">{{ trans('common.withdraw_proposal') }}</a>
						</div>
						@endif
						@if ( $application->isActive() )
						<div class="sub-section">
							<a class="btn btn-normal btn-send-message {{ $application->project->client->trashed() || $current_user->isSuspended() ? 'disabled' : '' }}" href="{{ _route('message.list', ['id' => $application->messageThread->id]) }}">{{ trans('common.send_message') }}</a>
						</div>
						@endif
					@endif
				</div>

				@include ('pages.job.detail.client_info')
			</div>
		</div>
	</div>   
</div>

@if ( !$current_user->isSuspended() )
	@include ('pages.freelancer.job.modal.change_term')
	@include ('pages.freelancer.job.modal.withdraw_proposal')
@endif

@endsection