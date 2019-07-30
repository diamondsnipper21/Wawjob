<div class="fields default-boxshadow p-4 ml-2 hidden payment-gateway-fields-4">
	<form class="form-horizontal" autocomplete="off" method="post" action="{{ route('user.payment_method') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="editPaymentGateway" />
		<input type="hidden" name="_gateway" value="4" />
        <input type="hidden" name="_id" value="0" />
        <div class="form-group">
            <label for="country" class="col-md-6 control-label text-right">{{ trans('user.payment_method.country_of_bank') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-6">
				<select type="text" class="form-control select2" data-rule-required="true" id="bankCountry" name="bankCountry">
					<option value="">{{ trans('common.please_select') }}</option>
					@foreach ($countries as $country)
					   <option value="{{ $country->charcode }}">{{ $country->name }}</option>
					@endforeach
				</select>
            </div>
        </div>
        <div class="form-group">
            <label for="bankName" class="col-md-6 control-label text-right">{{ trans('user.payment_method.bank_name') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-6">
                <input type="text" data-rule-required="true" maxlength="50" class="form-control" id="bankName" name="bankName">
            </div>
        </div>
        <div class="form-group">
            <label for="bankBranch" class="col-md-6 control-label text-right">{{ trans('user.payment_method.bank_branch') }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control" maxlength="50" id="bankBranch" name="bankBranch" />
            </div>
        </div>
        <div class="form-group">
            <label for="ibanAccountNo" class="col-md-6 control-label text-right">{{ trans('user.payment_method.iban_account_no') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-6">
                <input type="text" data-rule-required="true" maxlength="50" data-rule-minlength="10" class="form-control" id="ibanAccountNo" name="ibanAccountNo">
            </div>
        </div>
        <div class="form-group">
            <label for="accountName" class="col-md-6 control-label text-right">{{ trans('user.payment_method.account_name') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-6">
                <input type="text" data-rule-required="true" maxlength="50" class="form-control" id="accountName" name="accountName">
            </div>
        </div>
        <div class="form-group">
            <label for="beneficiaryAddress1" class="col-md-6 control-label text-right">{{ trans('user.payment_method.beneficiary_address1') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-6">
                <input type="text" data-rule-required="true" maxlength="50" class="form-control" id="beneficiaryAddress1" name="beneficiaryAddress1">
            </div>
        </div>
        <div class="form-group">
            <label for="beneficiaryAddress2" class="col-md-6 control-label text-right">{{ trans('user.payment_method.beneficiary_address2') }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control" maxlength="50" id="beneficiaryAddress2" name="beneficiaryAddress2">
            </div>
        </div>
        <div class="form-group">
            <label for="beneficiarySwiftCode" class="col-md-6 control-label text-right">{{ trans('user.payment_method.beneficiary_swift_code') }}<span class="form-required">&nbsp;*</span></label>
            <div class="col-md-6">
                <input type="text" data-rule-required="true" maxlength="50" data-rule-minlength="8" class="form-control" id="beneficiarySwiftCode" name="beneficiarySwiftCode">
            </div>
        </div>
    </form>
</div><!-- .payment-gateway-fields-4 -->