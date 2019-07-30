<div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="">
	<div class="modal-dialog" role="slot">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalPaymentLabel">{{ trans('common.refund_money') }}</h4>
			</div>

			<div class="modal-body text-center">
				<div class="content-section clearfix">
					<form id="form_payment" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id])}}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" >
						<input type="hidden" name="_action" value="payment" >

						<input type="hidden" name="payment_type" id="payment_type" value="Refund">
						{{ show_messages() }}

						<div class="row">
							<label class="col-md-3">{{ trans('common.amount') }}</label>
							<div class="col-md-9">
								<div class="input-field">
									<div class="input-group input-icon right">
										<span class="input-group-addon">$</span>
										<input type="text" data-rule-required="true" class="form-control" id="payment_amount" name="payment_amount" 
										placeholder="" value="{{ old('payment_amount') ? old('payment_amount') : '' }}">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<label class="col-md-3">{{ trans('common.note') }}</label>
							<div class="col-md-9">
								<textarea class="form-control" name="payment_note" id="payment_note" rows="7" placeholder="You can input note"></textarea> 
							</div>
						</div>

						<div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-9 text-left">
								<div class="input-icon right checkbox-inline">
									<input type="checkbox" name="confirm_payment" id="confirm_payment" class="checkbox" data-rule-required="true" value="1">
									<label for="confirm_payment">{{ trans('contract.confirm_refund') }}</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-9">
								<button class="btn btn-primary btn-submit-refund">{{ trans('common.submit')}}</button>
								<button class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel')}}</button>
							</div>
						</div>
					</form>  
				</div>
			</div>
		</div>
	</div>
</div>