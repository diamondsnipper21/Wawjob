<?php
/**
* Job Search Page (search/job)
*
* @author  - so gwang
*/

use iJobDesk\Models\Project;
?>
@extends('layouts/default/index')

@section('content')
<div class="page-content-section search-jobs-page no-padding">

    <form id="search_form" method="get" role="form" action="" data-saved-job-create="{{ route('saved_jobs.create') }}" data-saved-job-destroy="{{ route('saved_jobs.destroy') }}">
        <div class="row">
            <div class="col-sm-3">
            	<div class="default-boxshadow bg-white search-job-left">
			        @include ('pages.search.section.box_filter_job_sidebar')
	        	</div>
            </div>

            <div class="col-sm-9">
            	<div class="default-boxshadow bg-white search-job-right">
				    <div class="title-section border-0 pb-4">
				        <div class="row">
				            <div class="col-sm-6 col-xs-9">
				                <span class="title">{{ trans('page.' . $page . '.title') }}</span>
				            </div>

				            <div class="col-sm-6 col-xs-3">
				                <a class="btn btn-link btn-rss pull-right mt-2" href="{{ route('search.rssjob') . ($filteredParams ? '?' . $filteredParams : '') }}" target="_blank"><i class="icon-feed mr-1" aria-hidden="true"></i>RSS</a>
				            </div>
				        </div>
				    </div>

				    {{ show_messages() }}

			        <div class="row">
			        	<div class="col-sm-9">
				            <div class="input-group">
				                <input id="search_title" name="q" class="form-control" type="text" placeholder="{{ trans('search.search_jobs') }}" value="{{ old('q') }}" />
				                <span class="input-group-btn">
				                    <input type="submit" id="search_btn" class="btn btn-primary" value="{{ trans('common.search') }}"></input>           
				                </span>
				            </div>
				        </div>
			        </div>

					<div class="row pt-4 pb-2">
					    <div class="col-md-6 col-sm-8">
					        {!! render_pagination_desc('common.showing_of_results', $jobs) !!}
					    </div>
					    <!-- <div class="col-md-6">
					        <div class="w-25 pull-right">
					            <select class="form-control select2" name="s" id="sort">
					                @foreach ($sorts as $key => $sort)
					                <option value="{{ $key }}" {{ $key == old('s') ? 'selected' : '' }}>{{ $sort }}</option>
					                @endforeach
					            </select>
					        </div>
					        <label class="pull-right mt-2 mr-3">{{ trans('common.sort_by') }}</label>
					    </div> -->
					</div>

			        <div class="pt-4 border-top" id="job_list">
		            	@include ('pages.search.jobList')
		            </div>

		            <div class="text-right">
			            {!! $jobs->appends(Request::input())->render() !!}
			        </div>
		        </div>
            </div><!-- .col-md-9 -->
        </div><!-- .row -->
    </form>
</div>
@endsection