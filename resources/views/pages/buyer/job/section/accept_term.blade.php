<?php
/**
* Show the accept term dialog
*
* @author Ro Un Nam
* @since May 21, 2017
*/
use iJobDesk\Models\Project;
?>

@if (!$current_user->isAdmin())
<div class="modal fade" id="modalJobTerm" tabindex="-1" role="dialog" aria-labelledby="">
	@if ( !empty($new_project) )
	<div class="alert alert-success fade in" id="alertJobLive">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<i class="fa fa-check"></i>
		<p><strong>{{ trans('job.your_job_is_live') }}</strong></p>
		<p>{{ trans('job.please_invite_top_candidates') }}</p>
	</div>
	@endif
	
	<div class="modal-dialog" role="slot">
		<div class="modal-content">
			@if ( $page == 'buyer.job.interviews' )
			<form id="formJobTerm" class="form-horizontal" method="post" action="{{ _route('job.interviews', ['id' => $job->id]) }}">
			@elseif ( $page == 'buyer.job.hire_offers' )
			<form id="formJobTerm" class="form-horizontal" method="post" action="{{ _route('job.hire_offers', ['id' => $job->id]) }}">
			@else
			<form id="formJobTerm" class="form-horizontal" method="post" action="{{ _route('job.invite_freelancers', ['id' => $job->id]) }}">
			@endif

				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<div class="modal-header">
					<h4 class="modal-title">{{ trans('job.safty_notification') }}</h4>
				</div>

				<div class="modal-body">
					<ul>
						<li>
							<label><i class="fa fa-angle-right"></i> {{ trans('job.only_pay_freelances_ensure_payment_protection') }}</label>
						</li>
						<li>
							<label><i class="fa fa-angle-right"></i> {!! trans('job.paying_outside_violates') !!}</label>
						</li>
					</ul>
					<div class="checkbox-item mt-4">
						<div class="chk">
							<label>
								<input type="checkbox" name="job_accept_term" id="job_accept_term" value="1" data-rule-required="true">{!! trans('job.understand_term') !!}
							</label>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ trans('common.ok') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif