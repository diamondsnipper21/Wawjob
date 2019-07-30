<?php
/**
 * @author Ro Un Nam
 * @since Jun 03, 2017
 */

use iJobDesk\Models\Contract;
use iJobDesk\Models\ContractMilestone;
?>
<div id="contract_milestones" role="tabpanel" class="tab-pane">
	<div class="tab-inner">
		@if ( count($milestones) )
			@if ( !$this_user->isSuspended() )
			<form id="formMilestones" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id]) }}#contract_milestones">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_action" />
				<input type="hidden" name="_id" />
			@endif
			
			<table class="table">
				<thead>
					<th>{{ trans('common.description') }}</th>
					<th width="12%">{{ trans('common.amount') }}</th>
					<th width="25%">{{ trans('common.status') }}</th>
					<th width="12%" class="hidden-mobile">{{ trans('common.due_date') }}</th>
					<th width="10%"></th>
					<th width="10%"></th>
				</thead>
				<tbody>
					@foreach ( $milestones as $milestone)					
					<tr>
						<td>{{ $milestone->name_string() }}</td>
						<td>${{ formatCurrency($milestone->getPrice()) }}</td>
						<td>
							<div class="status">{{ $milestone->fund_status_string() }}</div>
							{!! $milestone->status_date_string() !!}
						</td>
						<td class="hidden-mobile">{{ date_format(date_create($milestone->end_time), 'M j, Y') }}</td>
						<td class="text-right action-buttons">
							@if ( $milestone->isFunded() )
							<button type="button" class="btn btn-danger btn-refund-fund" data-id="{{ $milestone->id }}" {{ !$contract->isAvailableAction(true) ? 'disabled' : '' }}>{{ trans('common.refund') }}</button>
							@endif
						</td>
						<td class="text-right action-buttons">
							@if ( $milestone->checkRequestPaymentButton() )
								<button type="button" class="btn btn-primary btn-request-payment" data-id="{{ $milestone->id }}" {{ !$contract->isAvailableAction() ? 'disabled' : '' }}>{{ trans('common.request_payment') }}</button>
							@endif
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			@if ( !$this_user->isSuspended() )
			</form>
			@endif
		@else
		<div class="not-found-result">
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="heading">{{ trans('contract.this_contract_has_no_milestons') }}</div>
				</div>
			</div>
		</div>
		@endif
	</div><!-- .tab-inner -->
</div>