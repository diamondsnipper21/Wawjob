<?php
/**
* @author Ro Un Nam
* @since May 30, 2017
*/
?>

<div class="proposals archived-proposals">
	@if ( count($proposals) )
		@foreach ($proposals as $proposal)
			@include ('pages.buyer.job.section.job_proposal')
		@endforeach
	@else
	<div class="not-found-result">
		<div class="row">
			<div class="col-md-12 text-center">
				<div class="heading">
					@if ( $show == 'interviewing' )
						{{ trans('job.no_interviews') }}
					@elseif ( $show == 'shortlisted' )
						{{ trans('job.no_shortlisted_proposals') }}
					@else
						{{ trans('job.no_archived_proposals') }}
					@endif
				</div>
			</div>
		</div>
	</div>
	@endif
</div><!-- .proposals-section -->