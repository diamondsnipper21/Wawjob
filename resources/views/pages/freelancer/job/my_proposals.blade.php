<?php
/**
* My Proposals Page (job/my_proposals)
*
* @author  - Ri Chol Min
*/
use iJobDesk\Models\Project;
use iJobDesk\Models\User;
?>
@extends('layouts/default/index')

@section('content')
<div id="all_proposals">
    <form method="post"></form>

    {{ show_messages() }}

    @if ( count($job_offers) > 0 )
    <div id="offers" class="job-offer box-section shadow-box">
        {{ show_warnings() }}

        <div class="title-section">
            <span class="title">
                <i class="icon-envelope-open title-icon"></i>
                {{ trans('common.job_offers') }} ({{ count($job_offers) }})
            </span>
        </div>
        <div class="section-content">
            <div class="box-header clearfix">
                <div class="col-sm-2 col-xs-3">{{ trans('common.received') }}</div>
                <div class="col-sm-6 col-xs-6">{{ trans('common.job') }}</div>
                <div class="col-sm-4 col-xs-3">{{ trans('common.client') }}</div>
            </div>
            @foreach ( $job_offers as $offer )
                @if ( $offer->project )
                <div class="box-row clearfix">
                    <div class="col-sm-2 col-xs-3 submitted-date">{{ custom_ago($offer->created_at, $format_date2) }}</div>
                    <div class="col-sm-6 col-xs-6 main-cell break">
                        @if ( $offer->project->client->isSuspended() )
                            {{ $offer->title }}
                            @if ( $offer->project->isSuspended() )
                            <span class="status-cancelled">[{{ trans('common.suspended') }}]</span>
                            @endif
                        @else
                        <a href="{{ route('job.apply_offer', ['id' => $offer->id]) }}">
                            {{ $offer->title }}
                            @if ( $offer->project->isSuspended() )
                            <span class="status-cancelled">[{{ trans('common.suspended') }}]</span>
                            @endif
                        </a>
                        @endif
                    </div>
                    <div class="col-sm-4 col-xs-3 username">{{ $offer->project->client->fullname() }}</div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <div class="page-content-section shadow-box">
        @if ( !count($job_offers) )
            {{ show_warnings() }}
        @endif
        <div class="title-section">
            <span class="title">
                <i class="icon-briefcase title-icon"></i>
                {{ trans('job.my_proposals') }}
            </span>
            <span class="connects hidden-mobile">{{ trans('job.available_connects') }}: <strong>{{ $current_user->stat->connects }}</strong></span>
        </div>

        <div class="tab-section">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="{{ $tab == 'active'?'active':''}}">
                    <a href="{{ route('job.my_proposals', ['tab' => 'active']) }}">{{ trans('common.active') }}</a>
                </li>
                <li role="presentation" class="{{ $tab == 'archived'?'active':''}}">
                    <a href="{{ route('job.my_proposals', ['tab' => 'archived']) }}">{{ trans('common.archived') }}</a>
                </li>
            </ul>
        </div>

        @if ($tab == 'active')
            @if ( !count($active_jobs) && !count($invite_jobs) && !count($my_proposals) )
            <div class="not-found-result">
                <div class="heading">{{ trans('job.no_active_proposals') }}</div>
            </div>
            @endif

            @if ( count($active_jobs) > 0 )
            <div id="active_jobs" class="active-proposal box-section">
                <div class="title-section">{{ trans('common.active_candidacies') }} ({{ count($active_jobs) }})</div>
                <div class="section-content">
                    <div class="box-header clearfix">
                        <div class="col-sm-2 col-xs-3">{{ trans('common.received') }}</div>
                        <div class="col-sm-6 col-xs-6">{{ trans('common.job') }}</div>
                        <div class="col-sm-4 col-xs-3">{{ trans('common.client') }}</div>
                    </div>
                    @foreach ( $active_jobs as $job )
                        @if ( $job->project )
                        <div class="box-row clearfix">
                            <div class="col-sm-2 col-xs-3 submitted-date">{{ custom_ago($job->messageThread ? $job->messageThread->message->created_at : $job->created_at, $format_date2) }}</div>
                            <div class="col-sm-6 col-xs-6 break">
                                @if ( $job->project->client->isSuspended() )
                                    <span class="main-cell">{{ $job->project->subject }}</span>
                                @else
                                <a href="{{ _route('job.application_detail', ['id' => $job->id]) }}">
                                    <span class="main-cell">{{ $job->project->subject }}</span>
                                </a>
                                @endif
                                @if ( $job->project->isSuspended() )
                                <span class="round-ribbon label-suspended">[{{ trans('common.suspended') }}]</span>
                                @endif
                            </div>
                            <div class="col-sm-4 col-xs-3 username">{{ $job->project->client->fullname() }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

    		@if ( count($invite_jobs) > 0 )        
            <div id="invitations" class="invitation box-section">
                <div class="title-section">{{ trans('common.invitations_to_interview') }} ({{ count($invite_jobs) }})</div>
                <div class="section-content">
                    <div class="box-header clearfix">
                        <div class="col-sm-2 col-xs-3">{{ trans('common.received') }}</div>
                        <div class="col-sm-6 col-xs-6">{{ trans('common.job') }}</div>
                        <div class="col-sm-4 col-xs-3">{{ trans('common.client') }}</div>
                    </div>                  
                    @foreach ( $invite_jobs as $invitation )
                        @if ( $invitation->project )
                        <div class="box-row clearfix">
                            <div class="col-sm-2 col-xs-3 submitted-date">{{ custom_ago($invitation->created_at, $format_date2) }}</div>
                            <div class="col-sm-6 col-xs-6 break">
                                @if ( $invitation->project->client->isSuspended() )
                                    <span class="main-cell">{{ $invitation->project->subject }}</span>
                                @else
                                <a href="{{ route('job.accept_invite', ['id' => $invitation->id]) }}">
                                    <span class="main-cell">{{ $invitation->project->subject }}</span>
                                </a>
                                @endif

                                @if ( $invitation->project->isSuspended() )
                                <span class="round-ribbon label-suspended">{{ trans('common.suspended') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-4 col-xs-3 username">{{ $invitation->project->client->fullname() }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if ( count($my_proposals) > 0 )
            <div id="proposals" class="my-applicants box-section">
                <div class="title-section">{{ trans('common.submitted_proposals') }} ({{ count($my_proposals) }})</div>
                <div class="section-content">
                    <div class="box-header clearfix">
                        <div class="col-sm-2 col-xs-3">{{ trans('common.submitted') }}</div>
                        <div class="col-sm-6 col-xs-6">{{ trans('common.job') }}</div>
                        <div class="col-sm-4 col-xs-3">{{ trans('common.client') }}</div>
                    </div>
                    @foreach ( $my_proposals as $job )
                        @if ( $job->project )
                        <div class="box-row clearfix">
                            <div class="col-sm-2 col-xs-3 submitted-date">{{ ago($job->created_at) }}</div>
                            <div class="col-sm-6 col-xs-6 break">
                                @if ( $job->project->client->isSuspended() )
                                    <span class="main-cell">{{ $job->project->subject }}</span>
                                @else
                                <a href="{{ _route('job.application_detail', ['id' => $job->id]) }}">
                                    <span class="main-cell">{{ $job->project->subject }}</span>
                                </a>
                                @endif
                                @if ( $job->project->isSuspended() )
                                <span class="round-ribbon label-suspended">{{ trans('common.suspended') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-4 col-xs-3 username">{{ $job->project->client->fullname() }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        @elseif ($tab == 'archived')
            @if ( count($archived_jobs) > 0 )
            <div class="archived-proposal box-section">
                <div class="section-content">
                    <div class="box-header clearfix">
                        <div class="col-sm-2 col-xs-3">{{ trans('common.date') }}</div>
                        <div class="col-sm-8 col-xs-6">{{ trans('common.job') }}</div>
                        <div class="col-sm-2 col-xs-3">{{ trans('common.reason') }}</div>
                    </div>
                    @foreach ( $archived_jobs as $proposal )
                        @if ( $proposal->project )
                        <div class="box-row clearfix">
                            <div class="col-sm-2 col-xs-3 date">{{ getFormattedDate($proposal->updated_at) }}</div>
                            <div class="col-sm-8 col-xs-6 main-cell"><a href="{{ _route('job.application_detail', ['id' => $proposal->id]) }}">{{ $proposal->project->subject }}</a></div>
                            <div class="col-sm-2 col-xs-3 status">
                                @if ( $proposal->isArchived() )
                                    {{ trans('common.archived') }}
                                @elseif ( $proposal->isDeclinedByBuyer() )
                                    {{ trans('common.declined_by_client') }}
                                @elseif ( $proposal->isDeclinedByFreelancer() )
                                    {{ trans('common.withdrawn_by_you') }}
                                @elseif ( $proposal->project->isClosed() )
                                    {{ trans('common.closed') }}
                                @elseif ( $proposal->isCancelled() )
                                    {{ trans('common.closed_by_customer_support') }}
                                @elseif ( $proposal->isExpired() )
                                    {{ trans('common.expired') }}
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach

                    <div class="row box-pagination">
                        <div class="col-sm-12 text-right">{!! $archived_jobs->render() !!}</div>
                    </div>
                </div>
            </div>
            @else
            <div class="not-found-result">
                <div class="heading">{{ trans('job.no_archived_proposals') }}</div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection