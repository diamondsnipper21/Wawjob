<?php
/**
 * @author Ro Un Nam
 * @since Dec 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\PaymentGateway;
?>

<div id="modal_deposit" class="modal fade modal-scroll" tabindex="-1" data-width="1000" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title"><span>New</span> Deposit Transaction</h4>
	</div>
	<form id="formDeposit" action="{{ Request::url() }}" method="post">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="_id" value="" />
    	<input type="hidden" name="_action" value="edit_deposit" />

		<div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Buyer&nbsp;<span class="required">*</span></label>
						<select id="user_id" name="user_id" class="form-control select2" data-placeholder="Search for a buyer">
				        </select>
					</div>

					<div class="form-group">
						<label class="control-label">Amount&nbsp;<span class="required">*</span></label>
						<div class="input-group have-group-addon">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" name="amount" id="amount" value="" data-rule-number="true" data-rule-required="true" data-rule-min="1" />
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Payment Gateway&nbsp;<span class="required">*</span></label>
						<select class="form-control select2" name="user_payment_gateway_type" id="user_payment_gateway_type" data-rule-required="true" data-width="100%">
							<option value="">Select...</option>
							@foreach($payment_gateways as $payment_gateway)
								<option value="{{ $payment_gateway->id }}">{{ parse_json_multilang($payment_gateway->name) }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Comment&nbsp;<span class="required">*</span></label>
						<textarea name="description" id="description" data-rule-required="true" class="form-control maxlength-handler" rows="5" maxlength="1000"></textarea>
					</div>
				</div>
			</div><!-- .row -->

			<div class="form-group all-gateway-fields">
				<div id="gateway_fields_{{ PaymentGateway::GATEWAY_PAYPAL }}" class="row gateway-fields hide">
					<div class="col-md-6">
						<input type="text" class="form-control" id="email_1" name="paypal_email" data-rule-required="true" placeholder="Email">
					</div>
				</div><!-- .paypal-fields -->

				<div id="gateway_fields_{{ PaymentGateway::GATEWAY_SKRILL }}" class="row gateway-fields hide">
					<div class="col-md-6">
						<input type="text" class="form-control" id="email_5" name="skrill_email" data-rule-required="true" placeholder="Email">
					</div>
				</div><!-- .paypal-fields -->

				<div id="gateway_fields_{{ PaymentGateway::GATEWAY_WEIXIN }}" class="row gateway-fields hide">
					<div class="col-md-6">
						<input type="text" class="form-control" id="phoneNumber_3" name="wepayPhoneNumber" data-rule-required="true" placeholder="Phone Number">
					</div>
				</div><!-- .paypal-fields -->
				
				<div id="gateway_fields_{{ PaymentGateway::GATEWAY_WIRETRANSFER }}" class="gateway-fields hide">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<select type="text" class="form-control select2" data-rule-required="true" id="bankCountry_4" name="bankCountry" data-width="100%">
									<option value="">{{ trans('common.please_select') }}</option>
									@foreach ($countries as $country)
									   <option value="{{ $country->charcode }}">{{ $country->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="bankName_4" name="bankName" data-rule-required="true" placeholder="Bank Name">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="bankBranch_4" name="bankBranch" data-rule-required="true" placeholder="Bank Branch">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="ibanAccountNo_4" name="ibanAccountNo" data-rule-required="true" placeholder="IBAN / Account No">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<input type="text" class="form-control" id="accountName_4" name="accountName" data-rule-required="true" placeholder="Account Name">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="beneficiaryAddress1_4" name="beneficiaryAddress1" data-rule-required="true" placeholder="Beneficiary Address 1">
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="beneficiaryAddress2_4" name="beneficiaryAddress2" data-rule-required="true" placeholder="Beneficiary Address 2">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="beneficiarySwiftCode_4" name="beneficiarySwiftCode" data-rule-required="true" placeholder="Beneficiary SWIFT Code">
							</div>
						</div>
					</div>
				</div><!-- .bank-fields -->

				<div id="gateway_fields_{{ PaymentGateway::GATEWAY_CREDITCARD }}" class="gateway-fields hide">
                    <div class="row">
                    	<div class="col-md-6">
	                        <div class="form-group">
	                            <input type="text" data-rule-required="true" class="form-control" id="firstName_2" name="firstName" placeholder="First Name">
	                        </div>

	                        <div class="form-group">
                                <input type="text" data-rule-required="true" class="form-control" id="lastName_2" name="lastName" placeholder="Last Name">
	                        </div>

	                        <div class="form-group">
                                <select name="cardType" id="cardType_2" data-rule-required="true" class="form-control select2" data-width="100%">
                                    <option value="">Select Card Type</option>
                                    <option value="Visa">Visa</option>
                                    <option value="MasterCard">MasterCard</option>
                                    <option value="Discover">Discover</option>
                                    <option value="Amex">American Express</option>
                                </select>
	                        </div>
	                    </div>

	                    <div class="col-md-6">
	                        <div class="form-group">
                                <input type="text" data-rule-required="true" data-rule-number="true" data-rule-minlength="14" class="form-control" id="cardNumber_2" name="cardNumber" placeholder="Card Number">
	                        </div>

	                        <div class="form-group row">
                                <div class="col-md-6">
                                    <select name="expDateMonth" id="expDateMonth_2" data-rule-required="true" class="form-control select2" data-width="100%">
                                        <option value="">Expiry Month</option>
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
	                                <select name="expDateYear" id="expDateYear_2" data-rule-required="true" class="form-control select2" data-width="100%">
	                                    <option value="">Expiry Year</option>
	                                    @for ( $i = date('Y'); $i <= date('Y') + 10; $i++ )
	                                    <option value="{{ $i }}">{{ $i }}</option>
	                                    @endfor
	                                </select>
	                            </div>
	                        </div>

	                        <div class="form-group">
                                <input type="text" data-rule-required="true" class="form-control" id="cvv_2" name="cvv" placeholder="CVV">
	                        </div>
	                    </div>
	                </div>
				</div><!-- .creditcard-fields -->

				<div id="gateway_fields_{{ PaymentGateway::GATEWAY_PAYONEER }}" class="row gateway-fields hide">
					<div class="col-md-6">
						<input type="text" class="form-control" id="email_6" name="payoneer_email" data-rule-required="true" placeholder="Email">
					</div>
				</div><!-- .payoneere-fields -->
			</div><!-- .payment-gateway-fields -->
		</div>

		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
			<button type="submit" class="save-button btn blue">Save</button>
		</div>
	</form>
</div>