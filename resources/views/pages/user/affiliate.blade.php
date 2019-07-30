<?php
/**
 * Affiliate Page (/affiliate)
 *
 * @author  - Ro Un Nam
 */
?>
@extends('layouts/default/index')

@section('content')
<div class="user-affiliate-page">
    <div class="row">
        <div class="col-sm-9">
            <div class="user-affiliate-page-left default-boxshadow p-4">
                <div class="title-section mb-4 border-bottom-0">
                    <span class="title">
                        <i class="icon-share title-icon"></i>
                        {{ trans('page.' . $page . '.title') }}
                    </span>
                </div>

                <div class="tab-section">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#affiliate_overview" aria-controls="affiliate_overview" role="tab" data-toggle="tab">{{ trans('common.overview') }}</a></li>
                        <li role="presentation"><a href="#affiliate_history" aria-controls="affiliate_history" role="tab" data-toggle="tab">{{ trans('common.history') }}</a></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="affiliate_overview">

                        {{ show_messages() }}

                        <div class="row">
                        	<div class="col-sm-7 mb-4">
                                <div class="sub-title text-center mb-2">{{ trans('user.affiliate.your_referrals') }}</div>
                                <div class="info text-center mb-2">{{ trans('user.affiliate.it_shows_the_users_registered_by_your_referral') }}</div>
	                        	<canvas id="chart_invitation" data-buyer-label="{{ trans('common.buyer') }}" data-freelancer-label="{{ trans('common.freelancer') }}" data-buyer="{{ $values['accepted_buyer'] }}" data-freelancer="{{ $values['accepted_freelancer'] }}"></canvas>
	                        </div>

                            <div class="col-sm-5 mt-5">
                                <div class="mt-5 pt-4 pb-4 pl-5 border-left">
                                    @if ( $values['total_sent'] )
                                    <div class="pl-4">
                                        <div class="row">
                                        	<div class="col-sm-12">
                                        		<div class="col-xs-6">
		                                            <div class="color-gray value">{{ $values['total_sent'] }}</div>
		                                            <div class="info mb-5">{{ trans('user.affiliate.invitations_sent') }}</div>
		                                        </div>
		                                        <div class="col-xs-6">
	                                            	<div class="color-gray value">{{ $values['accepted'] }}</div>
	                                            	<div class="info">{{ trans('user.affiliate.registered') }}</div>
	                                            </div>
	                                        </div>
                                        </div>
                                    </div>
                                    @else
                                    	<div class="info pt-5 pb-5">{{ trans('user.affiliate.nothing_sent_invitations_yet') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="title-section">
                            <span class="title"><i class="icon-envelope-open title-icon"></i> {{ trans('common.send_invitation') }}</span>
                        </div>

                        <div class="form-section">
                            <form id="formAffiliate" class="form-horizontal" method="post" action="{{ route('user.affiliate') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                
                                <fieldset>
                                    <div class="row">
                                        <div class="col-sm-12 pre-summary">
                                            {{ trans('user.affiliate.let_them_know_url') }}
                                        </div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-1"></div>
                                        <div class="col-sm-11">
                                        	<div class="mb-1">{{ trans('user.affiliate.invite_as_buyer') }}</div>
                                            <input type="text" value="{{ $affiliate_buyer_url }}" class="form-control field-url" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-1"></div>
                                        <div class="col-sm-11">
                                        	<div class="mb-1">{{ trans('user.affiliate.invite_as_freelancer') }}</div>
                                            <input type="text" value="{{ $affiliate_freelancer_url }}" class="form-control field-url" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 pre-summary">
                                            {{ trans('user.affiliate.or_send_invitation_here') }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2 col-sm-12 control-label">
                                            {{ trans('common.emails') }}
                                        </div>
                                        <div class="col-md-10 col-sm-12">
                                            <div class="form-line-wrapper">
                                                <input type="text" class="form-control" data-rule-required="true" id="emails" name="emails" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                                            </div>
                                            <div class="pre-summary pre-italic">{{ trans('user.affiliate.note_comma_separated_email') }}</div>
                                        </div>
                                    </div>

                                    <div class="row pt-4 pb-4">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.send') }}</button>
                                        </div>
                                    </div>

                                </fieldset>
                            </form>
                        </div>            

                    </div>

                    <div role="tabpanel" class="tab-pane" id="affiliate_history">

                        <form id="formAffiliateHistory" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_action" value="filter">

                            {{ show_messages() }}

                            <div class="date-filter-section form-group pull-left">
                                <div class="date-filter">
                                    <div class="input-group" id="date_range" data-from="{{ date('Y-m-d', strtotime($dates['from'])) }}" data-to="{{ date('Y-m-d', strtotime($dates['to'])) }}">
                                        @include("pages.snippet.daterange")
                                        <span class="input-group-btn">
                                        <button class="btn default date-range-toggle" type="button"><i class="fa icon-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="type-filter-section section-content w-25 pull-right">
                                <select class="form-control select2" id="user_type" name="user_type">
                                    <option value="0"@if ($user_filter == '0') selected @endif>{{ trans('common.all') }}</option>
                                    <option value="2"@if ($user_filter == '2') selected @endif>{{ trans('common.buyer') }}</option>
                                    <option value="1"@if ($user_filter == '1') selected @endif>{{ trans('common.freelancer') }}</option>
                                </select>
                            </div>

                            <div class="clearfix"></div>
                        </form>

                        <div class="table-scrollable list-affiliates">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="8%">{{ trans('common.ref_id') }}</th>
                                        <th width="12%">{{ trans('common.date') }}</th>
                                        <th width="15%">{{ trans('common.user') }}</th>
                                        <th>{{ trans('common.description') }}</th>
                                        <th width="10%" class="text-right">{{ trans('common.amount') }}</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @include('pages.user.affiliate_ajax')
                                </tbody>
                            </table>
                        </div><!-- .table-scrollable"-->

                    </div><!-- #affiliate_history -->
                </div><!-- .tab-content -->
            </div><!-- .user-affiliate-page-left -->
        </div>

        <div class="col-sm-3">
            <div class="user-affiliate-page-right">
                <div class="box default-boxshadow p-4 mb-4">
                    <h5 class="mt-0 mb-4">{{ trans('user.affiliate.affiliate_program') }}</h5>
                    <p>{!! trans('user.affiliate.affiliate_description', ['link' => route('frontend.help.detail', ['slug' => 'affiliate-program'])]) !!}</p>
                </div>

                <div class="box pending default-boxshadow p-4 mb-4">
                	<div class="row">
                		<div class="col-sm-4">
                			<div class="wallet"><i class="icon-wallet"></i></div>
                		</div>

                		<div class="col-sm-8">
                			<div class="amount">${{ formatCurrency($values['payment_pending']) }}</div>
                			<label>{{ trans('common.pending_payments') }}</label>
                		</div>
                	</div>
                </div>

                <div class="box incoming default-boxshadow">
                	<div class="p-4">
                		<div class="icon mb-2"><i class="icon-share"></i></div>
                		<div class="amount mb-2">${{ formatCurrency($values['earning_lifetime']) }}</div>
                		<label class="mb-0">{{ trans('user.affiliate.all_income') }}</label>
                	</div>
                	<div class="border-light-top bg-color color-gray p-2">{{ trans('user.affiliate.lifetime_earnings_by_affiliate_program') }}</div>
                </div>
            </div><!-- .user-affiliate-page-right -->
        </div>
    </div>
</div><!-- .page-content-section -->

@endsection