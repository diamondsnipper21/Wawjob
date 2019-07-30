<?php
/**
 * @author Ro Un Nam
 * @since Jun 03, 2017
 */

use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
$amount = $total_paid_for_user;
?>
<div id="contract_transactions" role="tabpanel" class="tab-pane">
	<div class="tab-inner">
	@if ( count($transactions) )
		<div class="table">
			<div class="thead">
				<div class="tr">
					<div class="th rp-ref" style="width:8%">{{ trans('common.ref_id') }}</div>
					<div class="th rp-date" style="width:14%">{{ trans('common.date') }}</div>
					<div class="th rp-type" style="width:14%">{{ trans('common.type') }}</div>
					<div class="th rp-description" style="width:35%">{{ trans('common.description') }}</div>
					<div class="th rp-buyer" style="width:15%">{{ trans('common.client') }}</div>
					<div class="th rp-amount text-right" style="width:14%">{{ trans('common.amount') }}</div>
				</div>
			</div>
			<div class="tbody">
				@foreach ($transactions as $t)
					<div class="tr status-{{ strtolower($t->status_string()) }}">
						<div class="td rp-ref" style="width:8%">{{ $t->id }}</div>
						<div class="td rp-date" style="width:14%">{!! $t->date_string() !!}</div>
						<div class="td rp-type" style="width:14%">{{ $t->type_string() }}</div>
						<div class="td rp-description" style="width: 35%"><div class="break">{!! $t->description_string() !!}</div></div>
						<div class="td rp-buyer" style="width:15%">{{ $t->buyer_string() }}</div>
						<div class="td rp-amount text-right" style="width:14%">
							@if ( $t->for == TransactionLocal::FOR_IJOBDESK )
								@if ( $t->isRefund() )
									<?php
										$credit = abs($t->amount);
									?>
								@else
									<?php
										$credit = -(abs($t->amount));
									?>
								@endif
							@else
								@if ( $t->isRefund() )
									<?php
										$credit = -(abs($t->reference->amount));
									?>
								@else
									@if ( $t->reference )
									<?php
										$credit = abs($t->reference->amount);
									?>
									@else
									<?php
										$credit = abs($t->amount);
									?>
									@endif
								@endif
							@endif

							<div class="credit">
								@if ( $credit > 0 )
									${{ formatCurrency($credit) }}
								@elseif ( $credit < 0 )
									(${{ formatCurrency(abs($credit)) }})
								@else
									-
								@endif
							</div>
							<div class="amount">
								${{ formatCurrency($amount) }}
							</div>
							<?php
								$amount = $amount + (-$credit);
							?>						
						</div>
					</div>
				@endforeach
			</div>
		</div>
	@else
		<div class="not-found-result">
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="heading">{{ trans('contract.you_have_no_transactions') }}</div>
				</div>
			</div>
		</div>
	@endif
	</div>
</div>