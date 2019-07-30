<?php 
/**
 * Navigation for job posting
 *
 * @author Ro Un Nam
 * @since May 18, 2017
 */
use iJobDesk\Models\Project; 
?>

<div class="job-top-links default-boxshadow">
	<ul>
		<li>
			@if ( $page == 'buyer.job.overview' )
			<a class="nav-link active">
			@else
			<a class="nav-link" href="{{ _route('job.overview', ['id' => $job->id]) }}">
			@endif			
				{{ trans('common.overview') }}
				<span class="triangle hidden-mobile"></span>
			</a>
		</li>
		<li>
			@if ( $page == 'buyer.job.invite_freelancers' )
			<a class="nav-link active">
			@else
			<a class="nav-link" href="{{ _route('job.invite_freelancers', ['id' => $job->id]) }}">
			@endif
				{!! trans('common.invite_freelancers') !!}
				<span class="triangle hidden-mobile"></span>
			</a>
		</li>
		<li>
			@if ( $page == 'buyer.job.interviews' )
			<a class="nav-link active" data-count="{{ $counts['review_proposals'] }}">
			@else
			<a class="nav-link" href="{{ _route('job.interviews', ['id' => $job->id]) }}" data-count="{{ $counts['review_proposals'] }}">
			@endif
				{!! trans('common.review_proposals') !!} (<span class="total-proposals">{{ $counts['review_proposals'] }}</span>)
				<span class="triangle hidden-mobile"></span>
			</a>
		</li>
		<li>
			@if ( $page == 'buyer.job.hire_offers' )
			<a class="nav-link last active">
			@else
			<a class="nav-link last" href="{{ _route('job.hire_offers', ['id' => $job->id]) }}">
			@endif
				{{ trans('common.hire') }} ({{ $counts['hires'] }})<span class="hidden-mobile"> / {{ trans('common.offers') }} ({{ $counts['offers'] }})</span>
			</a>
		</li>
	</ul>
</div>