<?php
/**
  * @author Ro Un Nam
 */
?>

<div class="users-section past-hires">
@if ( count($freelancers) )
	@foreach ($freelancers as $user)
		@include ('pages.buyer.job.section.invite_freelancers_user')
	@endforeach
@else
	<div class="not-found-result">
		<div class="row">
			<div class="col-md-12 text-center">
				<div class="heading">{{ trans('job.you_have_no_past_hires') }}</div>
				<p>{{ trans('job.after_you_have_hired_freelancer_they_will_show_up_here_you_can_easily_invite_them') }}</p>
			</div>
		</div>
	</div>
@endif
</div><!-- .users-section -->