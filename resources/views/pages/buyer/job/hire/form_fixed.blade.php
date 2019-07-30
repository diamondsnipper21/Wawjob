<div id="fixed_setting" class="mb-4 hidden">

	<div class="mb-4 pb-2">
		<label class="control-label">{{ trans('common.estimate') }} <span class="required">*</span></label>
		<div class="row form-group">
			<div class="col-md-4">
				<div class="input-group">
					<span class="input-group-addon">{{ $currency_sign }}</span>
					<input type="input" id="contract_estimate" name="contract_estimate" class="contract-title form-control" value="{{ old('contract_title') ? old('contract_estimate') : formatCurrency($proposal ? $proposal->price : 0) }}" data-rule-number="true" data-rule-required="true" data-rule-min="1" data-rule-max="999999" />
				</div>
			</div>
		</div>
	</div>

	<div class="pb-2">
		<label class="control-label">
			{{ trans('common.milestones') }}
			<span class="info ml-3 fs-13">({{ trans('common.optional') }} - {{ trans('job.you_can_do_it_later') }})</span>
		</label>
	</div>

	<div class="pb-4 pl-2">
		<div class="radiobox pt-2">
			<label>
				<input type="radio" name="contract_milestones" id="one_milestone" value="1" checked> {{ trans('job.create_milestone_whole_project') }}
			</label>
		</div><!-- .radio-list -->
		<div class="radiobox pt-2">
			<label>
				<input type="radio" name="contract_milestones" id="more_milestones" value="2"> {{ trans('job.need_more_milestones') }}
			</label>
		</div><!-- .radio-list -->
	</div>

    <div class="milestones">
    	<div class="milestone-header">
    		<div class="text-right pb-3 mb-3 border-light-bottom">
    			{{ trans('common.available_funds_in_your_account') }}: <strong>${{ formatCurrency($balance, $currency_sign) }}</strong>
    		</div>

    		<div class="row hidden-mobile">
    			<div class="col w-30 text-center">
    				<label class="control-label">{{ trans('job.name_of_milestone') }}</label>
    			</div>
    			<div class="col w-20 text-center">
    				<label class="control-label">{{ trans('common.amount') }}</label>
    			</div>
    			<div class="col w-25 text-center">
    				<label class="control-label">{{ trans('common.due_date') }} ({{ trans('common.optional') }})</label>
    			</div>
    			<div class="col w-25 text-center">
    				<label class="control-label">{{ trans('common.deposit_into_escrow') }}</label>
    			</div>
    		</div>
    	</div>

    	@include('pages.buyer.job.hire.form_fixed_milestone', ['main' => true])
	</div><!-- .milestones -->

    <div class="add-milestone-info hidden">
			<a class="btn btn-link btn-add-milestone">{{ trans('job.create_additional_milestones') }}</a>
		<span class="info ml-3 fs-13">({{ trans('common.optional') }} - {{ trans('job.you_can_do_it_later') }})</span>
    </div>

</div><!-- #fixed_setting -->