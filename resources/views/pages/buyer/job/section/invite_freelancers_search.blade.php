<?php
/**
  * @author Ro Un Nam
 */
?>

<form class="form-horizontal" id="form_invite_freelancers" method="get" action="{{ _route('job.invite_freelancers', ['id' => $job->id]) }}">
	@include ('pages.search.section.box_filter')
</form>

@if ( $freelancers )
<div class="text-right">
    {!! $freelancers->render() !!}
</div>
@endif
            
<div class="users-section search-freelancers">
@if ( count($freelancers) )
	@foreach ($freelancers as $user)
		@include ('pages.buyer.job.section.invite_freelancers_user')
	@endforeach
@else
    <div class="not-found-result">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="heading">{{ trans('job.no_freelancers') }}</div>
            </div>
        </div>
    </div>
@endif
</div><!-- .users-section -->