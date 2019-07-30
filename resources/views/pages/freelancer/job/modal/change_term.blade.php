<?php

use iJobDesk\Models\ProjectApplication;

?>
<!-- Change Term -->
<div class="modal fade" id="modalChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="changeModalLabel">{{ trans('common.revise_term') }}</h4>
			</div>
			<form id="formChange" class="form-horizontal" method="post" action="{{ _route('job.application_detail', ['id' => $application->id]) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="type" value="T">

				<div class="modal-body">
					<p class="pl-4 pb-4">{{ trans('job.you_may_propose_different_terms') }}</p>
					<div class="row margin-bottom-30">
						<div class="col-md-4">
							<label class="control-label">{{ trans('common.billing_amount') }}&nbsp;<span class="required">*</span></label>
						</div>
						<div class="col-md-8">
							<div class="input-group have-group-addon{{ $job->isHourly() ? ' w-40' : ' w-35' }}">
								<span class="input-group-addon"><i class="fa fa-usd"></i></span>

								@if ( $job->isHourly() )
									<input type="text" id="BillingRate" name="billing_hourly_rate" class="form-control billing-hourly-rate" data-rule-required="true" data-rule-number="true" data-rule-min="1" data-rule-max="999" value="{{ ($application->price) }}"/>
									<div class="input-group-addon">/hr</div>
								@else
									<input type="text" id="BillingRate" name="billing_fixed_rate" class="form-control billing-fixed-rate" data-rule-required="true" data-rule-number="true" data-rule-min="1" data-rule-max="9999999" value="{{ ($application->price) }}"/>
								@endif
							</div>

							<div class="info pt-1">{{ trans('job.this_is_what_the_client_sees') }}</div>
						</div>
					</div>
					<div class="row margin-bottom-30">
						<div class="col-md-4">
							<label class="control-label">{{ trans('common.ijobdesk_project_fee') }}</label>
						</div>
						<div class="col-md-8">
							<div class="fee-label mt-2{{ $job->isHourly() ? ' w-40' : ' w-35' }} text-right">
								<span class="input-group-addon pull-left"><i class="fa fa-usd"></i></span>
								@if ( $job->isHourly() )
									<span class="fee-unit pull-right">
									/{{ trans('common.hr') }}
									</span>
								@endif
								<span id="FeeValue" class="pull-right">
									{{ $application->feeRate($is_affiliated) }}									
								</span>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label class="control-label">
								{{ trans('job.you_will_receive') }}
							</label>
						</div>
						<div class="col-md-8">
							<div class="input-group have-group-addon{{ $job->isHourly() ? ' w-40' : ' w-35' }}">
								<span class="input-group-addon"><i class="fa fa-usd"></i></span>
								<input class="form-control" type="text" id="EarningRate" name="earning_rate" data-rule-required="true" data-rule-number="true" value="{{ $application->freelancerRate($is_affiliated) }}" />
								@if ( $job->isHourly() )
								<span class="input-group-addon">/{{ trans('hr') }}</span>
								@endif
							</div>
							<div class="info pt-1">{{ trans('job.estimated_amount_description') }}</div>
						</div>
					</div>
					@if (!$job->isHourly())
					<div class="row margin-bottom-30">
						<div class="col-md-4">
							<label class="control-label">{{ trans('common.estimated_duration') }}&nbsp;<span class="required">*</span></label>
						</div>
						<div class="col-md-8">
							<div class="w-50">
								<select name="duration" class="form-control select2" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
	                                <option value="">{{ trans('common.please_select') }}</option>
	                                @foreach (ProjectApplication::$str_application_duration as $key => $value)
	                                <option value="{{ $key }}" {{ $key == $application->duration?'selected':'' }}> {{ $value }} </option>
	                                @endforeach
	                            </select>
	                        </div>
						</div>
					</div>
					@endif
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ trans('common.submit') }}</button>
					<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
