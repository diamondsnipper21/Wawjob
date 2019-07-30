<?php
/**
 * End contract and leave feedback (contract/@id/feedback)
 *
 * @author Ro Un Nam
 * @since Jun 05, 2017
 */

use iJobDesk\Models\Contract;
?>
@extends('layouts/default/index')

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="page-content-section">
			<div class="view-section">  

				@if ( $contract->fundedLastMilestone )
					<div class="alert alert-danger" role="alert">
						{{ trans('contract.this_contract_has_milestones_not_paid_yet') }}			
					</div>
				@endif

			  	<div class="title-section border-0 pt-4 pb-4">
					<span class="title break">
					@if ( $contract->isClosed() )
			      		{{ trans('page.contract.feedback.title') }}
			      	@else
			      		{{ trans('page.contract.end.title') }}
			      	@endif
			      	 - {{ $contract->title }}
			      	</span>
			    </div>

			    <div class="desc mb-4">{{ trans('contract.share_your_experience') }}</div>

			    <div class="feedback-section">
			 		<form class="feedback-form" id="form_feedback" method="post">
				 		<input type="hidden" name="_token" value="{{ csrf_token() }}">
				 			
						<div class="form-group pb-4">
						  	<label class="control-label">
						  		{{ trans('common.reason') }}
						  		<span class="form-required"> *</span>
						  	</label>
					  		<div class="w-30">
						  		<select name="reason" class="form-control select2">
						  			<option value="0">{{ trans('contract.completed_successfully') }}</option>
						  			<option value="1">{{ trans('common.no_responsive') }}</option>
						  			<option value="2">{{ trans('common.cancel') }}</option>
						  			@if ( $current_user->isBuyer() )
						  			<option value="3">{{ trans('contract.no_required_skills') }}</option>
						  			@else
						  			<option value="4">{{ trans('contract.requirement_changed') }}</option>
						  			@endif
						  		</select>
						  	</div>
						</div>

						@if ( abs($contract->totalPaid()) > 0 || ($current_user->isBuyer() && $contract->fundedLastMilestone) )
						<div class="form-group pb-4">
						  	<label class="control-label">
						  		{{ trans('common.rate') }}
						  		<span class="form-required"> *</span>
						  	</label>
					  		<div class="row">
					  			<div class="col-md-2">
					  				<div class="stars mt-1">
		                            	<input type="hidden" value="" id="review" name="review">
		                                <div class="review-default"></div>
		                                <div class="review-hover"></div>
		                                <div class="review-selected"></div>
		                            </div> 
					  			</div>
					  			<div class="col-md-2" id="mark">
					  				{{ trans('common.score') }}&nbsp;&nbsp;&nbsp;<span></span>
					  			</div>
					  		</div>
						</div>

						<div class="form-group pb-4">
						  	<label class="control-label">
						  		{{ trans('common.comment') }} 
						  		<span class="form-required"> *</span>
						  	</label>
						  	<div class="">
						    	<textarea class="form-control feedback-description maxlength-handler" name="feedback" rows="5" data-rule-required="true" maxlength="5000"></textarea>
						    </div>
					  	</div>
					  	@endif
					
						@if ( $contract->fundedLastMilestone )
						<div class="form-group pb-4">
							<div class="chk">
								<label class="pl-0">
									<input type="checkbox" name="confirm_fund" id="confirm_fund" value="1" data-rule-required="true">
									@if ( $current_user->isBuyer() )
										{{ trans('contract.confirm_release_fund_before_close') }}
									@else
										{{ trans('contract.confirm_refund_fund_before_close') }}
									@endif
								</label>
							</div>
						</div>
				  		@endif

					  	<div class="form-group pt-4">
	    					<button type="submit" class="btn btn-primary btn-submit">{{ trans('common.submit') }}</button>
	    					<a href="{{ _route('contract.contract_view', ['id' => $contract->id]) }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
		      			</div>

		      		</form>

				</div><!-- .feedback-section -->
			</div><!-- .view-section -->
		</div><!-- .page-content-section -->
	</div>
</div>
@endsection