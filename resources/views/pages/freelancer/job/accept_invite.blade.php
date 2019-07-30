<?php
/**
 * Job Apply Page (accept_invite/{id})
 */
use iJobDesk\Models\Project;
use iJobDesk\Models\Settings;
?>
@extends('layouts/default/index')

@section('content')

<script type="text/javascript">
	var rate = {{ $rate }};
</script>

<div class="page-content-section no-padding">
	<div class="view-section job-content-section {{ $job->isHourly() ? 'hourly-job' : 'fixed-job' }}">
        <div class="row">
            <div class="col-md-9">
                <div class="box-section page-content">

					{{ show_warnings() }}
					{{ show_messages() }}

                	<div class="job-top-section mb-4">
						<div class="title-section">
							<span class="title">{{ trans('common.job_invitation') }}</span>
						</div>
					</div>

					<div class="box-section mb-4">
			            <div class="sub-title break ml-3">
			                <a href="{{ _route('job.view', ['id' => $job->id]) }}">{{ $job->subject }}</a>
			            </div>

						<div class="mb-3 ml-3">
							{!! render_more_less_desc($invitation->message) !!}
						</div>


						<form id="formInvitation" class="form-horizontal" method="post" action="{{ route('job.accept_invite', ['id' => $invitation->id]) }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="_action" value="accept">

							<div class="sub-title">{{ trans('common.terms') }}</div>

							<div class="job-term-panel mb-4 pl-4">
								<div class="row pb-2">
									<label class="col-sm-3 col-xs-6 control-label">{{ trans('common.billing_amount') }}<span class="form-required"> *</span></label>
									<div class="col-sm-9 col-xs-6">
										<div class="input-group w-25">
											<div class="input-group-addon">{{ $currency_sign }}</div>
											@if ( $job->isHourly() )
												<input type="text" id="BillingRate" name="billing_hourly_rate" class="form-control billing-hourly-rate" data-rule-required="true" data-rule-number="true" data-rule-max="999" value="{{ $billing_rate }}" {{ $current_user->isSuspended() ? 'disabled' : '' }}/>
												<div class="input-group-addon">/hr</div>
											@else
												<input type="text" id="BillingRate" name="billing_fixed_rate" class="form-control billing-fixed-rate" data-rule-required="true" data-rule-number="true" value="{{ $billing_rate }}" {{ $current_user->isSuspended() ? 'disabled' : '' }}/>
											@endif
										</div>
										<div class="info pt-2">{{ trans('job.billing_amount_description') }}</div>
									</div>
								</div>

								<div class="row pb-4">
									<label class="col-sm-3 col-xs-6 control-label">{{ trans('common.ijobdesk_project_fee') }}</label>
									<div class="col-sm-9 col-xs-6">
										<div class="fee-label pt-2 w-25">
											<div class="input-group-addon pull-left">{{ $currency_sign }}</div>
											@if ( $job->isHourly() )
											<span class="fee-unit pull-right">/{{ trans('common.hr') }}</span>
											@endif
											<span id="FeeValue" class="pull-right">{{ $job->isHourly() ? $ijobdesk_fee : 0 }}</span>
										</div>
									</div>
								</div>

								<div class="row pb-4">
									<label class="col-sm-3 col-xs-6 control-label">{{ trans('job.you_will_receive') }}</label>
									<div class="col-sm-9 col-xs-6">
										<div class="input-group w-25">
											<div class="input-group-addon">{{ $currency_sign }}</div>
											<input type="text" id="EarningRate" name="earning_rate" class="form-control" value="{{ $job->isHourly() ? $earning_rate : 0 }}" data-rule-required="true" data-rule-number="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}/>
											@if ( $job->isHourly() )
											<div class="input-group-addon">/{{ trans('common.hr') }}</div>
											@endif
										</div>
										<div class="info pt-2">{{ trans('job.estimated_amount_description') }}</div>
									</div>
								</div>

								@if ( !$job->isHourly() )
								<div class="row pb-4">
									<label class="col-sm-3 col-xs-6 control-label">{{ trans('common.estimated_duration') }}</label>
									<div class="col-sm-3 col-xs-6">
										<div class="parent">
			                                <select name="duration" class="form-control select2" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
			                                    <option value="">{{ trans('common.please_select') }}</option>
			                                    <option value="MT6M"> {{ trans('common.mt6m') }} </option>
			                                    <option value="3T6M"> {{ trans('common.3t6m') }} </option>
			                                    <option value="1T3M"> {{ trans('common.1t3m') }} </option>
			                                    <option value="LT1M"> {{ trans('common.lt1m') }} </option>
			                                    <option value="LT1W"> {{ trans('common.lt1w') }} </option>
			                                </select>
			                            </div>
			                        </div>
			                    </div>
								@endif

								<div class="row pb-2">
									<label class="col-md-3 col-xs-6 control-label">{{ trans('common.comment') }}<span class="form-required"> *</span></label>
									<div class="col-sm-6 col-xs-6">
										<textarea name="message" id="message" class="form-control maxlength-handler" maxlength="{{ $config['freelancer']['user']['description_length'] }}" rows="5" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}></textarea>
									</div>
								</div>

								<div class="mb-4 border-bottom pb-4"></div>

								<div class="row">
									<div class="col-md-9">
										<button type="submit" id="btnAccept" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.accept') }}</button>
										<button type="button" id="btnDecline" class="btn btn-link" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.decline') }}</button>
									</div>
								</div>								
							</div>
						</form>

						<form id="formDeclineInvitation" method="post" action="{{ route('job.accept_invite', ['id' => $invitation->id]) }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="_action" value="decline">
							<input type="hidden" name="message" value="">
						</form>
					</div>
                </div>
            </div><!-- .col-md-9 -->

            <div class="col-md-3 page-content">
                <div class="instruction">
                    <div class="title">{{ trans('job.accept_or_decline') }}</div>
                    <ul class="pb-4">
                        <li class="mb-4">{{ trans('job.accept_or_decline_invitation') }}</li>
                        <li class="mb-4">{{ trans('job.one_of_the_best_ways_you_can_for_invitation') }}</li>
                    </ul>
                </div>
            </div>
        </div><!-- .roww -->
	</div><!-- .view-section -->
</div><!-- .page-content-section -->

@endsection