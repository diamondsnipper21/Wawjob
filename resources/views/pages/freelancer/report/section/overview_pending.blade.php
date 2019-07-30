<?php
/**
* @author Ro Un Nam
* @since Jun 08, 2017
*/

use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\UserPaymentGateway;
?>

<div class="section section-in-review">
	@if ( count($last2_week_transactions) )
	<div class="section-content">
		<table class="table">
			<thead>
				<tr>
					<th width="10%">{{ trans('common.date') }}</th>
					<th width="10%">{{ trans('common.type') }}</th>
					<th class="text-left">{{ trans('common.description') }}</th>
					<th width="10%">{{ trans('common.amount') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $last2_week_transactions as $t )
				<tr>
					<td>{{ format_date('M d, Y', $t->created_at) }}</td>
					<td>{{ $t->type_string() }}</td>
					@if ( $t->isWithdraw() )
						<td class="text-left">
							<div class="desc">
							@if ( $t->userPaymentGateway )
								{{ parse_json_multilang($t->userPaymentGateway->paymentGateway->name) }} 
								@if ( $t->userPaymentGateway->gateway == UserPaymentGateway::GATEWAY_WIRETRANSFER )
									({{ json_decode($t->userPaymentGateway->data)->bankName }})
								@else
									({{ json_decode($t->userPaymentGateway->data)->email }})
								@endif
							@else
								- 
							@endif
							</div>
						</td>
						<td>(${{ formatCurrency(abs($t->amount)) }})</td>
					@elseif ( $t->isAffiliate() )
						<td class="text-left">
							@if ( $t->contract )
								<span class="user">{{ $t->contract->buyer->fullname() }}</span>
								<div class="desc">
									<a href="{{ _route('contract.contract_view', ['id' => $t->contract_id]) }}">{{ $t->contract->title }}</a>
								</div>
							@else
								<span class="user">{{ $t->ref_user->fullname() }}</span>
							@endif
						</td>
						<td>
							@if ( $t->amount > 0 )
								${{ formatCurrency($t->amount) }}
							@else
								(${{ formatCurrency(abs($t->amount)) }})
							@endif
						</td>
					@else
						<td class="text-left">
							<span class="user">{{ $t->contract->buyer->fullname() }}</span>
							<div class="desc">
								<a href="{{ _route('contract.contract_view', ['id' => $t->contract_id]) }}">{{ $t->contract->title }}</a>
							</div>
						</td>
						<td>${{ $t->reference ? formatCurrency(abs($t->reference->amount)) : $t->amount }}</td>
					@endif
				</tr>
				@endforeach
				<tr>
					<td colspan="4" class="text-right">
						<strong>
						@if ( $total_pending > 0 )
							${{ formatCurrency($total_pending) }}
						@else
							(${{ formatCurrency(abs($total_pending)) }})
						@endif
						</strong>
					</td>
				</tr>				
			</tbody>
		</table>
	</div>
	@else
	<div class="not-found-result">
		<div class="heading">{{ trans('report.you_have_no_pending_payments') }}</div>
	</div>	
	@endif
</div><!-- .section -->