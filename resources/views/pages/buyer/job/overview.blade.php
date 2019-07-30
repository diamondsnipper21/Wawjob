<?php
/**
* Job Posting Overview Page (job/{id}/overview)
*
* @author Ro Un Nam
* @since May 31, 2017
*/

use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\File;
?>
@extends($current_user->isAdmin()?('layouts/admin/super'.(!empty($user_id)?'/user':'/job_detail')):'layouts/default/index')

@section('content')

{!! Breadcrumbs::render('job_posting', $job) !!}

<div class="shadow-box job-overview-page">

	{{ show_warnings() }}

	@include('pages.buyer.job.section.job_title')
	
	{{ show_messages() }}

	<div class="page-content-section no-padding">
		<div class="job-top-section">
		@include('layouts.buyer.section.job_top_links')
		</div>

		@if ($current_user->isAdmin() && !empty($user_id))
			@include('layouts.admin.super.job_detail_nav')
		@endif

		<div class="overview-section">
			@if (empty($user_id) && Auth::user()->isAdmin())
			<div class="row margin-bottom-20">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-12">
							<a href="{{ route('admin.super.user.overview', ['user_id' => $job->client_id]) }}"><img src="{{ avatar_url($job->client) }}" class="img-circle" width="100" /></a>&nbsp;&nbsp;&nbsp;
							<a href="{{ route('admin.super.user.overview', ['user_id' => $job->client_id]) }}">{{ $job->client->fullname() }}</a>
						</div>
					</div>
				</div>
			</div>
			@endif

			<div class="row">
				<div class="col-md-8">
					<div class="title-section break">
		                <span class="title">{{ $job->subject }}</span>
		            </div>
					@include('pages.job.detail.top')
				</div>

				<div class="col-md-4">
					<div class="content-right border-light-left pl-4">
						<div class="border-light-bottom pt-2 pb-2 mb-4 values">
							<div class="row">
								<div class="col-xs-3">
									<span class="value">{{ $job->totalInvitationsCount() }}</span>
									<label class="control-label">{{ trans('common.invites') }}</label>
								</div>
								<div class="col-xs-3">
									<span class="value">{{ $job->totalProposalsCount() }}</span>
									<label class="control-label">{{ trans('common.proposals') }}</label>
								</div>
								<div class="col-xs-3">
									<span class="value">{{ $job->totalInterviewsCount() }}</span>
									<label class="control-label">{{ trans('common.interviews') }}</label>
								</div>
								<div class="col-xs-3">
									<span class="value">{{ Contract::totalHiresCount($job->id) }}</span>
									<label class="control-label">{{ trans('common.hired') }}</label>
								</div>
							</div>
						</div>

						<div class="row pt-4">
							<label class="col-xs-6 control-label">{{ trans('job.type_of_project') }}</label>
							<div class="col-xs-6">{{ $job->term_string() }}</div>
						</div>

						<div class="row pt-2">
							<label class="col-xs-6 control-label">{{ trans('job.freelancers_needed')}}</label>
							<div class="col-xs-6">{{ $job->contract_limit_string() }}</div>
						</div>

						<div class="row pt-2">
							<label class="col-xs-6 control-label">{{ trans('common.job_visibility') }}</label>
							<div class="col-xs-6">
								{{ $job->is_public_string() }}
							</div>
						</div>

						<div class="row pt-2 mb-4 pb-4">
							<label class="col-xs-6 control-label">{{ trans('common.cover_letter') }}</label>
							<div class="col-xs-6">
								{{ $job->req_cv == 1 ? trans('common.yes_required') : trans('common.no_required') }}
							</div>
						</div>

		                @if ($job->qualification_success_score || $job->qualification_location || $job->qualification_hours)
						<div class="border-light-top pt-4 preferred">
							<div class="pb-2">
								<label class="control-label">{{ trans('job.preferred_qualifications') }}</label>
							</div>
							
                            @if ($job->qualification_success_score)
                            <div class="row">
                                <label class="col-md-6 control-label">{{ trans('job.preferred_success_score') }}</label>
                                <span class="col-md-6">{{ trans('job.at_least_n_percents', ['n' => $job->qualification_success_score]) }}</span>
                            </div>
                            @endif                            
                            @if ($job->qualification_hours)
                            <div class="row">
                                <label class="col-md-6 control-label">{{ trans('common.hours_worked') }}</label>
                                <span class="col-md-6">{{ trans('job.at_least_n_hours', ['n' => $job->qualification_hours]) }}</span>
                            </div>
                            @endif
                            @if ($job->qualification_location)
                            <div class="row">
                                <label class="col-md-6 control-label">{{ trans('common.location') }}</label>
                                <span class="col-md-6">{{ $job->qualification_location }}</span>
                            </div>
                            @endif
						</div>
						@endif
					</div>
				</div>
			</div>
			
		</div><!-- .overview-section -->

		@if ( $job->accept_term != Project::ACCEPT_TERM_YES )
			@include('pages.buyer.job.section.accept_term')
		@endif
	</div><!-- .page-content-section -->
</div><!-- .page-content -->
@endsection