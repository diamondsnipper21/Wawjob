<?php
/**
  * @author Ro Un Nam
 */
?>

<div class="users-section saved-freelancers">
@if ( count($freelancers) )
	@foreach ($freelancers as $user)
		@include ('pages.buyer.job.section.invite_freelancers_user')
	@endforeach
@else
	<div class="not-found-result">
		<div class="row">
			<div class="col-md-12 text-center">
				<div class="heading">{{ trans('job.you_have_no_saved_freelancers') }}</div>
				<p>{{ trans('job.after_you_save_freelancer_you_like_they_will_show_up_here_you_can_easily_invite_them') }}</p>
			</div>
		</div>
	</div>
@endif
</div><!-- .users-section -->