<div class="fields default-boxshadow p-4 ml-2 payment-gateway-fields-2 hidden">
	<form class="form-horizontal" autocomplete="off" method="post" action="{{ route('user.payment_method') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="editPaymentGateway" />
		<input type="hidden" name="_gateway" value="2" />
        <input type="hidden" name="_id" value="0" />
        <div class="form-group">
            <label for="firstName" class="col-md-4 control-label text-right">{{ trans('user.payment_method.first_name') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-8">
                <input type="text" data-rule-required="true" class="form-control" id="firstName" name="firstName">
            </div>
        </div>

        <div class="form-group">
            <label for="lastName" class="col-md-4 control-label text-right">{{ trans('user.payment_method.last_name') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-8">
                <input type="text" data-rule-required="true" class="form-control" id="lastName" name="lastName">
            </div>
        </div>

        <div class="form-group">
            <label for="cardType" class="col-md-4 control-label text-right">{{ trans('user.payment_method.card_type') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-8">
                <select name="cardType" id="cardType" data-rule-required="true" class="form-control select2">
                    <option value="">{{ trans('common.please_select') }}</option>
                    <option value="Visa">{{ trans('user.payment_method.visa') }}</option>
                    <option value="MasterCard">{{ trans('user.payment_method.mastercard') }}</option>
                    <option value="Discover">{{ trans('user.payment_method.discover') }}</option>
                    <option value="Amex">{{ trans('user.payment_method.american_express') }}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="cardNumber" class="col-md-4 control-label text-right">{{ trans('user.payment_method.card_number') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-8">
		        <div class="input-group">
		            <span class="input-group-addon"><i class="icon-credit-card"></i></span>
		            <input type="text" data-rule-required="true" data-rule-number="true" data-rule-minlength="14" class="form-control" id="cardNumber" name="cardNumber">
		        </div>
            </div>
        </div>

        <div class="form-group">
            <label for="expDateMonth" class="col-md-4 control-label text-right">{{ trans('user.payment_method.expiry_date') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <select name="expDateMonth" id="expDateMonth" data-rule-required="true" class="form-control select2">
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
                        <select name="expDateYear" id="expDateYear" data-rule-required="true" class="form-control select2">
                            @for ( $i = date('Y'); $i <= date('Y') + 10; $i++ )
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="cvv" class="col-md-4 control-label text-right">{{ trans('user.payment_method.cvv') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-8">
                <input type="text" data-rule-required="true" class="form-control" id="cvv" name="cvv">
            </div>
        </div>
    </form>
</div><!-- .payment-gateway-fields-2 -->