<div class="modal fade" id="modalRefund" tabindex="-1" role="dialog" aria-labelledby="">
	<div class="modal-dialog" role="slot">
		<div class="modal-content">
			<form id="form_payment" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id])}}">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalPaymentLabel">{{ trans('common.refund') }}</h4>
				</div>

				<div class="modal-body">
					<div class="content-section">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" >
						<input type="hidden" name="_action" value="payment" >

						<input type="hidden" name="payment_type" id="payment_type" value="Refund">
						{{ show_messages() }}

						@if ( $total_paid_for_user_include_fee < 0 )
						<div class="alert alert-danger">{{ trans('contract.refund_not_available_description') }}</div>
						@endif

						<div class="mb-4">{{ trans('contract.refund_description') }}</div>

						<div class="row">
							<label class="col-xs-3 control-label">
								{{ trans('common.you_received') }}
							</label>
							<div class="col-xs-9">
								<label class="control-value">
									{{ $total_paid > 0 ? '(' . formatCurrency($total_paid, $currency_sign) . ')' : formatCurrency(abs($total_paid), $currency_sign) }}
								</label>

								@if ( $total_paid_pending > 0 )
								<div class="info mb-3">({{ trans('common.available') }}: {{ formatCurrency($total_paid_for_user_include_fee, $currency_sign) }}, {{ trans('common.pending') }}: {{ formatCurrency($total_paid_pending, $currency_sign) }})</div>
								@endif
								<div class="info mb-3">{{ trans('contract.you_received_description') }}</div>
							</div>
						</div>

						<div class="row">
							<label class="col-xs-3 control-label">{{ trans('common.amount') }} <span class="form-required"> *</span></label>
							<div class="col-xs-9">
								<div class="input-group input-icon right mb-1">
									<span class="input-group-addon">$</span>
									<input type="text" data-rule-required="true" data-rule-number="true" class="form-control amount-field" id="payment_amount" name="payment_amount" 
									value="{{ old('payment_amount') ? old('payment_amount') : '' }}" data-rule-min="0.1" data-rule-max="{{ $balance }}" data-msg-max="{{ trans('message.freelancer.payment.contract.failed_refund_amount_over_balance') }}" data-rule-max_paid_amount="{{ $total_paid_for_user_include_fee }}">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-9 col-xs-offset-3">
								<div class="info mb-2">{{ trans('contract.refund_amount_description') }}</div>
							</div>
						</div>

						<div class="row">
							<label class="col-xs-3 control-label">{{ trans('common.comment') }}</label>
							<div class="col-xs-9">
								<textarea class="form-control maxlength-handler" name="payment_note" id="payment_note" rows="3" maxlength="500" placeholder="{{ trans('common.comment') }}"></textarea> 
							</div>
						</div>

						<div class="row">
							<div class="col-xs-9 col-xs-offset-3">
								<div class="chk">
									<label class="pl-0">
										<input type="checkbox" name="confirm_payment" id="confirm_payment" class="checkbox" data-rule-required="true" value="1">
										{{ trans('contract.payment.confirm_refund') }}
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary btn-submit-refund{{ $total_paid_for_user_include_fee < 0 ? ' disabled' : ''}}">{{ trans('common.submit')}}</button>
					<button class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel')}}</button>
				</div>				
			</form>
		</div>
	</div>
</div>