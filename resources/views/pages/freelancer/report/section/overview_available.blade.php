<?php
/**
* @author Ro Un Nam
* @since Jun 08, 2017
*/

use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\UserPaymentGateway;
?>
<div class="section section-transactions">
	<div class="section-title">
		{{ trans('report.recent_transactions') }} ({{ trans('common.last_n_days', ['n' => 30]) }})
	</div>

	@if ( count($recent_transactions) )
	<div class="section-content">
		<table class="table">
			<thead>
				<tr>
					<th width="8%">{{ trans('common.ref_id') }}</th>
					<th width="10%" class="text-left">{{ trans('common.date') }}</th>
					<th width="10%" class="text-left">{{ trans('common.type') }}</th>
					<th class="text-left">{{ trans('common.description') }}</th>
					<th width="10%" class="text-right">{{ trans('common.amount') }}</th>
					<th width="10%" class="text-right">{{ trans('common.balance') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $recent_transactions as $index => $t )
				<tr>
					<td>{{ $t->id }}</td>
					<td class="text-left">{{ format_date('M d, Y', $t->done_at) }}</td>
					<td class="text-left">{{ $t->type_string() }}</td>
					<td class="text-left">
						@if ( $t->contract )
							<span class="user">{{ $t->contract->buyer->fullname() }}</span>
						@endif					
						{!! $t->description_string() !!}
					</td>
					<td class="text-right">
						@if ( $t->amount < 0 || $t->for == TransactionLocal::FOR_IJOBDESK )
							<?php
								$prev_balance = $balance + abs($t->amount);
							?>
						@else
							<?php
								$prev_balance = $balance - abs($t->reference->amount);
							?>
						@endif
						{{ $t->amount_string() }}
					</td>
					<td class="text-right">
						${{ formatCurrency($balance) }}
						<?php
							$balance = $prev_balance;
						?>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="link">
		<a href="{{ route('report.transactions') }}">{{ trans('report.view_all_transactions') }} <i class="fa fa-angle-right"></i></a>
	</div>
	@else
		<div class="not-found-result">
			<div class="heading">{{ trans('report.you_have_no_transactions') }}</div>
		</div>
	@endif
</div><!-- .section -->