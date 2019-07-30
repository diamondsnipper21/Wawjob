<?php
/**
 * User Withdraw Page (user/withdraw)
 *
  * @author  - Daniel Lu
 */
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\Settings;

$min_withdraw = Settings::get('WITHDRAW_MIN_AMOUNT');
$max_withdraw = Settings::get('WITHDRAW_MAX_AMOUNT');
?>
@extends('layouts/user/index')

@section('content')
<div class="title-section">
	<span class="title">
		<i class="icon-wallet title-icon"></i>
		@if ( $action == 'previewGetPaid' )
			{{ trans('page.user.withdraw_preview.title') }}
		@else
			{{ trans('page.' . $page . '.title') }}
		@endif
	</span>
</div>
<div class="page-content-section user-withdraw-page">

	{{ show_messages(false, false) }}

	@if ( $action == '' )

	<form id="form_user_request_get_paid" method="post" action="{{ route('user.withdraw')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="requestGetPaid">

		<div class="sub-title">
			<span class="balance">{{ trans('common.balance') }}: {{ $wallet < 0 ? '($'.formatCurrency(abs($wallet)).')' : '$'.formatCurrency($wallet) }}</span>

			<button class="btn btn-primary" value="get_paid_now" {{ !$current_user->isAvailableWithdraw() ? 'disabled' : '' }}>{{ trans('common.get_paid_now') }}</button>
		</div>

		@if ( $holding_amount > 0 )
		<div class="holding pt-3 pl-4">{{ trans('common.in_holding_now_note', ['amount' => '$' . formatCurrency($holding_amount)]) }} <i class="icon icon-question ml-2" data-toggle="tooltip" data-placement="right" title="{{ trans('common.in_holding_now_reason') }}"></i></div>
		@endif

		<ul class="list-group">
			<li class="list-group-item">
				<div class="row">
					<div class="col-md-3">
						<strong>{{ trans('common.last_payment') }}</strong>
					</div>
					<div class="col-md-9">
						@if ($last_payment)
							<div class="row-inner">
								<i class="icon-check"></i> 
								${{ formatCurrency(abs($last_payment->amount + ($last_payment->reference ? $last_payment->reference->amount : 0))) }} {{ trans('common.on') }} {{ format_date($format_date2, $last_payment->created_at) }} {{ trans('common.to') }} 
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

	<div class="info footer-note">{!! trans('user.withdraw.withdraw_footer_note') !!}</div>

	@elseif ( $action == 'requestGetPaid' )

	<form id="form_user_preview_get_paid" method="post" action="{{ route('user.withdraw')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="previewGetPaid">

		<div class="pt-2 pl-4 pr-4 pb-2 mb-5 info">{!! trans('user.withdraw.withdraw_note') !!}</div>

		<div class="row">
			<div class="col-md-6">
				<div class="pl-4 pr-4">
					<label class="control-label">{{ trans('page.user.payment_method.title') }}</label>
					
					<div class="mt-2">
						@if ( $current_user->totalActiveWithdrawPaymentGateways() < 1 )
						<div class="pb-4 pl-4 info border-bottom">
							{!! trans('user.withdraw.message_no_available_payment_method') !!}<br />
							{!! trans('user.withdraw.click_here_to_add_payment_method', ['url' => route('user.payment_method')]) !!}
						</div>
						@endif

						@foreach ( $current_user->activePaymentGatewaysOrderWithdraw() as $upg )
							@if ( $upg->isEnabledWithdraw() )
							<?php
								$fee = $upg->withdrawFee();
								$fee_fixed = $upg->withdrawFixedFee();
								if ( $balance < $min_withdraw ) {
									continue;
								}

								$enable_withdraw = $upg->paymentGateway->enable_withdraw;
							?>
							<div class="radiobox p-3 {{ !$enable_withdraw ? 'disabled' : ''}}">
								<label>
									<input type="radio" {{ !$enable_withdraw ? 'disabled' : '' }} name="payment_gateway" id="payment_gateway{{ $upg->id }}" value="{{ $upg->id }}" {{ $enable_withdraw && ($upg->id == $payment_gateway_id || $upg->isPrimary()) ? 'checked' : ''}} data-maximum="{{ $current_user->isBuyer() ? $upg->depositAmount() : $max_withdraw }}" data-fee="{{ $fee }}" data-fee-fixed="{{ $fee_fixed }}" data-gateway-label="{{ parse_json_multilang($upg->paymentGateway->name) }}" data-gateway="{{ $upg->gateway }}">
									<span class="box-img w-30"><img src="{{ $upg->logo() }}" /></span>
									<span class="box-title w-60">{{ $upg->title() }}</span>
								</label>
							</div>
							@endif
						@endforeach
					</div>

					<div class="info mt-5 ml-3">{!! trans('user.withdraw.withdraw_footer_note') !!}</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="border-left ml-3 pl-3">
					<div class="row ml-4 mr-4 form-group text-right">
						<label class="col-xs-6 control-label">{{ trans('common.available_balance') }}</label>
						<div class="col-xs-6">
							<div class="info-div current-balance" data-balance="{{ $balance }}" data-min="{{ $min_withdraw }}" data-max="{{ $max_withdraw }}">${{ formatCurrency($balance) }}</div>
						</div>
					</div>

					<div class="row ml-4 mr-4 form-group text-right">
						<label class="col-xs-6 control-label amount-title">{{ trans('common.withdrawal_amount') }}</label>
						<div class="col-xs-6">
							<div class="input-group form-line-wrapper withdraw-amount-field">
								<div class="input-group-addon">$</div>
								<input type="text" class="form-control text-right" id="withdraw_amount" name="withdraw_amount" data-rule-required="true" data-rule-number="true" data-rule-min="{{ $min_withdraw }}" data-rule-max="{{ $max_withdraw }}" value="{{ $withdraw_amount }}">
							</div>

							@if ( $current_user->isBuyer() )
							<div class="mt-2">{{ trans('common.maximum') }}: $<span class="maximum">0.00</span></div>
							@endif
						</div>
					</div>

					<div class="row ml-4 mr-4 form-group text-right">
						<label class="col-xs-6 control-label">{{ trans('common.processing_fee') }} <span class="fee-tooltip ml-2"><i class="icon icon-question" data-toggle="tooltip" data-placement="top" title="{{ trans('user.withdraw.tip_fee_of_withdraw_amount') }}"></i></span></label>
						<div class="col-xs-6">
							<div class="info-div">
								<span class="fee">$ 0.00</span>
								<span class="per-payment fs-12">{{ trans('common.per_payment') }}</span>
							</div>
						</div>
					</div>

					<div class="info pt-5 pl-3 text-right hidden wechat-gateway-info">{{ trans('user.withdraw.gateway_note', ['currency' => trans('common.cny'), 'rate' => $cny_exchange_rate]) }}</div>

					<div class="info pt-5 pl-3 text-right hidden payoneer-gateway-info">
						<div class="pb-1">{{ trans('user.withdraw.you_will_get_paid_in_currency', ['currency' => trans('common.eur')]) }}</div>
						<div>(1.00 {{ trans('common.usd') }} = {{ $eur_exchange_rate }} {{ trans('common.eur') }})</div>
					</div>

					{{--
					@if ( $current_user->isBuyer() )
					<div class="info mt-5 ml-3 note-paypal hide">{!! trans('user.withdraw.withdraw_paypal_note') !!}</div>
					@endif
					--}}
				</div>
			</div>
		</div>

		<div class="title-section title-blank"></div>

		<div class="text-right gateway-info">{!! trans('user.withdraw.you_are_going_to_send_money_to_gateway') !!}</div>

		<div class="form-group pt-4 pl-4 text-right">
			<button type="submit" class="btn btn-preview btn-primary">{{ trans('common.preview') }}</button>
			<a href="{{ route('user.withdraw') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
		</div>
	</form>

	@elseif ( $action == 'previewGetPaid' )

	<form id="form_user_withdraw" method="post" action="{{ route('user.withdraw')}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_action" value="withdraw">
		<input type="hidden" name="withdraw_amount" value="{{ $withdraw_amount }}">
		<input type="hidden" name="payment_gateway" value="{{ $payment_gateway->id }}">

		<div class="pt-2 pl-4 pr-4 pb-2 mb-5 info">{!! trans('user.withdraw.withdraw_note') !!}</div>

		<div class="row form-group">
			<label class="col-sm-3 control-label text-right">{{ trans('common.payment_method') }}</label>
			<div class="col-sm-9">
				<div class="info-div">
					<img src="{{ $payment_gateway->logo() }}" class="img-responsive gateway-logo" /> - 
					{{ $payment_gateway->title() }} 
					{!! $payment_gateway->info() !!}
				</div>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-sm-3 control-label text-right">{{ trans('common.withdrawal_amount') }}</label>
			<div class="col-sm-9">
				<div class="info-div">${{ formatCurrency($withdraw_amount) }}</div>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-sm-3 control-label text-right">{{ trans('common.processing_fee') }}</label>
			<div class="col-sm-9">
				<div class="info-div">{{ $withdraw_fee > 0 ? '$' . formatCurrency($withdraw_fee) : trans('common.free') }} <span class="pl-4 fs-14{{ $withdraw_fee <= 0 ? ' hide' : ''}}">{{ trans('common.per_payment') }}</span></div>
			</div>
		</div>

		@if ( $payment_gateway->paymentGateway->isWeixin() )
		<div class="row form-group">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="pt-3 wechat-gateway-info">{{ trans('user.withdraw.gateway_note', ['currency' => trans('common.cny'), 'rate' => $cny_exchange_rate]) }}</div>
			</div>
		</div>
		@elseif ( $payment_gateway->paymentGateway->isPayoneer() )
		<div class="row form-group">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="pt-3 payoneer-gateway-info">{{ trans('user.withdraw.gateway_note', ['currency' => trans('common.eur'), 'rate' => $eur_exchange_rate]) }}</div>
			</div>
		</div>
		@endif

		{{--
		@if ( $current_user->isBuyer() && $payment_gateway->paymentGateway->isPayPal() )
		<div class="pt-2 pl-4 info">{!! trans('user.withdraw.withdraw_paypal_note') !!}</div>
		@endif
		--}}

		<div class="title-section title-blank"></div>

		<div class="text-right gateway-info">
		@if ( $payment_gateway->paymentGateway->isWeixin() )
			{!! trans('user.withdraw.you_are_going_to_send_money_to_gateway', ['amount' => formatCurrency(($withdraw_amount - $withdraw_fee) * $cny_exchange_rate), 'gateway' => parse_json_multilang($payment_gateway->paymentGateway->name) . ' - ' . $payment_gateway->title(), 'currency' => trans('common.cny') . ' ']) !!}
		@elseif ( $payment_gateway->paymentGateway->isPayoneer() )
			{!! trans('user.withdraw.you_are_going_to_send_money_to_gateway', ['amount' => formatCurrency(($withdraw_amount - $withdraw_fee) * $eur_exchange_rate), 'gateway' => parse_json_multilang($payment_gateway->paymentGateway->name) . ' - ' . $payment_gateway->title(), 'currency' => trans('common.eur') . ' ']) !!}
		@else
			{!! trans('user.withdraw.you_are_going_to_send_money_to_gateway', ['amount' => formatCurrency($withdraw_amount - $withdraw_fee), 'gateway' => parse_json_multilang($payment_gateway->paymentGateway->name) . ' - ' . $payment_gateway->title(), 'currency' => '$']) !!}
		@endif
		</div>

		<div class="form-group pt-4">
			<button type="button" class="btn btn-link btn-back-get-paid pull-left"><i class="fa fa-angle-left"></i> {{ trans('common.back') }}</button>
			<button type="button" class="btn btn-primary btn-withdraw pull-right">{{ trans('common.get_paid') }}</button>
			<div class="clearfix"></div>
		</div>
	</form>

	@else

	<div class="form-group">
		<a class="btn btn-primary" href="{{ route('user.withdraw')}}">{{ trans('common.back') }}</a>
	</div>  

	@endif

</div>

@endsection