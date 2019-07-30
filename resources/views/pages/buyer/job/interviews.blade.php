<?php
/**
* Interviews Page (job/{id}/interviews)
*
* @author Ro Un Nam
* @since May 30, 2017
*/

use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Project;
use iJobDesk\Models\File;

?>
@extends($current_user->isAdmin()?('layouts/admin/super'.(!empty($user_id)?'/user':'/job_detail')):'layouts/default/index')

@section('content')

{!! Breadcrumbs::render('job_posting', $job) !!}

<div class="shadow-box job-interviews-page">

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
					<a href="{{ $url_active }}">{{ trans('common.active') }} (<span class="total-active">{{ $count_active }}</span>)</a>
				</li>
				<li role="presentation" class="{{ $sub_page == 'archived' ? 'active' : '' }}">
					<a href="{{ $url_archived }}">{{ trans('common.archived') }} (<span class="total-archived">{{ $count_archived }}</span>)</a>
				</li>
			</ul>
		</div><!-- .tab-section -->

		<div class="proposals-filter">
			<form class="form-horizontal form-filter" method="post" action="{{ _route('job.interviews_page', ['id' => $job->id, 'page' => $sub_page, 'user_id' => $user_id]) }}">
	  			<input type="hidden" name="_token" value="{{ csrf_token() }}">		
				<div class="row">
					<div class="col-md-6"></div>
					<div class="col-md-6 pt-1 pb-1 bg-gray">
						<div class="row">
							<div class="col-xs-6">
								<span class="pull-left w-25 mt-2">{{ trans('common.show') }}</span>
								<div class="pull-right w-75">
									<select class="form-control select2" id="show" name="show">
										<option value="" {{ $show == '' ? 'selected' : '' }}>- {{ trans('common.all') }} -</option>
										<option value="shortlisted" {{ $show == 'shortlisted' ? 'selected' : '' }}>{{ trans('common.shortlisted') }}</option>
										<option value="interviewing" {{ $show == 'interviewing' ? 'selected' : '' }}>{{ trans('common.interviewing') }}</option>
									</select>
								</div>
							</div>
							<div class="col-xs-6">
								<span class="pull-left w-25 mt-2">{{ trans('common.sort_by') }}</span>
								<div class="pull-right w-75">
									<select class="form-control select2" id="sort" name="sort">
										<option value="" {{ $sort == '' ? 'selected' : '' }}>{{ trans('common.newest_first') }}</option>
										<option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>{{ trans('common.oldest_first') }}</option>
										<option value="lowest_price" {{ $sort == 'lowest_price' ? 'selected' : '' }}>{{ trans('common.lowest_price_first') }}</option>
										<option value="best_match" {{ $sort == 'best_match' ? 'selected' : '' }}>{{ trans('common.best_match') }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div><!-- .proposals-filter -->

		<div class="proposals-section{{ $show == 'shortlisted' ? ' shortlisted' : ''}}" data-token="{{ csrf_token() }}" data-url="{{ _route('job.interviews', ['id' => $job->id, 'user_id' => $user_id]) }}">
			@if ( $sub_page == 'archived' )
				@include('pages.buyer.job.section.interviews_archived')
			@else
				@include('pages.buyer.job.section.interviews_active')
			@endif

			@if ( $proposals )
			<div class="text-right">
				{!! $proposals->render() !!}
			</div>
			@endif

			@if ( $job->status != Project::STATUS_SUSPENDED )
			<div class="modal fade" id="messagesModal" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<form class="form-horizontal form-message" method="post" action="{{ _route('job.interviews', ['id' => $job->id, 'user_id' => $user_id]) }}">
	  						<input type="hidden" name="_token" value="{{ csrf_token() }}">
	  						<input type="hidden" name="action" value="send_message">
	  						
	  						<input type="hidden" name="id" value="">

							<div class="modal-header">
								<div class="row">
									<div class="col-md-1">
										<label>{{ trans('common.to') }}</label>
									</div>
									<div class="col-md-10">
										<div class="user-info">
										</div>
									</div>
									<div class="col-md-1">
										<button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('common.close') }}"><span aria-hidden="true">&times;</span></button>
									</div>
								</div>
							</div>
							<div class="modal-body">
		  						<div class="box-message">
			  						<div class="box-ctrl">
			  							<textarea name="message" placeholder="{{ trans('job.type_messages') }}" class="form-control maxlength-handler" maxlength="5000"></textarea>
			  						</div>
			  						<div class="box-files">
				  						<div class="row">
											<div class="col-md-6">
												{!! render_file_element(File::TYPE_MESSAGE) !!}
											</div>
											<div class="col-md-6 text-right padding-top-10">
												<button type="button" class="btn btn-primary btn-submit-message">{{ trans('common.send') }}</button>
												<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('common.close') }}</button>
											</div>
										</div>
									</div>
								</div><!-- .box-message -->
							</div><!-- .modal-body -->
						</form>
					</div><!-- .modal-content -->
				</div>
			</div><!-- .modal -->
			@endif
		</div><!-- .proposals-section -->

		@if ( !$job->accept_term )
			@include('pages.buyer.job.section.accept_term')
		@endif
	</div><!-- .page-content-section -->
</div><!-- .page-content -->
@endsection