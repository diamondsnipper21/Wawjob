<?php
/**
* Job Apply Page (apply_offer/{id})
*
* @author  - Ri Chol Min
*/
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
?>
@extends('layouts/default/index')

@section('content')
    <div class="page-content-section no-padding">
        <div class="view-section job-content-section {{ $job->isHourly() ? "hourly-job" : "fixed-job" }}">
            <div class="row">
                <div class="col-md-9">
                    <div class="box-section page-content">
                        
                        {{ show_warnings() }}
                        {{ show_messages() }}

                        <div class="job-top-section mb-4">
                            <div class="title-section">
                                <span class="title">{{ trans('common.job_offer') }}</span>
                            </div>
                        </div><!-- .job-top-section -->

        				<div class="sub-title break ml-3">
                            <a href="{{_route('job.view', ['id' => $job->id])}}">{{ $contract->title }}</a>
                        </div>

                        <form id="ApplyOfferForm" method="post" action="{{ route('job.apply_offer', ['id' => $contract->id]) }}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="SubmitAction" name="_action" value="">
                            
                            <div class="mb-4 ml-3">
                                <div class="mb-2 break">
                                    {!! nl2br($offer->message) !!}
                                </div>

                                @if ( $contract->files->count() )
                                <strong class="mt-4 display-block">{{ trans('common.attachments') }}</strong>
                                {!! render_files($contract->files) !!}
                                @endif
                            </div>
                            
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="sub-title">
                                        {{ trans('common.terms') }}
                                    </div>

                                    <div class="pl-4">
                                    	@if ( $contract->isHourly() )
                                        <div class="term-section">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label>{{ trans('common.job_type') }}</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <span class="margin-bottom-20">{{ trans('common.hourly') }}</span>
                                                </div>
                                            </div>
                                        </div><!-- .term-section -->

                                        <div class="term-section">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label>{{ trans('common.billing_rate') }}</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <span class="margin-bottom-20">{{ formatCurrency($contract->price, $currency_sign) }}/{{ trans('common.hr') }}</span>
                                                    <i class="icon icon-question ml-2" data-toggle="tooltip" title="{{ trans('job.it_includes_project_fee', ['p' => formatCurrency($contract->freelancerRate(), $currency_sign) . ($contract->isHourly() ? '/' . trans('hr') : '')]) }}" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div><!-- .term-section -->

                                        <div class="term-section">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label>{{ trans('common.weekly_limit') }}</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <span class="margin-bottom-20">
                                                        @if ( $contract->isNoLimit() )
                                                            {{ trans('common.no_limit') }}
                                                        @else
                                                            {{ trans('common.n_hours_week', ['n' => $contract->limit]) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div><!-- .term-section -->

                                        <div class="term-section">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label>{{ trans('common.manual_time') }}</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <span class="margin-bottom-20">
                                                        {{ $contract->isAllowedManualTime() ? trans('common.allowed') : trans('common.not_allowed') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div><!-- .term-section -->
                                    	@else
                                        <table class="table text-center margin-bottom-30">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('common.milestone') }}</th>
                                                    <th width="15%">{{ trans('common.start_date') }}</th>
                                                    <th width="15%">{{ trans('common.end_date') }}</th>
                                                    <th width="15%">{{ trans('common.status') }}</th>
                                                    <th width="15%" class="text-right">{{ trans('common.amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($contract->milestones as $milestone)
                                                    <tr>
                                                        <td class="text-left">
                                                            <span>{{ $milestone->name }}</span>
                                                        </td>
                                                        <td class="text-left">
                                                            <span>{{ date('n/j/Y', strtotime($milestone->start_time)) }}</span>
                                                        </td>
                                                        <td class="text-left">
                                                            <span>{{ date('n/j/Y', strtotime($milestone->end_time)) }}</span>
                                                        </td>
                                                        <td class="text-left">
                                                            <span>{{ $milestone->fund_status_string() }}</span>
                                                        </td>
                                                        <td class="text-right">
                                                            <span>{{ formatCurrency($milestone->getPrice(), $currency_sign) }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="5" class="text-right">
                                                        <strong>{{ trans('common.total') }}</strong> {{ formatCurrency($total_price, $currency_sign) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    	@endif

                                        <div>
                                            <textarea class="form-control maxlength-handler" name="message" maxlength="5000" placeholder="{{ trans('common.comment') }}" data-rule-required="true"></textarea>
                                            <div class="mb-4 border-bottom pb-4"></div>
                                        </div>

        	                            <div class="pb-4">
        	                                <button type="button" id="acceptOffer" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.accept') }}</button>&nbsp;&nbsp;&nbsp;
        	                                <a id="rejectOffer" class="btn btn-danger btn-border {{ $current_user->isSuspended() ? 'disabled' : '' }}">{{ trans('common.decline') }}</a>
        	                            </div>
        	                        </div>
                                </div><!-- .col-md-9 -->
                            </div><!-- .row -->
                        </form><!-- #ApplyOfferForm -->
                    </div><!-- .box-section -->
                </div><!-- .col-md-9 -->
                <div class="col-md-3 page-content">
                    <div class="instruction">
                        <div class="title">{{ trans('job.accept_or_decline') }}</div>
                        <ul class="pb-4">
                            <li class="mb-2">{{ trans('job.negotiate_the_terms_with_your_client') }}</li>
                            <li class="mb-2">{{ trans('job.when_you_accept_the_offer') }}</li>
                            <li class="mb-4">{{ trans('job.one_of_the_best_ways_you_can') }}</li>
                        </ul>
                    </div>
                </div>
            </div><!-- .row -->
        </div><!-- .view-section -->
    </div><!-- .page-content-section -->
</div><!-- .page-content -->
<script>
    var _error = '{{ isset($errorflag) ? $errorflag : "" }}';
</script>
@endsection