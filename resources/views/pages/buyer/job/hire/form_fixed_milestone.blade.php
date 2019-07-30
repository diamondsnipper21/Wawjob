<div class="milestone pt-3 border-light-bottom{{ $main ? ' first' : '' }}">

    <div class="row pb-3">
	    <div class="col col-title w-30">
	        <input type="input" name="milestone_title[]" class="form-control milestone-title maxlength-handler" maxlength="40" data-rule-required="true" value="{{ trans('common.completion_project') }}">
	    </div>

	    <div class="col col-amount w-20">
	        <div class="input-group">
	            <span class="input-group-addon">$</span>
	            <input type="text" class="form-control amount-field rate-input" name="milestone_price[]" data-rule-required="true" data-rule-number="true" data-rule-min="1" data-rule-max="999999" value="{{ $proposal ? $proposal->price : '0.00' }}">
	        </div>
	    </div>

	    <div class="col col-date w-25">
	        <div class="input-group date-field">
	            <input type="text" class="form-control" data-rule-date="true" name="milestone_end[]" value="{{ date('m/d/Y') }}">
	            <span class="input-group-addon date-picker">
	                <i class="fa icon-calendar"></i>
	            </span>
	        </div>
	    </div>

	    <div class="col col-fund w-25">
	        <div class="chk pt-2 pl-5">
	            <label class="pl-0">
	                <input type="checkbox" class="toggle checkbox" name="milestone_fund[]" value="1" checked>
	                <input type="hidden" class="milestone_fund_value" name="milestone_fund_value[]" value="1">
	                {{ trans('common.yes') }}
	            </label>

	            <i class="icon icon-question ml-2" data-toggle="tooltip" data-placement="top" title="{{ trans('contract.payment.confirm_escrow') }}"></i>

	            @if ( !$main )
				<i class="hs-admin-trash btn-delete-milestone ml-2"></i>
				@endif
	        </div>
	    </div>
	</div>

</div>