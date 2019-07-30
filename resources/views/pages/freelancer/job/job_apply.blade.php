<?php
/**
* Job Apply Page (apply/{id})
*
* @author  - Ri Chol Min
*/

use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\File;
use iJobDesk\Models\Settings;

?>
@extends('layouts/default/index')

@section('content')
<script type="text/javascript">
    var errorConnectionLimit = '{{ trans('job.connection_limit') }}';
    var errorNotEnoughConnections = '{{ trans('job.connection_not_enough') }}';
    var required_connects = '{{ $needed_connections }}';
    var current_connects = '{{ $connections }}';
    var rate = {{ $rate }};
</script>

<div class="page-content-section page-job-apply no-padding">
    <div class="form-section">
        @if (isset($error))
        <div class="has-error"><span class="help-block">{{ $error }}</span></div>
        @endif
        <div class="view-section job-content-section {{ $job->isHourly() ? 'hourly-job' : 'fixed-job' }}">
            <div class="job-top-section mb-4 {{ $job->is_featured == 1 ? ' featured' : '' }}">
                <div class="title-section">
                    <span class="title">{{ trans('common.submit_a_proposal') }}</span>
                    <a href="{{ _route('job.view', ['id' => $job_id]) }}" class="pull-right pt-2">{{ trans('job.back_to_the_job_posting') }}</a>
                </div>
            </div>
            <div class="box-alert-section"></div>

            {{ show_messages() }}

            <!-- Invalid Qualifications -->
            @if ($show_alert)
            <div class="alert alert-warning alert-dismissible qualifications" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="alert-title"><strong>{{trans('job.you_do_not_meet_qualification')}}</strong> </div>
                <div class="alert-text">{{ trans('job.you_do_not_meet_qualification_desc') }}</div>
                <ul class="list">
                @if ($show_alert & 1)
                    <li data-ignore-toastr="true">
                        {{ trans('job.preferred_success_score') }}: <span>{{trans('job.at_least_n_percents', ['n' => $job->qualification_success_score]) }}</span>
                    </li>
                @endif
                @if ($show_alert & 2)
                    <li data-ignore-toastr="true">
                        {{ trans('common.location') }}: <span>{{$job->qualification_location}}</span>
                    </li>
                @endif
                @if ($show_alert & 4)
                    <li data-ignore-toastr="true">
                        {{ trans('common.hours_cap') }}: <span>{{trans('job.at_least_n_hours', ['n' => $job->qualification_hours]) }}</span>
                    </li>
                @endif
                </ul>
            </div>
            @endif

            <div class="box-section margin-bottom-35">
                <form id="JobDetailForm" method="post" action="{{ _route('job.apply', ['id' => $job_id]) }}" enctype="multipart/form-data" data-connections="{{ $connections }}" data-needed-connections="{{ $needed_connections }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="sub-section">
                        <div class="sub-block">
                            <!-- Project Title -->
                            <div class="subject-title margin-bottom-20 break"><h2>{{ $job->subject }}</h2></div>
                            <!-- Project Category -->
                            <div class="job-category rounded-item margin-bottom-20">&nbsp;&nbsp;{{ parse_multilang($job->category->name) }}</div>
                            <div class="clearfix"></div>

                            <div class="row">
                                <!-- Left Panel -->
                                <div class="col-md-5 border-right">
                                	<div class="pr-4">
	                                    <div class="row float-none">
	                                        <div class="col-md-10 col-sm-10">
	                                            @include ('pages.job.detail.top_summary')
	                                        </div>
	                                    </div>
	                                    <div class="sub-title border-0 margin-bottom-0 bold-title">{{ trans('common.description') }}</div>
	                                    <div class="description margin-bottom-30">
	                                        <div id="desc_more" class="desc break">
                                                {!! render_more_less_desc($desc, 250) !!}
	                                        </div>
	                                        <div class="mt-4 pull-right">
	                                            <a href="{{ _route('job.view', ['id' => $job_id]) }}" target="_blank">{{ trans('job.view_job_posting') }}</a>
	                                        </div>
	                                    </div>
	                                </div><!-- .pr-2 -->
                                </div><!-- End Left Panel -->

                                <!-- Right Panel -->
                                <div class="col-md-7">
                                    <div class="pl-4">
                                        <div class="proposal-connects shadow-box p-0">
                                            <div class="needed-connections" data-value="{{ $needed_connections }}">
                                                <strong>{{ $needed_connections }}</strong>
                                                <span>{{ trans('job.connects_required') }}</span>
                                            </div>
                                            <div class="total-connections" data-value="{{ $connections }}">
                                                <strong>{{ $connections - $needed_connections }} / {{ $connections }}</strong>
                                                <span>{{ trans('job.connects_left') }}</span>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="proposal-section">
                                            <div class="sub-title border-0 bold-title"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;{{ trans('common.proposal') }}</div>

                                            <div class="row mb-4">
                                                <div class="col-sm-4 col-xs-6 mt-2">
                                                    <label class="bold-title">{{ trans('common.billing_amount') }}<span class="form-required"> *</span></label>
                                                </div>
                                                <div class="col-sm-8 col-xs-6">
                                                    <div class="form-group">
                                                        <div class="input-group w-35">
                                                            <span class="input-group-addon price-unit"><i class="fa fa-usd"></i></span>
                                                            @if ( $job->isHourly() )
                                                            <input id="BillingRate" name="billing_hourly_rate" type="text" class="form-control" value="{{ old('billing_hourly_rate') ? formatCurrency(old('billing_hourly_rate')) : formatCurrency($billing_rate) }}" data-rule-required="true" data-rule-number="true" {{ $current_user->isSuspended() ? 'disabled' : '' }} data-rule-min="1" data-rule-max="999" />
                                                            <span class="input-group-addon unit">/{{ trans('common.hr') }}</span>
                                                            @else
                                                            <input id="BillingRate" name="billing_fixed_rate" type="text" class="form-control" value="{{ old('billing_rate_fixed') ? formatCurrency(old('billing_rate_fixed')) : formatCurrency($billing_rate) }}" data-rule-required="true" data-rule-number="true" {{ $current_user->isSuspended() ? 'disabled' : '' }} data-rule-min="1" data-rule-max="9999999" />
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <span class="help-comment" for="BillingRate">{{ trans('job.this_is_what_the_client_sees') }}</span>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Service Fee  -->
                                            <div class="row service-fee mb-4">
                                                <div class="col-sm-4 col-xs-6 mt-1">
                                                    <label class="bold-title">{{ trans('job.ijobdesk_service_fee') }}</label>
                                                </div>
                                                <div class="col-sm-8 col-xs-6">
                                                    <div class="input-group w-35">
                                                        <span class="input-group-addon pull-left"><i class="fa fa-usd"></i></span>
                                                        @if ( $job->isHourly() )
                                                        <span class="service-fee-unit pull-right">/{{ trans('common.hr') }}</span>
                                                        @endif                
                                                        <span class="service-fee-value pull-right">{{ old('ijobdesk_fee') ? formatCurrency(old('ijobdesk_fee')) : formatCurrency($ijobdesk_fee) }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-sm-4 col-xs-6 mt-2">
                                                    <label class="bold-title">{{ trans('job.you_will_receive') }}</label>
                                                </div>
                                                <div class="col-sm-8 col-xs-6">
                                                    <div class="form-group">
                                                        <div class="input-group w-35">
                                                            <span class="input-group-addon price-unit"><i class="fa fa-usd"></i></span>
                                                            @if ( $job->isHourly() )
                                                            <input id="EarningRate" name="earning_rate" type="text" class="form-control" data-rule-required="true" data-rule-number="true" value="{{ old('earning_rate') ? formatCurrency(old('earning_rate')) : formatCurrency($earning_rate) }}" {{ $current_user->isSuspended() ? 'disabled' : '' }} />
                                                            @else
                                                            <input id="EarningRate" name="earning_rate" type="text" class="form-control" data-rule-required="true" data-rule-number="true" value="{{ old('earning_rate') ? formatCurrency(old('earning_rate')) : formatCurrency($earning_rate) }}" {{ $current_user->isSuspended() ? 'disabled' : '' }} />
                                                            @endif
                                                            @if ( $job->isHourly() )
                                                            <span class="input-group-addon unit">/{{ trans('common.hr') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span for="EarningRate">{{ trans('job.estimated_amount_description') }}</span>
                                                </div>
                                            </div>

                                            @if ($job->type == Project::TYPE_FIXED)
                                            <div class="row mb-4">
                                                <div class="col-md-4 col-xs-6 mt-2">
                                                    <label class="bold-title">{{ trans('common.estimated_duration') }}<span class="form-required"> *</span></label>
                                                </div>
                                                <div class="col-md-8 col-xs-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-5 col-xs-12 input-group">
                                                            <select name="duration" class="form-control select2" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                                                                <option value="">{{ trans('common.please_select') }}</option>
                                                                @foreach (Project::$str_project_duration as $key => $value)
                                                                <option value="{{ $key }}"> {{ $value }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="clearfix"></div>

                                            <!-- Coverletter -->
                                            @if ($job->req_cv == Project::COVER_LETTER_YES)
                                            <div class="proposal-section">
                                                <div class="form-group">
                                                    <label class="bold-title" for="CoverLetter">{{ trans('common.cover_letter') }}<span class="form-required"> *</span></label>
                                                    <textarea id="CoverLetter" name="coverletter" class="form-control maxlength-handler" maxlength="5000" rows="7" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }} >{{ old('coverletter') ? old('coverletter') : '' }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Attach Files -->
                                            <div class="proposal-section attached-files">
                                                <div class="form-group">
                                                    <label class="control-label bold-title"  for="files">{{ trans('job.attachment_optional') }}</label>
                                                    <div>{!! render_file_element(File::TYPE_PROJECT_APPLICATION) !!}</div>
                                                    <div class="clearfix"></div>
                                                    <p class="help-block">{{ trans('job.attachments_explanation', ['max_upload_file_size' => config('filesystems.max_upload_file_size')]) }}</p>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="proposal-section featured-proposal mt-4 py-4">
                                                <div>
                                                	<label class="control-label" for="featured">{{ trans('job.feature_my_proposal') }}</label> - 
                                                	@if ( $job->isFeatured() )
                                                		{{ Settings::get('CONNECTIONS_FEATURED_PROJECT') }} 
                                                	@else
                                                		1 
                                                	@endif
                                                	{{ trans('job.more_connects_required') }}
                                                </div>
                                                <div class="info mb-4">{{ trans('job.feature_my_proposal_description') }}</div>
                                                <div class="chk">
                                                    <label>
                                                        <input type="checkbox" name="featured" id="featured" value="1" {{ $current_user->isSuspended() ? 'disabled' : '' }}> {{ trans('job.yes_make_this_proposal_featured') }}
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mb-4 mt-4">
                                                <button type="button" id="acceptSubmitProposal" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.submit_a_proposal') }}</button>
                                                <a href="{{_route('job.view', ['id' => $job_id])}}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                                            </div>
                                        </div>
                                    </div><!-- .pl-3 -->
                                </div><!-- End Right Panel -->
                            </div>
                        </div>
                    </div>

                    <!-- NOW we don't use this section -->
                    <div class="sub-section hide">
                        <div class="sub-title">{{ trans('job.propose_terms') }}</div>
                        <div class="sub-block">
                            <label>
                                {{ trans('job.submitting_as_freelancer') }}
                            </label>

                            <div class="info">
                                @if ( $job->type === Project::TYPE_HOURLY )
                                    {{ trans('job.affordable_rate_is_n', ['n' => $job->affordable_rate_string()]) }}
                                @else
                                    {{ trans('job.budget_is_n', ['n' => $job->price]) }}
                                @endif
                            </div>
                            
                            <div class="row">
                                <div class="col-md-9">
                                    
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-md-9">
                                    <div class="proposal-section service-fee">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>      
    </div>
</div>
@endsection