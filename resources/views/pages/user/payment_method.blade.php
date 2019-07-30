<?php
/**
* User Payment Method (user/payment-method)
*/
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\Country;

?>
@extends('layouts/user/index')

@section('content')
<div class="title-section">
    <span class="title">
        <i class="icon-credit-card title-icon"></i>
        {{ trans('page.' . $page . '.title') }}
    </span>
</div>

<div class="page-content-section user-payment-method-page">
    {{ show_messages() }}

    <form id="formListPaymentGateway" method="post" action="{{ route('user.payment_method') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_action" value="deletePaymentGateway">
        <input type="hidden" name="_gatewayId" value="0">
       
        @if ( count($current_user->paymentGateways) > 0 )
        <ul class="list-group">
            @foreach ( $current_user->paymentGateways as $upg )
            <li class="list-group-item{{ $upg->isPrimary() ? ' r-primary' : '' }}">
                <div class="row row-inner">
                	<div class="col-md-3">
                        <img src="{{ $upg->logo() }}" class="img-responsive img-logo mt-1 ml-2" />
                    </div>

                    <div class="col-md-3 c-title mt-2">
                        {{ $upg->title() }}
                    </div>

                    <div class="col-md-2 c-status text-center mt-2">
                        @if ($upg->isExpired())
                        <i class="icon-clock"></i><label class="expired">{{ trans('common.expired') }}</label>
                        @elseif ($upg->isPending())
                        <i class="icon-hourglass"></i><label class="pending">{{ trans('common.pending') }}</label>
                        @elseif ($upg->isPrimary())
                        <i class="icon-check"></i><label class="primary">{{ trans('common.primary') }}</label>
                        @endif
                    </div>

                    <div class="col-md-2 text-center mt-2 hidden-mobile">
                        {{ format_date($format_date2, $upg->created_at) }}
                    </div>

                    @if ( !$current_user->isSuspended() && !$current_user->isFinancialSuspended() )
                    <div class="col-md-2 text-right mt-1">
		                <a class="btn btn-link action-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
							<i class="icon-settings"></i> {{ trans('common.action') }} <i class="icon-arrow-down"></i>
						</a>
                        <ul class="dropdown-menu pull-right" role="menu">
                            @if ( $upg->paymentGateway->isWeixin() && $upg->file_id )
                            <li>
                                <a href="#modalViewPaymentGateway" class="btn btn-link" data-qrcode="{{ file_url($upg->file) }}" data-toggle="modal" data-backdrop="static"><i class="icon-link"></i> {{ trans('common.view') }}</a>
                            </li>
                        	@elseif ( $upg->paymentGateway->isCreditCard() || $upg->paymentGateway->isWireTransfer() )
                        	<li>
                        		<a href="#modalEditPaymentGateway" class="btn btn-link btn-edit" data-id="{{ $upg->id }}" data-logo="{{ $upg->paymentGateway->logo }}" data-gateway="{{ $upg->gateway }}" data-json="{{ $upg->dataJson() }}" data-toggle="modal" data-backdrop="static"><i class="icon-pencil"></i> {{ trans('common.edit')}}</a>
                        	</li>
                        	@endif
                            @if ( $upg->isActive() && !$upg->isPrimary() )
                            <li>
                                <a class="btn btn-link btn-make-primary" data-id="{{ $upg->id }}"><i class="icon-check"></i> {{ trans('common.set_primary')}}</a>
                            </li>
                            <li class="divider"></li>
                            @endif
                            <li>
                                <a class="btn btn-link btn-delete" data-id="{{ $upg->id }}"><i class="icon-trash"></i> {{ trans('common.remove')}}</a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div><!-- .row -->

                @if ( $upg->paymentGateway->isCreditCard() || $upg->paymentGateway->isWireTransfer() )
                <?php $parsed = $upg->dataArray(); ?>
                <div class="additional">
                    <div class="information mt-2 mb-2">
                    	<div class="row">
                    		<div class="col-md-9 col-md-offset-3 pt-3 pb-2">
		                    @if ( $upg->paymentGateway->isCreditCard() )
                                @if ( isset($parsed['firstName']) )
								<div class="row">
								    <label for="bankName" class="col-md-4">{{ trans('user.payment_method.first_name') }}</label>
								    <div class="col-md-8">{{ $parsed['firstName'] }}</div>
								</div>
                                @endif
                                @if ( isset($parsed['lastName']) )
								<div class="row">
								    <label for="bankName" class="col-md-4">{{ trans('user.payment_method.last_name') }}</label>
								    <div class="col-md-8">{{ $parsed['lastName'] }}</div>
								</div>
                                @endif
                                @if ( isset($parsed['cardType']) )
								<div class="row">
								    <label for="bankName" class="col-md-4">{{ trans('user.payment_method.card_type') }}</label>
								    <div class="col-md-8">{{ $parsed['cardType'] }}</div>
								</div>
                                @endif
                                @if ( isset($parsed['cardNumber']) )
								<div class="row">
								    <label for="bankName" class="col-md-4">{{ trans('user.payment_method.card_number') }}</label>
								    <div class="col-md-8">xxxx xxxx xxxx {{ $parsed['cardNumber'] }}</div>
								</div>
                                @endif
                                @if ( isset($parsed['expDateYear']) )
								<div class="row">
								    <label for="bankName" class="col-md-4">{{ trans('user.payment_method.expiry_date') }}</label>
								    <div class="col-md-8">{{ $parsed['expDateYear'] . '/' . $parsed['expDateMonth'] }}</div>
								</div>
                                @endif
                                @if ( isset($parsed['cvv']) )
								<div class="row">
								    <label for="bankName" class="col-md-4">{{ trans('user.payment_method.cvv') }}</label>
								    <div class="col-md-8">{{ $parsed['cvv'] }}</div>
								</div>
                                @endif
		                    @else
                                @if ( isset($parsed['bankCountry']) )
		                        <div class="row">
		                            <label for="country" class="col-md-4">{{ trans('user.payment_method.country_of_bank') }}</label>
		                            <div class="col-md-8">{{ Country::getCountryNameByCode($parsed['bankCountry']) }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['bankName']) )
		                        <div class="row">
		                            <label for="bankName" class="col-md-4">{{ trans('user.payment_method.bank_name') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['bankName']) ? $parsed['bankName'] : '' }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['bankBranch']) )
		                        <div class="row">
		                            <label for="bankBranch" class="col-md-4">{{ trans('user.payment_method.bank_branch') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['bankBranch']) ? $parsed['bankBranch'] : '' }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['ibanAccountNo']) )
		                        <div class="row">
		                            <label for="ibanAccountNo" class="col-md-4">{{ trans('user.payment_method.iban_account_no') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['ibanAccountNo']) ? $parsed['ibanAccountNo'] : '' }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['accountName']) )
		                        <div class="row">
		                            <label for="accountName" class="col-md-4">{{ trans('user.payment_method.account_name') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['accountName']) ? $parsed['accountName'] : '' }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['beneficiaryAddress1']) )
		                        <div class="row">
		                            <label for="beneficiaryAddress1" class="col-md-4">{{ trans('user.payment_method.beneficiary_address1') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['beneficiaryAddress1']) ? $parsed['beneficiaryAddress1'] : '' }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['beneficiaryAddress2']) )
		                        <div class="row">
		                            <label for="beneficiaryAddress2" class="col-md-4">{{ trans('user.payment_method.beneficiary_address2') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['beneficiaryAddress2']) ? $parsed['beneficiaryAddress2'] : '' }}</div>
		                        </div>
                                @endif
                                @if ( isset($parsed['beneficiarySwiftCode']) )
		                        <div class="row">
		                            <label for="beneficiarySwiftCode" class="col-md-4">{{ trans('user.payment_method.beneficiary_swift_code') }}</label>
		                            <div class="col-md-8">{{ isset($parsed['beneficiarySwiftCode']) ? $parsed['beneficiarySwiftCode'] : '' }}</div>
		                        </div>
                                @endif
		                    @endif
		                	</div>
		                </div>
                    </div><!-- .information -->
                    <div class="row">
                    	<div class="col-md-3 col-md-offset-3">
                    		<a class="btn-link"><span class="more">{{ trans('common.view_more') }} <i class="icon-arrow-down"></i></span><span class="less">{{ trans('common.view_less') }} <i class="icon-arrow-up"></i></span></a>
                    	</div>
                    </div>
                </div><!-- .additional -->
                @endif
            </li> 
            @endforeach
        </ul>
        @else
        <div class="not-found-result">
            <div class="heading">
            {{ trans('user.payment_method.message_no_payment_method') }}
            </div>
        </div>
        @endif
    </form>

    @if ( !$current_user->isSuspended() && !$current_user->isFinancialSuspended() )
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <a href="#modalPaymentGateway" class="btn btn-primary" data-toggle="modal" data-backdrop="static">{{ trans('user.payment_method.add_a_payment_method') }}</a>
        </div>
    </div>
    @endif

    @if ( !$current_user->isSuspended() && !$current_user->isFinancialSuspended() )
        @include('pages.user.payment_method.modal')
        @include('pages.user.payment_method.edit')
    @endif

    @include('pages.user.payment_method.view')
</div>

@endsection