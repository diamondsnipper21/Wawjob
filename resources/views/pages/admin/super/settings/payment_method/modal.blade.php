<?php
	use iJobDesk\Models\PaymentGateway;
	use iJobDesk\Models\Settings;
?>
<div id="modalPaymentMethod_{{ PaymentGateway::GATEWAY_PAYPAL }}" class="modal fade modal-payment-method" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
			<form class="form-horizontal" action="{{ Request::url() }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" name="_action" value="SAVE" />
				<input type="hidden" name="_type" value="{{ PaymentGateway::GATEWAY_PAYPAL }}" />

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">Edit PayPal Information</h4>
	            </div>

	            <div class="modal-body">

					{{ show_messages() }}

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Live Mode</label>
						</div>
						<div class="col-md-8">
							<div class="toggle-checkbox mt-3">
								<input type="checkbox" name="paypal_mode" {{ Settings::get('PAYPAL_MODE') == '1' ? 'checked' : '' }} value="1" />
							</div>
						</div>
					</div><!-- paypal_mode -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Email Address</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="paypal_email" name="paypal_email" data-rule-required="true" value="{{ Settings::get('PAYPAL_EMAIL') }}">
						</div>
					</div><!-- paypal_email -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">App ID</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="paypal_app_id" name="paypal_app_id" data-rule-required="true" value="{{ Settings::get('PAYPAL_APP_ID') }}">
						</div>
					</div><!-- paypal_email -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Api Username</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="paypal_api_username" name="paypal_api_username" data-rule-required="true" value="{{ Settings::get('PAYPAL_API_USERNAME') }}">
						</div>
					</div><!-- paypal_email -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Api Password</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="paypal_api_password" name="paypal_api_password" data-rule-required="true" value="{{ Settings::get('PAYPAL_API_PASSWORD') }}">
						</div>
					</div><!-- paypal_api_password -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Api Signature</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="paypal_api_signature" name="paypal_api_signature" data-rule-required="true" value="{{ Settings::get('PAYPAL_API_SIGNATURE') }}">
						</div>
					</div><!-- paypal_api_signature -->
				</div>
	            
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-primary blue btn-submit-payment-gateway">{{ trans('common.save') }}</button>
	                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
	            </div>
	        </div><!-- .modal-content -->
        </form>
    </div><!-- .modal-dialog -->
</div><!-- .modal-payment-method -->

<div id="modalPaymentMethod_{{ PaymentGateway::GATEWAY_SKRILL }}" class="modal fade modal-payment-method" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
			<form class="form-horizontal" action="{{ Request::url() }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" name="_action" value="SAVE" />
				<input type="hidden" name="_type" value="{{ PaymentGateway::GATEWAY_SKRILL }}" />

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">Edit Skrill Information</h4>
	            </div>

	            <div class="modal-body">

					{{ show_messages() }}

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Merchant Email</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="skrill_merchant_email" name="skrill_merchant_email" data-rule-required="true" value="{{ Settings::get('SKRILL_MERCHANT_EMAIL') }}">
						</div>
					</div><!-- skrill_merchant_email -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Merchant ID</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="skrill_merchant_id" name="skrill_merchant_id" data-rule-required="true" value="{{ Settings::get('SKRILL_MERCHANT_ID') }}">
						</div>
					</div><!-- skrill_merchant_id -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Merchant Password</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="skrill_merchant_password" name="skrill_merchant_password" data-rule-required="true" value="{{ Settings::get('SKRILL_MERCHANT_PASSWORD') }}">
						</div>
					</div><!-- skrill_merchant_password -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Merchant Secret Word</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="skrill_merchant_secret_word" name="skrill_merchant_secret_word" data-rule-required="true" value="{{ Settings::get('SKRILL_MERCHANT_SECRET_WORD') }}">
						</div>
					</div><!-- skrill_merchant_secret_word -->
				</div>
	            
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-primary blue btn-submit-payment-gateway">{{ trans('common.save') }}</button>
	                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
	            </div>
	        </div><!-- .modal-content -->
        </form>
    </div><!-- .modal-dialog -->
</div><!-- .modal-payment-method -->

<div id="modalPaymentMethod_{{ PaymentGateway::GATEWAY_PAYONEER }}" class="modal fade modal-payment-method" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
			<form class="form-horizontal" action="{{ Request::url() }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" name="_action" value="SAVE" />
				<input type="hidden" name="_type" value="{{ PaymentGateway::GATEWAY_PAYONEER }}" />

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">Edit Payoneer Information</h4>
	            </div>

	            <div class="modal-body">

					{{ show_messages() }}

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Payoneer Email</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="payoneer_email" name="payoneer_email" data-rule-required="true" value="{{ Settings::get('PAYONEER_EMAIL') }}">
						</div>
					</div><!-- weixin_phone_number -->
				</div>
	            
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-primary blue btn-submit-payment-gateway">{{ trans('common.save') }}</button>
	                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
	            </div>
	        </div><!-- .modal-content -->
        </form>
    </div><!-- .modal-dialog -->
