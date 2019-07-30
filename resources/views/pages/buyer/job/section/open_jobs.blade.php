<?php
/**
* @author KCG
*/
use iJobDesk\Models\Project;
?>   
@if ( count($open_jobs) == 0 )
<div class="not-found-result">
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="heading">
                @if ($type == 'open')
                {{ trans('job.no_open_jobs') }}
                @elseif ($type == 'draft')
                {{ trans('job.no_draft_jobs') }}
                @elseif ($type == 'archived')
                {{ trans('job.no_archived_jobs') }}
                @endif
            </div>
        </div>
    </div>
</div>    
@else
<div class="row box-header">
    <div class="col-md-5 col-sm-3 col-xs-12">
        @if ($type == 'open')
            {!! render_pagination_desc('common.showing_of_job_postings', $open_jobs) !!}
        @elseif ($type == 'draft')
            {!! render_pagination_desc('common.showing_of_drafts', $open_jobs) !!}
        @elseif ($type == 'archived')
            {!! render_pagination_desc('common.showing_of_archived', $open_jobs) !!}
        @endif
    </div>
    <div class="col-md-5 col-sm-6 col-xs-12 hidden-mobile">
        @if ( $type != 'draft' )
        <div class="col-sm-3 text-center">{{ trans('common.applicants') }}</div>
        <div class="col-sm-3 text-center">{{ trans('common.interviews') }}</div>
        <div class="col-sm-3 text-center">{{ trans('common.offers_hires') }}</div>
        <div class="col-sm-3 text-center">{{ trans('common.visibility') }}</div>
        @endif
    </div>
    <div class="col-md-2 col-sm-3 col-xs-12"></div>
