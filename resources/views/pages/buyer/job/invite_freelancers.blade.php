<?php
/**
* Invite Freelancers Page (job/{id}/invite-freelancers)
*
* @author Ro Un Nam
* @since May 19, 2017
*/

use iJobDesk\Models\Project;
?>
@extends($current_user->isAdmin()?('layouts/admin/super'.(!empty($user_id)?'/user':'/job_detail')):'layouts/default/index')

@section('additional-css')
<link rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">
@endsection

@section('content')

{!! Breadcrumbs::render('job_posting', $job) !!}

<div class="shadow-box job-invite-freelancers-page">

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

		<div class="tab-section">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="{{ $sub_page == '' ? 'active' : '' }}">
					<a href="{{ _route('job.invite_freelancers', ['id' => $job->id]) }}">{{ trans('common.search') }}</a>
				</li>
				<li role="presentation" class="{{ $sub_page == 'past' ? 'active' : '' }}">
					<a href="{{ _route('job.invite_freelancers_page', ['id' => $job->id, 'page' => 'past']) }}">{!! trans('common.past_hires') !!}</a>
				</li>
				<li role="presentation" class="{{ $sub_page == 'saved' ? 'active' : '' }}">
					<a href="{{ _route('job.invite_freelancers_page', ['id' => $job->id, 'page' => 'saved']) }}">{!! trans('common.saved_freelancers') !!}</a>
				</li>
				<li role="presentation" class="{{ $sub_page == 'invited' ? 'active' : '' }}">
					<a href="{{ _route('job.invite_freelancers_page', ['id' => $job->id, 'page' => 'invited']) }}">{!! trans('common.invited_freelancers') !!} (<span class="total-invited">{{ $total_invited_freelancers }}</span>)</a>
				</li>
			</ul>
		</div><!-- .tab-section -->

		<div class="invite-section">

			@if ( $sub_page == '' )
				@include('pages.buyer.job.section.invite_freelancers_search')
			@elseif ( $sub_page == 'past' )
				@include('pages.buyer.job.section.invite_freelancers_past')
			@elseif ( $sub_page == 'saved' )
				@include('pages.buyer.job.section.invite_freelancers_saved')
			@elseif ( $sub_page == 'invited' )
				@include('pages.buyer.job.section.invite_freelancers_invited')
			@endif

			@if ( $freelancers )
			<div class="text-right">
				{!! $freelancers->render() !!}
			</div>
			@endif
		</div><!-- .invite-section -->

		@if ( !$job->accept_term )
			@include('pages.buyer.job.section.accept_term')
		@endif
	</div><!-- .page-content-section -->
</div><!-- .page-content -->
@endsection