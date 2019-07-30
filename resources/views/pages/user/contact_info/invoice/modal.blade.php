<div class="modal fade modal-edit-invoice-address" id="modalEditInvoiceAddress" aria-hidden="false">
	<form name="edit_comment" class="form-horizontal" id="frm_edit_invoice_address" method="POST" action="{{ route('user.contact_info', ['section' => 'invoice-address']) }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">{{ trans('common.edit_invoice_address') }}</h4>
				</div>

				<div class="modal-body">
					<div class="row form-group">
						<div class="col-sm-3 control-label">{{ trans('common.address') }} <span class="form-required"> *</span></div>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="invoice_address" data-rule-required="true" value="{{ ($user->contact->invoice_address != null)? $user->contact->invoice_address : '' }}" />
						</div>
					</div>

					<div class="row form-group">
						<div class="col-sm-3 control-label">
							<div class="pre-summary">{{ trans('common.city') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="invoice_city" data-rule-required="true" value="{{ ($user->contact->invoice_city != null) ? $user->contact->city : '' }}" />							
						</div>
						<div class="clear-div"></div>
					</div>

					<div class="row form-group">
						<div class="col-sm-3 control-label">
							<div class="pre-summary">{{ trans('common.state') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="invoice_state" data-rule-required="true" value="{{ ($user->contact->invoice_state != null) ? $user->contact->state : '' }}" />
						</div>
						<div class="clear-div"></div>
					</div>

					<div class="row form-group">
						<div class="col-sm-3 control-label">
							<div class="pre-summary">{{ trans('common.country') }}</div>
						</div>
						<div class="col-sm-9">
							<select type="text" class="form-control select2" name="invoice_countryCode">
								<option value="">{{ trans('common.please_select') }}</option>
								@foreach ($countries as $country)
								   <option value="{{ $country->charcode }}" {{ ($user->contact->invoice_country_code == $country->charcode) ? 'selected' : '' }}>{{$country->name}}</option>
								@endforeach
							</select>						
						</div>
						<div class="clear-div"></div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-save">{{ trans('common.save') }}</button>
					<button type="button" class="btn btn-link btn-cancel" data-dismiss="modal">{{ trans('common.cancel') }}</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</form>
</div>
