<?php
/**
 * @author Ro Un Nam
 * @since Jun 11, 2017
 */

use iJobDesk\Models\TransactionLocal;
?>
<div class="transactions-section table-scrollable">
	<div class="table">
		<div class="thead">
			<div class="tr">
				<div class="th rp-ref" style="width:8%">{{ trans('common.ref_id') }}</div>
				<div class="th rp-date" style="width:10%">{{ trans('common.date') }}</div>
				<div class="th rp-type" style="width:12%">{{ trans('common.type') }}</div>
				<div class="th rp-description" style="width:34%">{{ trans('common.description') }}</div>
				<div class="th rp-buyer" style="width:12%">{{ trans('common.client') }}</div>
				<div class="th rp-amount text-right" style="width:12%">{{ trans('common.amount') }}</div>
				<div class="th rp-balance text-right" style="width:12%">{{ trans('common.balance') }}</div>
			</div>
		</div>
		<div class="tbody">
			@forelse ($transactions as $t)
				@include ('pages.freelancer.report.section.transaction_row', ['balance' => $balance])
				@if ( $t->isDone() || $t->isWithdraw() )
					@if ( $t->for == TransactionLocal::FOR_IJOBDESK )
						@if ( $t->isRefund() )
							<?php
								$prev_balance = $balance - abs($t->amount);
							?>
						@elseif ( $t->isWithdraw() )
							<?php
								$prev_balance = $balance - $t->amount;
							?>
						@else
							<?php
								$prev_balance = $balance + abs($t->amount);
							?>
						@endif
					@else
						@if ( $t->isRefund() )
							<?php
								$prev_balance = $balance + $t->reference->amount;
							?>
						@else
							@if ( $t->isWithdraw() )
							<?php
								$prev_balance = $balance - $t->amount;
							?>
							@else
								@if ( $t->reference )
								<?php
									$prev_balance = $balance - abs($t->reference->amount);
								?>
								@else
								<?php
									$prev_balance = $balance - abs($t->amount);
								?>
								@endif
							@endif
						@endif
					@endif
				@else
					<?php
						$prev_balance = $balance;
					?>
				@endif
				<?php
					$balance = $prev_balance;
				?>
			@empty
				<div class="not-found-result">
					<div class="heading">{{ trans('report.you_have_no_transactions') }}</div>
				</div>
			@endforelse
		</div>
	</div>
</div><!-- .transactions-section -->