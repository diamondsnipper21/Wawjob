<?php
/**
* @author Ro Un Nam
* @since Jun 08, 2017
*/
?>

<div class="section section-in-review">
	@if ( count($hourly_in_review) )
	<div class="section-content">
		<table class="table">
			<thead>
				<tr>
					<th width="10%">{{ trans('common.date') }}</th>					
					<th class="text-left" width="20%">{{ trans('common.client') }}</th>
					<th class="text-left">{{ trans('common.contract') }}</th>
					<th width="15%">{{ trans('common.amount') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $hourly_in_review as $r )
				<tr>
					<td>{{ format_date('M d, Y', $r->created_at) }}</td>
					<td class="text-left">{{ $r->contract->buyer->fullname() }}</td>
					<td class="text-left">
						<a href="{{ _route('contract.contract_view', ['id' => $r->contract_id]) }}">{{ $r->contract->title }}</a>
					</td>
					<td>${{ formatCurrency($r->amount) }}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="4" class="text-right">
						<strong>${{ formatCurrency($total_in_review) }}</strong>
					</td>
				</tr>				
			</tbody>
		</table>
	</div>
	@else
	<div class="not-found-result">
		<div class="heading">{{ trans('report.you_have_no_payments_in_review') }}</div>
	</div>	
	@endif
</div><!-- .section -->