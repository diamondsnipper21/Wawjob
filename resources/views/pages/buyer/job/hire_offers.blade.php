<?php
/**
* Hire & Offers Page (job/{id}/hire-offers)
*
* @author Ro Un Nam
* @since May 30, 2017
*/
?>
@extends($current_user->isAdmin()?('layouts/admin/super'.(!empty($user_id)?'/user':'/job_detail')):'layouts/default/index')

@section('content')

{!! Breadcrumbs::render('job_posting', $job) !!}

<div class="shadow-box job-hire-offers-page">

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

		<div class="contracts-section" data-token="{{ csrf_token() }}" data-url="{{ _route('job.hire_offers', ['id' => $job->id, 'user_id' => $user_id]) }}">
			
			<div class="contracts">
			@if ( count($offers) || count($contracts) )
				<div class="row">
					@if ( count($offers) )
					<div class="col-md-6">
						<div class="sub-title p-3">
							<i class="icon-envelope-open"></i> {{ trans('common.offers') }}
						</div>
						<div class="box-section">
						@foreach ( $offers as $contract )
							@include('pages.buyer.job.section.job_contract')
						@endforeach
						</div>
					</div>
					@endif

					@if ( count($contracts) )
					<div class="col-md-6">
						<div class="sub-title p-3">
							<i class="icon-layers"></i> {{ trans('common.hires') }}
						</div>
						<div class="box-section">
						@foreach ( $contracts as $contract )
							@include('pages.buyer.job.section.job_contract')
						@endforeach
						</div>
					</div>
					@endif
				</div>
			@else
				<div class="not-found-result">
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="heading">{{ trans('job.you_have_no_hire_offers') }}</div>
						</div>
					</div>
				</div>
			@endif
			</div><!-- .contracts -->
		</div><!-- .contracts-section -->

		@if ( !$job->accept_term )
			@include('pages.buyer.job.section.accept_term')
		@endif
	</div><!-- .page-content-section -->
</div><!-- .page-content -->
@endsection