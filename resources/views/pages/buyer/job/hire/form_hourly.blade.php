<div id="hourly_setting" class="hidden">

	<label class="control-label">{{ trans('common.billing_amount') }} <span class="required">*</span></label>
	<div class="row mb-4 pb-2">
		<div class="col-md-4">
			<div class="input-group">
				<span class="input-group-addon">{{ $currency_sign }}</span>
				<input type="text" id="billing_rate" name="billing_rate" class="form-control billing-rate" data-rule-number="true" data-rule-required="true" data-rule-min="1" data-rule-max="999" value="{{ old('billing_rate') ? old('billing_rate') : $billing_hourly_rate }}">
				<span class="input-group-addon">/{{ trans('common.hr') }}</span>
			</div>
			<div class="info text-right pt-1">{{ trans('job.you_will_pay') }}</div>
		</div>
	</div>

	<label class="control-label">{{ trans('common.weekly_limit') }} <span class="required">*</span></label>
	<div class="row mb-4 pb-2">
		<div class="col-md-4">
			<select class="form-control select2" name="week_limit" id="week_limit">
				@for ($i = 1; $i <= 12; $i++)
				<option value="{{ $i * 5 }}">{{ trans('common.n_hours_week', ['n' => $i * 5]) }}</option>
				@endfor
				<option value="-1">{{ trans('common.no_limit') }}</option>
			</select> 
		</div>
		<div class="col-md-3">
			<div class="mt-3">{!! trans('common.max_week') !!}</div>
		</div>
	</div>

	<div class="mb-4">
		<div class="chk">
			<label>
				<input type="checkbox" class="checkbox" name="manual_time" value="1" checked />
				{{ trans('job.allow_freelancer_manual_log') }}
			</label>
		</div>
	</div>
</div><!-- #hourly_setting -->