<div class="modal fade" id="modalPayment" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="payment_slot">
		<div class="modal-content">
			<form id="form_payment" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id])}}">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalPaymentLabel">{{ trans('common.bonus_payment') }}</h4>
				</div>
				<div class="modal-body">
					<div class="content-section">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="_action" value="payment" >
						<input type="hidden" name="payment_type" id="payment_type" value="">

						<div class="row">
							<label class="col-xs-3 control-label">{{ trans('common.amount') }}<span class="form-required"> *</span></label>
							<div class="col-xs-9">
								<div class="input-group form-line-wrapper">
									<span class="input-group-addon">$</span>
									<input type="text" class="form-control amount-field" id="payment_amount" name="payment_amount" value="{{ old('payment_amount') ? old('payment_amount') : '' }}" data-rule-required="true" data-rule-number="true" data-rule-min="0.1" data-rule-max="{{ $balance }}" data-msg-max="{{ trans('message.not_enough_balance') }}">
								</div>
							</div>
						</div>

						<div class="row">
							<label class="col-xs-3 control-label">
								{{ trans('common.balance') }}
							</label>
							<div class="col-xs-9">
								<label class="control-value">
									{{ $balance < 0 ? '(' . formatCurrency(abs($balance), $currency_sign) . ')' : formatCurrency($balance, $currency_sign) }}
								</label>
							</div>
						</div>

						<div class="row">
							<label class="col-xs-3 control-label">
								{{ trans('common.note') }}
							</label>
							<div class="col-xs-9">
								<textarea class="form-control maxlength-handler" name="payment_note" id="payment_note" rows="3" maxlength="500" placeholder="{{ trans('contract.payment.enter_your_note_here') }}"></textarea> 
							</div>
						</div>

						<div class="row">
							<div class="col-xs-9 col-xs-offset-3">
								<div class="chk">
									<label class="pl-0">
										<input type="checkbox" name="confirm_payment" id="confirm_payment" value="1" data-rule-required="true" value="1">
										{{ trans('contract.payment.confirm_bonus') }}
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary">{{ trans('common.submit') }}</button>
					<button data-dismiss="modal" class="btn btn-link">{{ trans('common.cancel') }}</button>
				</div>				
			</form>
		</div>
	</div>
</div>