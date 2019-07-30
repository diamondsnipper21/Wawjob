<?php
/**
 * @author Ro Un Nam
 * @since Jun 03, 2017
 */

use iJobDesk\Models\TransactionLocal;
$amount = $total_paid;
?>
<div id="contract_transactions" role="tabpanel" class="tab-pane">
	<div class="tab-inner">
		@if ( count($transactions) )
		<div class="table">
			<div class="thead">
				<div class="tr">
					<div class="th rp-ref" style="width:8%">{{ trans('common.ref_id') }}</div>
					<div class="th rp-date" style="width:10%">{{ trans('common.date') }}</div>
					<div class="th rp-type" style="width:12%">{{ trans('common.type') }}</div>
					<div class="th rp-description" style="width:46%">{{ trans('common.description') }}</div>
					<div class="th rp-freelancer" style="width:12%">{{ trans('common.freelancer') }}</div>
					<div class="th rp-amount text-right" style="width:12%">{{ trans('common.amount') }}</div>
				</div>
			</div>
			<div class="tbody">
				@foreach ($transactions as $t)
				<div class="tr status-{{ strtolower($t->status_string()) }}">
					<div class="td rp-ref" style="width:8%">{{ $t->id }}</div>
					<div class="td rp-date" style="width:10%">{{ $t->date_string() }}</div>
					<div class="td rp-type" style="width:12%">{{ $t->type_string() }}</div>
					<div class="td rp-description" style="width:46%"><div class="break">{!! $t->description_string() !!}</div></div>
					<div class="td rp-freelancer" style="width:12%">{{ $t->freelancer_string() }}</div>
					<div class="td rp-amount text-right" style="width:12%">{{ $t->amount_string() }}</div>
				</div>
				@endforeach

				<div class="tr border-0">
					<div class="td text-right" style="width:100%">
						<strong>{{ $amount > 0 ? '$' . formatCurrency($amount) : '($' . formatCurrency(abs($amount)) . ')' }}</strong>
					</div>
				</div>
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
	</div><!-- .tab-inner -->
</div>