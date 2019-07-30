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
		@if ( !$this_user->isSuspended() )
		<form id="formMilestones" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id]) }}#contract_milestones">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_action" />
			<input type="hidden" name="_id" />

			@if ( $contract->isOpen() )
			<div class="text-right">
				<a class="btn btn-primary btn-create-milestone invisible-in-admin" href="#modalMilestone" data-toggle="modal" data-backdrop="static"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('common.create_milestone') }}</a>
			</div>
			@endif
		@endif

		@if ( count($milestones) )
			<table class="table">
				<thead>
					<tr>
						<th>{{ trans('common.description') }}</th>
						<th width="12%">{{ trans('common.amount') }}</th>
						<th width="25%">{{ trans('common.status') }}</th>
						<th width="12%" class="hidden-mobile">{{ trans('common.due_date') }}</th>
						<th width="23%"></th>
					</tr>
				</thead>

				<tbody>
				@foreach ($milestones as $milestone)
					<tr class="milestone">
						<td class="td-title form-group">
							<span>{{ $milestone->name_string() }}</span>
						</td>
						<td class="form-group">
							<span>${{ formatCurrency($milestone->getPrice()) }}</span>
						</td>
						<td>
							<div class="status">{{ $milestone->fund_status_string() }}</div>
							{!! $milestone->status_date_string() !!}
						</td>
						<td class="col-calendar hidden-mobile">
							<span>{{ date_format(date_create($milestone->end_time), 'M j, Y') }}</span>
						</td>
						<td class="text-right action-buttons invisible-in-admin">
							@if ( $milestone->isEditable() || $milestone->isAvailableFund() || $milestone->isAvailableRelease() )
							<div class="col-xs-12 job-action">
								<a class="btn btn-link action-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
									<i class="icon-settings"></i> {{ trans('common.action') }} <i class="icon-arrow-down"></i>
								</a>

								<ul class="dropdown-menu">
									@if ( $milestone->isEditable() )
									<li>
										<a class="btn btn-link btn-edit" href="#modalMilestone" data-toggle="modal" data-backdrop="static" data-id="{{ $milestone->id }}" data-title="{{ $milestone->name }}" data-amount="{{ formatCurrency($milestone->price) }}" data-date="{{ date_format(date_create($milestone->end_time), 'm/d/Y') }}" {{ !$contract->isAvailableAction(true) ? 'disabled' : '' }}>{{ trans('common.edit') }}</a>
									</li>
									<li>
										<a class="btn btn-link btn-delete" data-id="{{ $milestone->id }}" {{ !$contract->isAvailableAction(true) ? 'disabled' : '' }}>{{ trans('common.delete') }}</a>
									</li>
									@endif
									@if ( $milestone->isAvailableFund() )
									<li>
										<a class="btn btn-link btn-fund" data-id="{{ $milestone->id }}" {{ !$contract->isAvailableAction(true) ? 'disabled' : '' }}>{{ trans('common.fund') }}</a>
									</li>
									@endif
									@if ( $milestone->isAvailableRelease() )
									<li>
										<a class="btn btn-link btn-release" data-id="{{ $milestone->id }}" {{ !$contract->isAvailableAction(true) ? 'disabled' : '' }}>{{ trans('common.release') }}</a>
									</li>
									@endif
								</ul>
							</div>
							@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@else
			<div class="not-found-result">
				<div class="row">
					<div class="col-md-12 text-center">
						<div class="heading">{{ trans('contract.this_contract_has_no_milestons') }}</div>
					</div>
				</div>
			</div>		
		@endif

		</form>

		@if ( !$this_user->isSuspended() )
			@include('pages.contract.modal.milestone')
		@endif
	</div><!-- .tab-inner -->
</div>