</div><!-- .modal-payment-method -->

<div id="modalPaymentMethod_{{ PaymentGateway::GATEWAY_WEIXIN }}" class="modal fade modal-payment-method" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
			<form class="form-horizontal" action="{{ Request::url() }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" name="_action" value="SAVE" />
				<input type="hidden" name="_type" value="{{ PaymentGateway::GATEWAY_WEIXIN }}" />

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">Edit WeiXin Information</h4>
	            </div>

	            <div class="modal-body">

					{{ show_messages() }}

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">WeiXin Phone Number</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="weixin_phone_number" name="weixin_phone_number" data-rule-required="true" value="{{ Settings::get('WEIXIN_PHONE_NUMBER') }}">
						</div>
					</div><!-- weixin_phone_number -->
				</div>
	            
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-primary blue btn-submit-payment-gateway">{{ trans('common.save') }}</button>
	                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
	            </div>
	        </div><!-- .modal-content -->
        </form>
    </div><!-- .modal-dialog -->
</div><!-- .modal-payment-method -->

<div id="modalPaymentMethod_{{ PaymentGateway::GATEWAY_CREDITCARD }}" class="modal fade modal-payment-method" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
			<form class="form-horizontal" action="{{ Request::url() }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" name="_action" value="SAVE" />
				<input type="hidden" name="_type" value="{{ PaymentGateway::GATEWAY_CREDITCARD }}" />

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">Edit Credit Card Information</h4>
	            </div>

	            <div class="modal-body">

					{{ show_messages() }}

					Coming Soon
				</div>
	            
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-primary blue btn-submit-payment-gateway">{{ trans('common.save') }}</button>
	                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
	            </div>
	        </div><!-- .modal-content -->
        </form>
    </div><!-- .modal-dialog -->
</div><!-- .modal-payment-method -->

<div id="modalPaymentMethod_{{ PaymentGateway::GATEWAY_WIRETRANSFER }}" class="modal fade modal-payment-method" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
			<form class="form-horizontal" action="{{ Request::url() }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" name="_action" value="SAVE" />
				<input type="hidden" name="_type" value="{{ PaymentGateway::GATEWAY_WIRETRANSFER }}" />

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">Edit Bank Information</h4>
	            </div>

	            <div class="modal-body">

					{{ show_messages() }}

					<h4><strong>USD Receiving Account (ACH)</strong></h4>
					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Bank Name</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_name" name="bank_name" data-rule-required="true" value="{{ Settings::get('BANK_NAME') }}">
						</div>
					</div><!-- Bank name -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Bank Routing Number</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_routing_number" name="bank_routing_number" data-rule-required="true" value="{{ Settings::get('BANK_ROUTING_NUMBER') }}">
						</div>
					</div><!-- Routing Number -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Account Number</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_account_number" name="bank_account_number" data-rule-required="true" value="{{ Settings::get('BANK_ACCOUNT_NUMBER') }}">
						</div>
					</div><!-- Account Number -->

					<br />
					<h4><strong>EUR Receiving Account (SEPA)</strong></h4>

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Bank Name</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_name_euro" name="bank_name_euro" data-rule-required="true" value="{{ Settings::get('BANK_NAME_EURO') }}">
						</div>
					</div><!-- Bank name -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Bank Address</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_address" name="bank_address" data-rule-required="true" value="{{ Settings::get('BANK_ADDRESS') }}">
						</div>
					</div><!-- Bank branch -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">BIC</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_bic" name="bank_bic" data-rule-required="true" value="{{ Settings::get('BANK_BIC') }}">
						</div>
					</div><!-- SWIFT Code -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">IBAN</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_iban" name="bank_iban" data-rule-required="true" value="{{ Settings::get('BANK_IBAN') }}">
						</div>
					</div><!-- Account Name -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Bank Reference</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_reference" name="bank_reference" data-rule-required="true" value="{{ Settings::get('BANK_REFERENCE') }}">
						</div>
					</div><!-- Bank Reference -->

					<div class="form-group">
						<div class="col-md-4">
							<label class="control-label">Bank Reference User</label>
						</div>
						<div class="col-md-8">
							<input type="text" class="form-control" id="bank_reference_user" name="bank_reference_user" data-rule-required="true" value="{{ Settings::get('BANK_REFERENCE_USER') }}">
						</div>
					</div><!-- Bank Reference User -->
				</div>
	            
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-primary blue btn-submit-payment-gateway">{{ trans('common.save') }}</button>
	                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
	            </div>
	        </div><!-- .modal-content -->
        </form>
    </div><!-- .modal-dialog -->
</div><!-- .modal-payment-method -->