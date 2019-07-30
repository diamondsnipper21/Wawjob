<?php
/**
* @author Ro Un Nam
* @since Jun 09, 2017
*/

use iJobDesk\Models\TransactionLocal;
?>
<div class="summary-section section">
	<div class="section-title">
		{{ trans('common.summary') }} <span>({{ date($format_date2, strtotime($from)) }} - {{ date($format_date2, strtotime($to)) }})</span>
	</div>
	
	@if ( count($other_payments) || count($timesheets) )
	<div class="section-content">
		<table class="table">
			<thead>
				<tr>
					<th width="15%">{{ trans('common.client') }}</th>
					<th>{{ trans('common.contract_title') }}</th>
					<th width="15%">{{ trans('common.type') }}</th>
					<th width="15%" class="text-right">{{ trans('common.amount_billed') }}</th>
				</tr>
			</thead>
			<tbody>

				@foreach ($timesheets as $t)
				<tr>
					<td>
						{{ $t['client'] }}
					</td>
					<td>
						{{ $t['contract_title'] }}
					</td>
					<td>
						{{ trans('common.hourly') }}
					</td>
					<td class="text-right">
						{{ $t['amount'] >= 0 ? '$' . formatCurrency($t['amount']) : '($' . formatCurrency(abs($t['amount'])) . ')' }}
					</td>
				</tr>
				@endforeach

				@foreach ($other_payments as $t)
				<tr>
					<td>
						{{ $t->contract->buyer->fullname() }}
					</td>
					<td>
						{{ $t->contract->title }}
					</td>
					<td>
						{{ $t->type_string() }}
					</td>
					<td class="text-right">
					@if ( $t->isRefund() )
						(${{ formatCurrency($t->reference->amount) }})
					@else
						{{ $t->amount >= 0 ? '$' . formatCurrency(abs($t->reference->amount)) : '($' . formatCurrency(abs($t->reference->amount)) . ')' }}
					@endif
					</td>
				</tr>
				@endforeach
				<tr class="bg-white border-light-bottom">
					<td></td>
					<td></td>
					<td></td>
					<td class="text-right">
						<span class="mr-4">{{ trans('common.sub_total') }}</span>
						@if ( $total_other_payments + $total['amount'] > 0 )
							${{ formatCurrency($total_other_payments + $total['amount']) }}
						@else
							(${{ formatCurrency(abs($total_other_payments + $total['amount'])) }})
						@endif
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	@else
	<div class="not-found-result">
		<div class="heading">{{ trans('common.you_have_no_timelogs') }}</div>
	</div>
	@endif
</div><!-- .summary-section -->