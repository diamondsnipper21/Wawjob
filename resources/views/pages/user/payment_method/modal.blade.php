<?php 
use iJobDesk\Models\PaymentGateway; 
use iJobDesk\Models\File;
?>
<!-- Add Payment Gateway Dialog -->
<div id="modalPaymentGateway" class="modal fade modal-payment-gateway" role="dialog" style="overflow:hidden;">
    <div class="modal-dialog modal-lg" role="slot">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('user.payment_method.add_a_payment_method') }}</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-5">
                        <div class="payment-gateways box default-boxshadow">
                            @foreach($payment_gateways as $payment_gateway)
                                @if ( !$payment_gateway->isWireTransfer() || count($countries) )
                                <div class="radiobox p-4 payment-gateway-{{ $payment_gateway->type }}">
                                    <label>
                                        <input type="radio" name="_gateway" id="payment_gateway{{ $payment_gateway->type }}" value="{{ $payment_gateway->type }}">
                                        <span>{{ parse_json_multilang($payment_gateway->name) }}</span>
                                        <img src="{{ $payment_gateway->logo }}" class="gateway-logo" height="24" />
                                    </label>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="col-xs-7">
                    	<div class="fields default-boxshadow p-4 ml-2 payment-gateway-fields-1 hidden">
            				<form class="form-horizontal" autocomplete="off" method="post" action="{{ route('user.payment_method') }}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_action" value="editPaymentGateway" />
								<input type="hidden" name="_gateway" value="{{ PaymentGateway::GATEWAY_PAYPAL }}" />

								<div class="form-group">
								    <label class="col-md-4 control-label">{{ trans('common.email') }}<span class="form-required">&nbsp;*</span></label>
								    <div class="col-md-8">
								        <div class="input-group">
								            <span class="input-group-addon"><i class="icon-user"></i></span>
								            <input type="text" data-rule-required="true" maxlength="40" data-rule-email="true" class="form-control" id="paypalEmail" name="paypalEmail">
								        </div>
								    </div>
								</div>
							</form>
                    	</div><!-- .payment-gateway-fields-1 -->

                    	@include('pages.user.payment_method.creditcard')

                    	<div class="fields default-boxshadow p-4 ml-2 payment-gateway-fields-3 hidden">
            				<form class="form-horizontal" autocomplete="off" method="post" action="{{ route('user.payment_method') }}">
            					<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_action" value="editPaymentGateway" />
								<input type="hidden" name="_gateway" value="{{ PaymentGateway::GATEWAY_WEIXIN }}" />
								<div class="form-group">
								    <label class="col-md-4 control-label">{{ trans('user.payment_method.phone_number') }}<span class="form-required">&nbsp;*</span></label>
								    <div class="col-md-8">
								        <div class="input-group">
								            <span class="input-group-addon"><i class="icon-phone"></i></span>
								            <input type="text" data-rule-required="true" maxlength="30" class="form-control" id="weixinNumber" name="weixinNumber">
								        </div>
								    </div>
								</div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">{{ trans('user.payment_method.qr_code') }}<span class="form-required">&nbsp;*</span></label>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="file-upload-container">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <span class="btn btn-success green btn-file">
                                                            <span class="fileinput-new "><i class="icon-cloud-upload"></i>&nbsp;&nbsp;{{ trans('common.upload') }}</span> 
                                                            <span class="fileinput-exists">{{ trans('common.change') }}</span>
                                                            
                                                            <input type="file" id="qrcode" class="form-control" name="attached_files" data-rule-required="true" {!! render_file_validation_options(File::TYPE_USER_QRCODE) !!} />
                                                            <input type="hidden" name="file_id">
                                                            <input type="hidden" name="file_type" value="{{ File::TYPE_USER_QRCODE }}" />
                                                        </span>
                                                        <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>&nbsp;&nbsp;&nbsp;
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="temp_qrcode"></div>
                                            </div>
                                        </div>

                                        <div class="info pt-2">
                                        	{!! trans('user.payment_method.description_qr_code') !!}
                                        </div>
                                    </div>
                                </div><!-- .form-group -->
							</form>
                    	</div><!-- .payment-gateway-fields-3 -->

                    	@include('pages.user.payment_method.bank')

                    	<div class="fields default-boxshadow p-4 ml-2 payment-gateway-fields-5 hidden">
            				<form class="form-horizontal" autocomplete="off" method="post" action="{{ route('user.payment_method') }}">
            					<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_action" value="editPaymentGateway" />
								<input type="hidden" name="_gateway" value="{{ PaymentGateway::GATEWAY_SKRILL }}" />
								<div class="form-group">
								    <label class="col-md-4 control-label">{{ trans('common.email') }}<span class="form-required">&nbsp;*</span></label>
								    <div class="col-md-8">
								        <div class="input-group">
								            <span class="input-group-addon"><i class="icon-user"></i></span>
								            <input type="text" data-rule-required="true" maxlength="40" data-rule-email="true" class="form-control" id="skrillEmail" name="skrillEmail">
								        </div>
								    </div>
								</div>
							</form>
                    	</div><!-- .payment-gateway-fields-5 -->

                        <div class="fields default-boxshadow p-4 ml-2 payment-gateway-fields-6 hidden">
                            <form class="form-horizontal" autocomplete="off" method="post" action="{{ route('user.payment_method') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_action" value="editPaymentGateway" />
                                <input type="hidden" name="_gateway" value="{{ PaymentGateway::GATEWAY_PAYONEER }}" />
                                <div class="form-group">
                                    <label class="col-md-4 control-label">{{ trans('common.email') }}<span class="form-required">&nbsp;*</span></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-user"></i></span>
                                            <input type="text" data-rule-required="true" maxlength="40" data-rule-email="true" class="form-control" id="payoneerEmail" name="payoneerEmail">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div><!-- .payment-gateway-fields-5 -->
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-submit-payment-gateway">{{ trans('common.save') }}</button>
                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
            </div>
        </div>
    </div>
</div>