</div>
@foreach ($open_jobs as $job)
<div class="box-row object-item{{ $job->isClosed() ? ' closed' : '' }}{{ $job->isCancelled() ? ' cancelled' : '' }}">
    <div class="col-md-5 col-sm-3 col-xs-7">
        <div class="job-title break">
            @if ( $job->isDraft() )
            <a href="{{ _route('job.edit', ['id' => $job->id]) }}">{{ $job->subject??'(' . trans('common.no_title') . ')' }}</a>
            @else
            <a href="{{ _route('job.overview', ['id' => $job->id]) }}">{{ $job->subject }}</a>
            @endif

			@if ( $job->isFeatured() )
			<span class="label-featured round-ribbon">{{ trans('common.featured') }}</span>
			@endif

			@if ( $job->isSuspended() )
            <span class="label-suspended round-ribbon">{{ trans('common.suspended') }}</span>
            @elseif ( $job->isClosed() )
            <span class="label-closed round-ribbon">{{ trans('common.closed') }}</span>
            @elseif ( $job->isCancelled() )
            <span class="label-cancelled round-ribbon">{{ trans('common.cancelled') }}</span>
            @endif

            @if ( $job->isPrivate() )
			<i class="icon-lock ml-2 fs-16" data-toggle="tooltip" title="{{ trans('job.only_freelancers_invited_can_find_this_job') }}"></i>
			@elseif ( $job->isProtected() )
			<i class="icon-shield ml-2 fs-16" data-toggle="tooltip" title="{{ trans('job.only_ijobdesk_users_can_find_this_job') }}"></i>
			@endif

            @if ( $job->isDraft() )
            <div class="job-type">{{ $job->type_string() }} - {{ trans('common.created') }} {{ ago($job->updated_at) }}</div>
            @else
            <div class="job-type">{{ $job->type_string() }} - {{ trans('common.posted') }} {{ ago($job->created_at) }}</div>
            @endif

            @if ( $job->isClosed() )
                <div class="job-status">{{ trans('common.closed') }} - {{ $job->cancelled_at ? getFormattedDate($job->cancelled_at, 'M d, Y') : getFormattedDate($job->updated_at, 'M d, Y') }}</div>
            @elseif ( $job->isCancelled() )
                <div class="job-status">{{ trans('common.cancelled') }} - {{ $job->cancelled_at ? getFormattedDate($job->cancelled_at, 'M d, Y') : getFormattedDate($job->updated_at, 'M d, Y') }}</div>
            @endif
        </div>
    </div><!-- .col-sm-5 -->

    <?php
    $totalProposalsCount = $job->totalProposalsCount();
    $totalInterviewsCount = $job->totalInterviewsCount();
    $offerHiredContractsCount = $job->offerHiredContractsCount();
    ?>

    <div class="col-md-5 col-sm-6 col-xs-12 hidden-mobile">
        @if ( $type != 'draft' )
        <div class="job-info col-sm-3">
            <div class="job-info-value">
                {{ $totalProposalsCount > 0 ? $totalProposalsCount : '-' }}
            </div>
        </div>
        <div class="job-info col-sm-3">
            <div class="job-info-value">
                {{ $totalInterviewsCount > 0 ? $totalInterviewsCount : '-' }}
            </div>
        </div>
        <div class="job-info col-sm-3">
            <div class="job-info-value">
                {{ $offerHiredContractsCount > 0 ? $offerHiredContractsCount : '-' }}
            </div>
        </div>
        <div class="job-info col-sm-3">
            <div class="job-info-value status">
                {{ $job->is_public_string() }}
            </div>
        </div>
        @endif
    </div><!-- .col-sm-5 -->

    @if ( $type == 'archived' && !$current_user->isSuspended() && $job->status != Project::STATUS_SUSPENDED )
    <div class="job-info col-md-2 col-sm-3 col-xs-5 job-action">
        <a class="btn btn-link action-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
            <i class="icon-settings"></i> {{ trans('common.action') }} <i class="icon-arrow-down"></i>
        </a>

        <ul class="dropdown-menu">
            <li>
                <a class="btn btn-link" data-status="view" href="{{ _route('job.edit.repost', ['id' => $job->id, 'action'=>'repost']) }}">
                    <i class="icon-note"></i> {{ trans('common.repost') }}
                </a>
            </li>
        </ul>
    </div><!-- .col-sm-2 -->
    @elseif ( $type != 'archived' && !$current_user->isSuspended() )
    <div class="job-info col-md-2 col-sm-3 col-xs-5 job-action">
        <a class="btn btn-link action-link dropdown-toggle {{ $job->status == Project::STATUS_SUSPENDED ? 'disabled' : '' }}" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
            <i class="icon-settings"></i> {{ trans('common.action') }} <i class="icon-arrow-down"></i>
        </a>

        <ul class="dropdown-menu">
            @if ( $type != 'draft' )
            <li>
                <a class="btn btn-link" data-status="view" href="{{ _route('job.overview', ['id' => $job->id]) }}">
                    <i class="icon-link"></i> {{ trans('common.view') }}
                </a>
            </li>
            @endif
            <li>
                <a class="btn btn-link" data-status="edit" href="{{ _route('job.edit', ['id' => $job->id]) }}">
                    <i class="icon-pencil"></i> {{ trans('common.edit') }}
                </a>
            </li>
            @if ( $type != 'draft' )
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
                <a class="btn btn-link visibility" data-status="visibility" href="#">
                    <i class="icon-eye"></i> {{ trans('common.visibility') }} &nbsp;<i class="icon-arrow-right"></i>
                </a>
                <ul class="visibility-status">
                    <li>
                        <a class="btn btn-link {{ $job->is_public == Project::STATUS_PUBLIC ? 'disabled' : '' }}" data-status="public" data-url="{{ _route('job.change_public.ajax', ['id' => $job->id, 'public'=>Project::STATUS_PUBLIC]) }}" title="Visible to Everybody">
                            <i class="icon-share-alt"></i> {{ trans('common.public') }}
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-link {{ $job->is_public == Project::STATUS_PROTECTED ? 'disabled' : '' }}" data-status="protected" data-url="{{ _route('job.change_public.ajax', ['id' => $job->id, 'public'=>Project::STATUS_PROTECTED]) }}" title="Visible only to {{ config('app.name') }} Freelancers">
                            <i class="icon-shield"></i> {{ trans('common.protected') }}
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-link {{ $job->is_public == Project::STATUS_PRIVATE ? 'disabled' : '' }}" data-status="private" data-url="{{ _route('job.change_public.ajax', ['id' => $job->id, 'public' => Project::STATUS_PRIVATE]) }}" title="Visible only to {{ config('app.name') }} Invited Freelancers">
                            <i class="icon-lock"></i> {{ trans('common.private') }}
                        </a>
                    </li>
                </ul>
            </li>
            @else
            <li>
                <a class="btn btn-link" data-status="delete" data-draft="{{ $job->isDraft() ? '1' : '0' }}" data-url="{{ _route('job.change_status.ajax', ['id' => $job->id, 'status'=>Project::STATUS_DELETED]) }}">
                    <i class="icon-trash"></i> {{ trans('common.delete') }}
                </a>
            </li>
            @endif
        </ul>
    </div><!-- .col-sm-2 -->
    @endif
</div>
@endforeach
@endif

@if ( count($open_jobs) > 0 )
<div class="row row-pagination box-pagination">
    <div class="col-md-6">
        @if ($type == 'open')
            {!! render_pagination_desc('common.showing_of_job_postings', $open_jobs) !!}
        @elseif ($type == 'draft')
            {!! render_pagination_desc('common.showing_of_drafts', $open_jobs) !!}
        @elseif ($type == 'archived')
            {!! render_pagination_desc('common.showing_of_archived', $open_jobs) !!}
        @endif
    </div>
    <div class="col-md-6 text-right">
        {!! $open_jobs->render() !!}
    </div>
</div>
@endif