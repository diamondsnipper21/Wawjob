<?php
/**
* JobInfo Page (search/job)
*
* @author  - so gwang
*/

use iJobDesk\Models\Project;
use iJobDesk\Models\UserSavedProject;
?>

@extends('layouts/default/index')
@section('content')

<div class="page-content-section no-padding">
	<div class="title-section mb-4">
		<span class="title">
			<i class="icon-docs title-icon"></i>
			{{ trans('page.' . $page . '.title') }}
		</span>
	</div>

	<div id="saved_jobs" class="box-section">
		<form method="post">
		@if ( count($userSavedJobs) )
			<div class="search-section mb-4">
				<div class="row">
					<div class="col-sm-6">
						<label class="mt-2 mr-3 pull-left">{{ trans('common.sort_by') }}</label>
						<div class="pull-left w-25">
							<select class="form-control select2" name="sortCode" id="sortBySelect">
								<option value="posted_at" {{ $colValue == 'posted_at' ? 'selected' : '' }}>{{ trans('common.posted_date') }}</option>
								<option value="created_at" {{ $colValue == 'created_at' ? 'selected' : '' }}>{{ trans('common.saved_date') }}</option>
								<option value="subject" {{ $colValue == 'subject' ? 'selected' : '' }}>{{ trans('common.job_title') }}</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6 text-right" id="pagination_wrapper">{!! $userSavedJobs->render() !!}</div>
				</div>
			</div>

			@foreach($userSavedJobs as $userSavedJob)
			<div class="box-row">
				<div class="col-sm-7 main-cell break">
					<a href="{{ _route('job.view', ['id'=>$userSavedJob->job->id]) }}">{{ $userSavedJob->job->subject }}</a>
				</div>					
				<div class="col-sm-3 col-xs-9 posted-date">
					<div class="date">{{ Date('M d, Y', strtotime($userSavedJob->job->created_at)) }}</div>
					<div class="ago">{{ ago($userSavedJob->job->created_at) }}</div>
				</div>
				<div class="col-sm-2 col-xs-3 action">
					<a class="delete-button pull-right" href="{{ route('saved_jobs.destroy', ['id' => $userSavedJob->project_id]) }}"><i class="fa fa-times" aria-hidden="true"></i></a>
				</div>
			</div>
			@endforeach

			<div class="text-right">{!! $userSavedJobs->render() !!}</div>
		@else
	        <div class="not-found-result">
	            <div class="heading">{{ trans('job.no_saved_jobs') }}</div>
	        </div>			
		@endif
		</form>
	</div>
</div>
@endsection