<?php
/**
 * @author  - nada
 */
use iJobDesk\Models\Project;
?>

<div class="view-section job-content-section">
    <div class="section mb-4 clearfix">
	    <div class="job-category rounded-item pull-left">&nbsp;&nbsp;{{ parse_multilang($job->category->name) }}</div>
        <div class="past-time pull-left">{{ trans('common.posted' )}} {{ ago($job->created_at) }}</div>
    </div>
	<div class="section mb-4 clearfix">
		<div class="project-type-info">
			@if ( $job->isHourly() )
			<div class="mb-2"><strong>{{ trans('common.hourly_job') }}</strong></div>
			<div class="ml-3">
				<div class="clearfix mb-2">
					<div class="name">{{ trans('common.hourly_rate') }}</div>
					<span class="info">{{ $job->affordable_rate_string() }}</span>
				</div>
				<div class="clearfix mb-2">
					<div class="name">{{ trans('common.workload') }}</div>
					<span class="info">{{ $job->workload_string() }}</span>
				</div>
				<div class="clearfix">
					<div class="name">{{ trans('common.project_length') }}</div>
					<span class="info">{{ $job->duration_string() }}</span>
				</div>
			</div>
			@else
			<div class="mb-2"><strong>{{ trans('common.fixed_price_job') }}</strong></div>
			<div class="ml-3">
				<div class="clearfix">
					<div class="name">{{ trans('common.budget') }}</div>
					<span class="info">{{ $job->price_string(true) }}</span>
				</div>
			</div>
			@endif
		</div>
	</div>
	<div class="section clearfix">
		<div class="sub-section">
			<div class="break margin-bottom-30">
				<div class="skill-label mb-2"><strong>{{ trans('common.description') }}</strong></div>
				{!! nl2br(str_limit($job->desc, 200)) !!} &nbsp;&nbsp;
				<a href="{{ _route('job.view', ['id'=>$job->id]) }}" target="_blank">{{ trans('common.view_original_job_posting') }}</a>
			</div>

		    @if (count($job->files) != 0)
		    <div class="margin-bottom-10 clearfix">
		        <div class="title">{{ trans('common.attachments') }}</div>
		        {!! render_files($job->files) !!}
		    </div>
		    @endif

		    <!-- <div class="project-term margin-bottom-10 clearfix">
		        <div class="term-label pull-left"><strong>{{ trans('common.project_type') }}:</strong></div>
		        <div class="term pull-left">
		            {{ $job->term_string() }}
		        </div>
		    </div> -->

		    @if ( count($job->skills) )
		    <div class="project-skills margin-bottom-20 clearfix">
		        <div class="title margin-bottom-10">
		            <strong>{{ trans('common.required_skills') }}</strong>
		        </div>
		        <div>
		            @foreach ( $job->skills as $skill )
		            <span class="rounded-item">{{ parse_multilang($skill->name) }}</span>
				@endforeach
			</div>
		    </div>
			@endif
		</div>
	</div>
</div><!-- END OF .job-content-section -->