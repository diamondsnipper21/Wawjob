<?php
/**
 * User Deposit Page (user/deposit)
 *
 * @author  - Ro Un Nam
 */
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\Settings;
?>
@extends('layouts/user/index')

@section('content')
<div class="title-section">
	<span class="title">
		<i class="icon-credit-card title-icon"></i>
		@if ( $action == 'previewDeposit' )
			{{ trans('page.buyer.user.deposit_preview.title') }}
		@else
			{{ trans('page.' . $page . '.title') }}
		@endif
	</span>
</div>
<div class="page-content-section buyer-user-deposit-page">

	{{ show_messages(false, false) }}

	@if ( $action == '' )
	<form id="form_user_request_deposit" method="post" action="{{ route('user.deposit')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="requestDeposit">

		<div class="sub-title">
			<span class="balance">{{ trans('common.balance') }}: {{ $wallet < 0 ? '($'.formatCurrency(abs($wallet)).')' : '$'.formatCurrency($wallet) }}</span>

			<button class="btn btn-primary" value="deposit_now" {{ !$current_user->depositPrimaryPaymentGateway || !$current_user->isAvailableAction(true) ? 'disabled' : ''}}>{{ trans('common.deposit_now') }}</button>
		</div>

		@if ( $holding_amount > 0 )
		<div class="holding pt-3 pl-4">{{ trans('common.in_holding_now_note', ['amount' => '$' . formatCurrency($holding_amount)]) }} <i class="icon icon-question ml-2" data-toggle="tooltip" data-placement="right" title="{{ trans('common.in_holding_now_reason') }}"></i></div>
		@endif

		<ul class="list-group">
			<li class="list-group-item">
				<div class="row">
					<div class="col-md-3">
						<strong>{{ trans('common.last_deposit') }}</strong>
					</div>
					<div class="col-md-9">
						@if ($last_payment)
							<div class="row-inner">
								<i class="fa fa-check-circle"></i> ${{ formatCurrency(abs($last_payment->amount)) }} {{ trans('common.on') }} {{ format_date($format_date2, $last_payment->created_at) }} {{ trans('common.from') }} 
								<img src="{{ $last_payment->gateway_logo() }}" class="img-responsive gateway-logo" height="24" /> - {{ $last_payment->gateway_string(false) }}
							</div>
						@else
							<p>{{ trans('common.n_a') }}</p>
						@endif
						<a href="{{ route('report.transactions') }}">{{ trans('report.view_all_transactions') }} <i class="fa fa-angle-right"></i></a>
					</div>
				</div>
			</li>    
		</ul>
	</form>
	@elseif ( $action == 'requestDeposit' )

	<form id="form_user_preview_deposit" method="post" action="{{ route('user.deposit')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="previewDeposit">

		<div class="pt-2 pl-4 pr-4 pb-2 mb-5 info">{!! trans('user.withdraw.withdraw_note') !!}</div>

		<div class="row">
			<div class="col-md-6">
				<div class="pl-4 pr-4">
					<label class="control-label">{{ trans('page.user.payment_method.title') }}</label>
					<div class="mt-2">
						@foreach ( $current_user->depositPaymentGateways as $upg )
						<div class="radiobox p-3">
							<label>
								<input type="radio" name="payment_gateway" id="payment_gateway{{ $upg->id }}" data-gateway="{{ $upg->gateway }}" data-fee="{{ $upg->depositFee() }}" value="{{ $upg->id }}" {{ $upg->id == $payment_gateway_id || $upg->isPrimary() ? 'checked="checked"' : ''}}>
								<span class="box-img w-30"><img src="{{ $upg->logo() }}" /></span>
								<span class="box-title w-60">{{ $upg->title() }}</span>
							</label>
						</div>
						@endforeach
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="border-left ml-3 pl-3">
					<div class="text-right ml-4 mr-4">
						<div class="row">
							<div class="col-sm-12">
								<label class="control-label">{{ trans('common.amount') }}</label>
							</div>
						</div>
					</div>
					<div class="border-top ml-4 mr-4 mb-3"></div>

					<div class="row form-group ml-4 mr-4">
						<label class="col-xs-6 control-label text-right amount-title">{{ trans('common.deposit_amount') }}</label>
						<div class="col-xs-6">
							<div class="input-group form-line-wrapper deposit-amount-field">
								<div class="input-group-addon">$</div>
								<input type="text" class="form-control text-right" id="deposit_amount" name="deposit_amount" data-rule-required="true" data-rule-number="true" data-rule-min="0.1" value="{{ $deposit_amount }}">
							</div>
						</div>
					</div>

					<div class="row form-group ml-4 mr-4">
						<label class="col-xs-6 control-label text-right">{{ trans('common.processing_fee') }} <span class="fee-tooltip ml-2"><i class="icon icon-question" data-toggle="tooltip" data-placement="top" title="{{ trans('user.deposit.tip_fee_of_deposit_amount') }}"></i></span></label>
						<div class="col-xs-6">
							<div class="info-div text-right"><span class="fee">$ 0.00</span></div>
						</div>
					</div>

					<div class="border-top ml-4 mr-4 mb-3"></div>

					<div class="row form-group ml-4 mr-4">
						<label class="col-xs-6 control-label text-right">{{ trans('common.total') }}</label>
						<div class="col-sm-6">
							<div class="info-div text-right">$ <span class="total">0.00</span></div>
						</div>
					</div>

					<div class="info pt-4 text-right gateway-info">
						<div class="pb-1">{{ trans('user.deposit.your_account_will_be_charged_in_currency', ['currency' => trans('common.cny')]) }}</div>
						<div>(1.00 {{ trans('common.usd') }} = {{ $cny_exchange_rate }} {{ trans('common.cny') }})</div>
					</div>
				</div>
			</div>
		</div>

		<div class="title-section title-blank"></div>

		<div class="text-right hidden gateway-info">{!! trans('user.deposit.you_are_going_to_deposit_amount', ['currency' => trans('common.cny')]) !!}</div>

		<div class="form-group pt-4 pl-4 text-right">
			<button type="submit" class="btn btn-primary">{{ trans('common.preview') }}</button>
			<a href="{{ route('user.deposit') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
		</div>
	</form>

	@elseif ( $action == 'previewDeposit' )

	<form id="form_user_deposit" method="post" action="{{ route('user.deposit') }}" data-qrcode-action="{{ route('user.deposit.wcqrcode.get') }}" data-wcpayment-action="{{ route('user.deposit.wcpayment') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="deposit">
		<input type="hidden" name="deposit_amount" value="{{ $deposit_amount }}">
		<input type="hidden" name="payment_gateway" value="{{ $payment_gateway->id }}">
		<input type="hidden" name="wechat_queue_id" value="{{ $wechat_queue_id }}">
		<input type="hidden" name="gateway" value="{{ $payment_gateway->gateway }}">
		<input type="hidden" name="_tokenCSE" value="" />

		<div class="row form-group">
			<label class="col-sm-3 control-label text-right">{{ trans('common.deposit_from') }}</label>
			<div class="col-sm-9">
				<div class="info-div">
					<img src="{{ $payment_gateway->logo() }}" class="img-responsive gateway-logo" /> - {{ $payment_gateway->title() }}
				</div>
			</div>
		</div>

		@if ( !$payment_gateway->paymentGateway->isWeixin() )
		<div class="row form-group">
			<label class="col-sm-3 control-label text-right">{{ trans('common.total_amount') }}</label>
			<div class="col-sm-9">
				<div class="info-div">${{ formatCurrency($deposit_amount + $fee) }}</div>
			</div>
		</div>
		@endif

		@if ( $payment_gateway->paymentGateway->isWeixin() )
		<div class="row form-group pt-5">
			<div class="col-sm-9 col-sm-offset-3 fs-16">{!! trans('user.deposit.you_are_depositing_amount', ['currency' => trans('common.cny'), 'amount' => formatCurrency(($deposit_amount + $fee) * $cny_exchange_rate)]) !!}</div>
		</div>

		<div class="row form-group box-waiting-qrcode hide">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="pt-5 mb-3">{!! trans('user.deposit.waiting_qrcode_description') !!}</div>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="pt-5 mb-3">{!! trans('user.deposit.scan_qrcode_description') !!}</div>
				<div class="mb-5 box-description-qrcode hide">{!! trans('user.deposit.scan_qrcode_description2') !!}</div>
				<div class="mb-5"><img id="qrcode" src="{{ url('/assets/images/common/payments/wechat_ijobdesk.png') }}" width="200" /></div>
			</div>
		</div>

		@elseif ( $payment_gateway->paymentGateway->isSkrill() )
		<div class="row form-group">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="pt-5 pb-5">
					{!! trans('user.deposit.manual_deposit_instruction', ['gateway' => 'Skrill', 'address' => Settings::get('SKRILL_MERCHANT_EMAIL'), 'url' => 'https://www.skrill.com']) !!}
				</div>
			</div>
		</div>
		@elseif ( $payment_gateway->paymentGateway->isPayoneer() )
		<div class="row form-group">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="pt-5 pb-5">
					{!! trans('user.deposit.manual_deposit_instruction', ['gateway' => 'Payoneer', 'address' => Settings::get('PAYONEER_EMAIL'), 'url' => 'https://www.payoneer.com']) !!}
				</div>
			</div>
		</div>
		@elseif ( $payment_gateway->paymentGateway->isWireTransfer() )
		<div class="row form-group">
			<label class="col-sm-3 control-label text-right">{{ trans('common.to_cap') }}</label>
			<div class="col-sm-8">
				<div class="border p-3">
					<label class="control-label">{{ trans('user.payment_method.receiving_account_ach') }}</label>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.bank_name') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_NAME') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.aba_routing_number') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_ROUTING_NUMBER') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.account_number') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_ACCOUNT_NUMBER') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.beneficiary_name') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_BENEFICIARY_NAME') }}</div>
					</div>
				</div>

				<div class="border-left border-right border-bottom p-3">
					<label class="control-label">{{ trans('user.payment_method.receiving_account_sepa') }}</label>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.bank_name') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_NAME_EURO') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.bank_address') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_ADDRESS') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.bic') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_BIC') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.iban') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_IBAN') }}</div>
					</div>
					<div class="row mb-1">
						<div class="col-sm-4">{{ trans('user.payment_method.beneficiary_name') }}</div>
						<div class="col-sm-8">{{ Settings::get('BANK_BENEFICIARY_NAME') }}</div>
					</div>
				</div>

				<div class="border-left border-right border-bottom p-3">
					<label class="control-label">{{ trans('common.reference') }}</label>
					<div mb-1>{{ trans('user.deposit.wire_deposit_to') }} {{ Settings::get('BANK_REFERENCE') }}</div>
					<div mb-1>{{ trans('common.username') }}: {{ Settings::get('BANK_REFERENCE_USER') }}</div>
					<div mb-1>{{ trans('common.id') }}: {{ mt_rand(10000000, 99999999) }}</div>
				</div>

				<div class="border-left border-right border-bottom p-3 mb-4">
					<label class="control-label">{{ trans('user.deposit.amount_to_send') }}</label>
					<div>${{ formatCurrency($deposit_amount) }}</div>
				</div>

				<div class="mb-4">
					<p>{{ trans('user.deposit.bank_deposit_instruction') }}</p>
					<p>{{ trans('user.deposit.bank_deposit_instruction2') }}</p>
				</div>

				<div class="mb-4 form-group">
					<label class="control-label">{{ trans('user.deposit.date_of_deposit') }}<span class="form-required">&nbsp;*</span></label>
					<div class="input-group w-60">
						<input type="text" class="form-control" data-rule-required="true" id="deposit_date" name="deposit_date" value="">
						<span class="input-group-addon date-picker">
						 	<i class="icon-calendar"></i>
						</span>
					</div>
				</div>

				<div class="mb-4 form-group">
					<label class="control-label">{{ trans('user.deposit.deposit_reference') }}<span class="form-required">&nbsp;*</span> <i class="icon icon-question" data-toggle="tooltip" data-placement="top" title="{{ trans('user.deposit.deposit_reference_tooltip') }}"></i></label>
					<div class="w-60">
						<input type="text" class="form-control" data-rule-required="true" id="deposit_reference" name="deposit_reference" value="">
					</div>
				</div>

				<div class="mt-2">
					<p>{{ trans('user.deposit.bank_deposit_note') }}</p>
				</div>
			</div>
		</div>
		@endif

		<div class="title-section title-blank"></div>

		<div class="form-group pt-4">
			<a href="{{ route('user.deposit') }}" class="btn btn-link pull-left"><i class="fa fa-angle-left"></i> {{ trans('common.back') }}</a>
			<button type="button" class="btn btn-primary btn-deposit pull-right">{{ $payment_gateway->paymentGateway->isPayPal() ? trans('common.confirm_deposit') : trans('common.complete_deposit') }}</button>
			<div class="clearfix"></div>
		</div>
	</form>

	@else

	<div class="form-group">
		<a class="btn btn-primary" href="{{ route('user.deposit')}}">{{ trans('common.back') }}</a>
	</div>

	@endif

</div>

@endsection