<?php
/**
 * @author Ro Un Nam
 * @since Jun 04, 2017
 */

use iJobDesk\Models\Contract;
?>
<div id="contract_review" role="tabpanel" class="tab-pane">
	<div class="tab-inner">
	@if ( $contract->status == Contract::STATUS_CLOSED )
		<div class="box-feedback">
			@if ( $current_user->isBuyer() )
			<div class="row pb-5">
				<div class="col-md-3 col-sm-4 col-xs-6 text-right">
					<label class="control-label">{{ trans('contract.your_feedback_to_freelancer') }}</label>
				</div>
				<div class="col-md-9 col-sm-8 col-xs-6">
					<div class="mb-2 ml-3">
						<div class="stars" data-value="{{ $contract->feedback->buyer_score / 5 * 100 }}%" data-score="{{ $contract->feedback->buyer_score }}"></div>
					</div>
					<div class="review ml-3 pl-1 pb-2">{!! nl2br($contract->feedback->buyer_feedback) !!}</div>
				</div>
			</div>

			<div class="row pb-5">
				<div class="col-md-3 col-sm-4 text-right">
					<label class="control-label">{{ trans('contract.freelancer_feedback_to_you') }}</label>
				</div>
				<div class="col-md-9 col-sm-8">
					<div class="mb-2 ml-3">
						<div class="stars" data-value="{{ $contract->feedback->freelancer_score / 5 * 100 }}%" data-score="{{ $contract->feedback->freelancer_score }}"></div>
					</div>
					<div class="review ml-3 pl-1 pb-2">{!! nl2br($contract->feedback->freelancer_feedback) !!}</div>
				</div>
			</div>
			@else
			<div class="row pb-5">
				<div class="col-md-3 col-sm-4 col-xs-6 text-right">
					<label class="control-label">{{ $current_user->isAdmin() ? trans('contract.freelancer_feedback_to_client') : trans('contract.your_feedback_to_client') }}</label>
				</div>
				<div class="col-md-9 col-sm-8 col-xs-6">
					<div class="mb-2 ml-3">
						<div class="stars" data-value="{{ $contract->feedback->freelancer_score / 5 * 100 }}%" data-score="{{ $contract->feedback->freelancer_score }}"></div>
					</div>
					<div class="review ml-3 pl-1 pb-2">{!! nl2br($contract->feedback->freelancer_feedback) !!}</div>
				</div>
			</div>

			<div class="row pb-5">
				<div class="col-md-3 col-sm-4 col-xs-6 text-right">
					<label class="control-label">{{ $current_user->isAdmin() ? trans('contract.client_feedback_to_freelancer') : trans('contract.client_feedback_to_you') }}</label>
				</div>
				<div class="col-md-9 col-sm-8 col-xs-6">
					<div class="mb-2 ml-3">
						<div class="stars" data-value="{{ $contract->feedback->buyer_score / 5 * 100 }}%" data-score="{{ $contract->feedback->buyer_score }}"></div>
					</div>
					<div class="review ml-3 pl-1 pb-2">{!! nl2br($contract->feedback->buyer_feedback) !!}</div>
				</div>
			</div>
			@endif
		</div>
	@endif
	</div>
</div>