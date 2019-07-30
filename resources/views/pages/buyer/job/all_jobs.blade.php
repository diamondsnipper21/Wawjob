<?php
/**
 * My Info Page (job/my-jobs)
 *
 * @author  - nada
 */

use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
?>
@extends('layouts/default/index')

@section('content')
<div class="page-content-section no-padding buyer-jobs-page {{ count($offers) > 0?'exist-offers':'' }}">
	{{ show_messages() }}

    <!-- Offers -->
    @if (count($offers) > 0 )
	    <div id="offers" class="shadow-box">
	    	{{ show_warnings() }}

		    <div class="title-section">
				<i class="icon-envelope-open title-icon"></i>
				<span class="title">{{ trans('common.offers_sent') }}</span>
				@if ( !$current_user->isSuspended() )
				<div class="right-action-link pull-right">
					<a href="{{ route('job.create') }}" class="btn btn-primary {{ !$current_user->isAvailableAction() ? 'disabled' : '' }}">{{ trans('common.post_job') }}</a>
				</div>
				@endif
			</div>
			<div class="box-section">
		        <form method="post"></form>
		        @include ('pages.buyer.job.section.offers')
			</div>
		</div>
    @endif

	<div id="job_postings" class="shadow-box mb-0">
		@if ( !count($offers) )
			{{ show_warnings() }}
		@endif
		<div class="title-section">
			<i class="icon-docs title-icon"></i>
			<span class="title">{{ trans('common.job_postings') }}</span>
			@if (!$current_user->isSuspended() )
			<div class="right-action-link pull-right">
				<a href="{{ route('job.create') }}" class="btn btn-primary {{ !$current_user->isAvailableAction() ? 'disabled' : '' }}">{{ trans('common.post_job') }}</a>
			</div>
			@endif
		</div>
		
		<form method="post"></form>
		<ul class="nav nav-tabs">
			@if ( $type == 'open' )
            <li class="active"><a class="tab">{{ trans('common.open') }}</a></li>
            @else
            <li><a href="{{ route('job.all_jobs') }}" class="tab">{{ trans('common.open') }}</a></li>
            @endif
            
            @if ( $type == 'draft' )
            <li class="active"><a class="tab">{{ trans('common.draft') }}</a></li>
            @else
            <li><a href="{{ route('job.all_jobs', ['type' => 'draft']) }}" class="tab">{{ trans('common.draft') }}</a></li>
            @endif

            @if ( $type == 'archived' )
            <li class="active"><a class="tab">{{ trans('common.archived') }}</a></li>
            @else
            <li><a href="{{ route('job.all_jobs', ['type' => 'archived']) }}" class="tab">{{ trans('common.archived') }}</a></li>
            @endif
        </ul>

        {{ show_messages() }}
        <div class="tab-content-{{ $type }}">
			@include('pages.buyer.job.section.all_job_posting')
		</div>
	</div>

    <!-- All Contracts -->
    <div id="open_contracts" class="hide">
        <form method="post">
            <div class="title-section">
                <i class="icon-layers title-icon"></i>
                <span class="title">{{ trans('common.contracts') }}</span>
                <span class="pull-right mt-3"><a class="more-link" href="{{ route('contract.all_contracts') }}">{{ trans('job.view_all_contracts') }}</a></span>        
            </div>
        </form>
        @include ('pages.buyer.job.section.open_contracts')
    </div>
</div>
@endsection