<?php
/**
* Job Detail Page (job/{id})
*
* @author  - Ri Chol Min
*/

use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\File;
?>
@extends('layouts/default/index')

@section('content')
<div class="page-content-section page-job-detail no-padding">
    <div class="view-section job-content-section {{ $job->isHourly() ? 'hourly-job' : 'fixed-job' }}">
        <form id="JobDetailForm" class="form-horizontal" method="post" action="{{_route('job.view', ['id' => $job->id])}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-9 col-sm-8">
				    <div class="job-top-section mb-2{{ $job->isFeatured() ? ' featured' : '' }}">
				        <div class="title-section border-0 break">
				            <h2>
				            	{{ $job->subject }}
				            	@if ( $job->isFeatured() )
				                <span class="label-featured round-ribbon">{{ trans('common.featured') }}</span>
				                @endif
				            </h2>
				        </div>
				    </div>

				    {{ show_messages() }}

                	@include('pages.job.detail.top')

                    <div class="sub-section pl-3 pr-3 mb-4">
                        <div class="activity border-top">
                            <div class="row">
                                @if ($job->qualification_success_score || 
                                $job->qualification_location || 
                                $job->qualification_hours)
                                <!-- Preferred Qualifications -->
                                <div class="col-md-6 col-sm-6">
                                    <div class="title margin-bottom-10">{{ trans('job.preferred_qualifications') }}</div>
                                    @if ($job->qualification_success_score)
                                        <div class="display-info">{{ trans('job.preferred_success_score') }} <span>{{trans('job.at_least_n_percents', ['n' => $job->qualification_success_score]) }}</span></div>
                                    @endif
                                    @if ($job->qualification_location)
                                        <div class="display-info">{{ trans('common.location') }} <span>{{$job->qualification_location}}</span></div>
                                    @endif
                                    @if ($job->qualification_hours)
                                        <div class="display-info">{{ trans('common.hours_cap') }} <span>{{trans('job.at_least_n_hours', ['n' => $job->qualification_hours]) }}</span></div>
                                    @endif
                                </div>
                                @endif

                                <!-- Activity on this job -->
                                <div class="col-md-6 col-sm-6">
                                    <div class="title margin-bottom-10">{{ trans('job.activity_on_this_job') }}</div>
                                    <div class="display-info">{{ trans('common.proposals') }} <span>{{ $job->totalProposalsCount() }}</span></div>
                                    <div class="display-info">{{ trans('common.interviewing') }} <span>{{$job->totalInterviewsCount()}}</span></div>
                                    <div class="display-info">{{ trans('common.invites_sent') }} <span>{{ $job->totalInvitationsCount() }}</span></div>
                                    @if ( $job->contract_limit != 1 )
                                    <div class="display-info">{{ trans('common.hires') }} <span>{{ $job->hiredContractsCount() }}</span></div>
                                    @endif
                                    <!-- DONT USE THIS SECTION NOW -->
                                    @if (false)
                                    <div class="display-info">{{ trans('common.unanswered_invites') }}: <span>{{ $job->totalUnansweredInvitationsCount() }}</span></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    @if ( !$job->isClosed() && $current_user && $current_user->isFreelancer() )
                        <div class="action-buttons-section">
                            @if ( !$applied )
                                @if ($needed_connections <= $available_connections)
                                    <a href="{{ _route('job.apply', ['id' => $job->id]) }}" id="submit_proposal" class="btn btn-primary {{ $current_user->isSuspended() ? 'disabled' : '' }}">{{ trans('common.submit_a_proposal') }}</a>
                                @else
                                    <label class="alert-message">{{ trans('job.no_connects') }}</label>
                                @endif
                            @else
                                <div class="sub-section client-applied">
                                    <label>{{ trans('job.you_applied_this_job') }}</label>
                                    <div class="price">
                                        {{ formatCurrency($application->price, $currency_sign) . ($job->isHourly() ? '/' . trans('common.hr') : '') }}
                                    </div>
                                    @if ( !$job->isHourly() )
                                        <div class="duration">
                                        {{ trans('job.est_duration') }}: {{ $application->duration_string() }}
                                        </div>
                                    @endif
                                    <a class="btn btn-primary" href="{{ _route('job.application_detail', ['id' => $application->id]) }}">{{ trans('job.view_proposal') }}</a>
                                </div>
                            @endif
                            <button type="button" id="saved_job" class="btn margin-top-10" {{ $saved || $current_user->isSuspended() ? 'disabled':'' }} data-url="{{ route('saved_jobs.create', ['id' => $job->id]) }}" >
                                <i class="fa {{ $saved ? 'fa-heart' : 'fa-heart-o' }}"></i>&nbsp;&nbsp;{{ $saved ? trans('common.saved') : trans('common.save') }}
                            </button>
                        </div>

                        @if ( !$applied )
                        <div class="connects pt-4">
                            <p>
                                {{ trans('job.required_connects') . ': ' . $needed_connections }}
                                <i class="hs-admin-help-alt" data-toggle="tooltip" title="{{ trans('job.required_connects_desc') }}"></i>
                            </p>
                            <p>
                                {{ trans('job.available_connects') . ': ' . $available_connections }}
                                <i class="hs-admin-help-alt" data-toggle="tooltip" title="{{ trans('job.you_have_n_connects', ['n' => $available_connections]) }}"></i>
                            </p>
                        </div>
                        @endif
                    @endif

                    @include ('pages.job.detail.client_info')
               </div>
            </div>

            @if ( count($end_contracts) )
            <div class="workhistory col-md-9 col-sm-8">
                <div class="margin-bottom-35 border-top">
                    <div class="sub-section">
                        <div class="title margin-bottom-15">
                            {{ trans('job.client_history_feedback') }}
                        </div>

                        @include('pages.job.detail.end_contracts')
                    </div>
                </div>
            </div>
            @endif
            <div class="clearfix"></div>
        </form>
    </div>
</div>
@endsection