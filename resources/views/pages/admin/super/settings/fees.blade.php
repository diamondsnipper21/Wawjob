<?php
/**
* Fees on Super Admin
*/

use iJobDesk\Models\Settings;
?>
@extends('layouts/admin/super')

@section('content')
<script type="text/javascript">
	var url_change_chinese_rate = '{{ route('admin.super.settings.fees.refresh_chinese_money_rate') }}';
</script>

<div id="fees">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Fees and Charges</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-horizontal" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	    		<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row">
    				<div class="col-md-8">
						<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Fee for all types of Contracts</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="fee_rate" name="fee_rate" data-rule-required="true" value="{{ Settings::get('FEE_RATE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div><!-- Fee Rate -->

						<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Fee for all types of affiliated Contracts</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="fee_rate_affiliated" name="fee_rate_affiliated" data-rule-required="true" value="{{ Settings::get('FEE_RATE_AFFILIATED') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div><!-- Fee Rate Affiliated -->

						<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Fee for Featured Job Posting</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="featured_job_fee" name="featured_job_fee" data-rule-required="true" value="{{ Settings::get('FEATURED_JOB_FEE') }}">
								</div>
							</div>
						</div><!-- Featured Job Fee -->

						<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Connections Required for Featured Job</label>
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control" id="connections_featured_project" name="connections_featured_project" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('CONNECTIONS_FEATURED_PROJECT') }}">
							</div>
						</div>
						<hr>

						<!--<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Commission for 1st Affiliate - Buyer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="affiliate_buyer_fee" name="affiliate_buyer_fee" data-rule-required="true" value="{{ Settings::get('AFFILIATE_BUYER_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>--><!-- Affiliate Amount for Buyer -->

						<!--<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Commission for 2nd Affiliate - Buyer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="affiliate_child_buyer_fee" name="affiliate_child_buyer_fee" data-rule-required="true" value="{{ Settings::get('AFFILIATE_CHILD_BUYER_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div><hr>--><!-- Child Affiliate Amount for Buyer -->

						<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Commission for 1st Affiliate - Freelancer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="affiliate_freelancer_fee_rate" name="affiliate_freelancer_fee_rate" data-rule-required="true" value="{{ Settings::get('AFFILIATE_FREELANCER_FEE_RATE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div><!-- Affiliate Fee for Freelancer -->

						<div class="form-group">
							<div class="col-md-6 col-md-offset-2">
								<label class="control-label">Commission for 2nd Affiliate - Freelancer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="affiliate_child_freelancer_fee_rate" name="affiliate_child_freelancer_fee_rate" data-rule-required="true" value="{{ Settings::get('AFFILIATE_CHILD_FREELANCER_FEE_RATE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div><!-- Child Affiliate Fee for Freelancer -->

						<hr>
						<div class="form-group">
							<div class="col-md-3 col-md-offset-2">
								<label class="control-label">Exchange Rate</label>
							</div>
							<div class="col-md-3 text-right">
								<label class="control-label">Buy (When deposit)</label>
							</div>
							<div class="col-md-4">
								<div class="input-group form-line-wrapper">
									<span class="input-group-addon"><a href="#" class="chinese-rate-refresh" data-toggle="tooltip" title="Refresh chinese rate automatically" data-params="max,5"><i class="icon-refresh"></i></a></span>
									<input type="text" class="form-control" id="cny_exchange_rate" name="cny_exchange_rate" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('CNY_EXCHANGE_RATE') }}">
									<div class="input-group-addon">CNY = 1 USD</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3 col-md-offset-2">
							</div>
							<div class="col-md-3 text-right">
								<label class="control-label">Sell (When withdrawal)</label>
							</div>
							<div class="col-md-4">
								<div class="input-group form-line-wrapper">
									<span class="input-group-addon"><a href="#" class="chinese-rate-refresh" data-toggle="tooltip" title="Refresh chinese rate automatically" data-params="min,-5"><i class="icon-refresh"></i></a></span>
									<input type="text" class="form-control" id="cny_exchange_rate_sell" name="cny_exchange_rate_sell" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('CNY_EXCHANGE_RATE_SELL') }}">
									<div class="input-group-addon">CNY = 1 USD</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3 col-md-offset-2">
							</div>
							<div class="col-md-3 text-right">
								<label class="control-label">Sell (When withdrawal)</label>
							</div>
							<div class="col-md-4">
								<div class="input-group form-line-wrapper">
									<span class="input-group-addon"><a href="#" class="chinese-rate-refresh" data-toggle="tooltip" title="Refresh chinese rate automatically" data-params="min,-5"><i class="icon-refresh"></i></a></span>
									<input type="text" class="form-control" id="eur_exchange_rate_sell" name="eur_exchange_rate_sell" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('EUR_EXCHANGE_RATE_SELL') }}">
									<div class="input-group-addon">EUR = 1 USD</div>
								</div>
							</div>
						</div>
						
						<hr>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
								<label class="control-label">Deposit Transaction Fees</label>
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">PayPal</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="deposit_fee_paypal" name="deposit_fee_paypal" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('DEPOSIT_FEE_PAYPAL') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">Skrill</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="deposit_fee_skrill" name="deposit_fee_skrill" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('DEPOSIT_FEE_SKRILL') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">Payoneer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="deposit_fee_payoneer" name="deposit_fee_payoneer" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('DEPOSIT_FEE_PAYONEER') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>

						<hr>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
								<label class="control-label">Withdrawal Transaction Fees</label>
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">PayPal</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="withdraw_paypal_fee" name="withdraw_paypal_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_PAYPAL_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="withdraw_paypal_fixed_fee" name="withdraw_paypal_fixed_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_PAYPAL_FIXED_FEE') }}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">Skrill</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="withdraw_skrill_fee" name="withdraw_skrill_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_SKRILL_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="withdraw_skrill_fixed_fee" name="withdraw_skrill_fixed_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_SKRILL_FIXED_FEE') }}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">Payoneer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="withdraw_payoneer_fee" name="withdraw_payoneer_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_PAYONEER_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="withdraw_payoneer_fixed_fee" name="withdraw_payoneer_fixed_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_PAYONEER_FIXED_FEE') }}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">WeChat</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="withdraw_wechat_fee" name="withdraw_wechat_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_WECHAT_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="withdraw_wechat_fixed_fee" name="withdraw_wechat_fixed_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_WECHAT_FIXED_FEE') }}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">Credit Card</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<input type="text" class="form-control" id="withdraw_creditcard_fee" name="withdraw_creditcard_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_CREDITCARD_FEE') }}">
									<div class="input-group-addon">%</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="withdraw_creditcard_fixed_fee" name="withdraw_creditcard_fixed_fee" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('WITHDRAW_CREDITCARD_FIXED_FEE') }}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 col-md-offset-2">
							</div>
							<div class="col-md-2 text-right">
								<label class="control-label">Bank Transfer</label>
							</div>
							<div class="col-md-2">
								<div class="input-group form-line-wrapper">
									<div class="input-group-addon">$</div>
									<input type="text" class="form-control" id="withdraw_bank_fee" name="withdraw_bank_fee" data-rule-required="true" value="{{ Settings::get('WITHDRAW_BANK_FEE') }}">
								</div>
							</div>
						</div><!-- Bank Withdraw Fee -->

						<br />
						<br />
						<div class="form-group">
							<div class="col-md-4 col-md-offset-8">
								<button type="button" class="btn blue button-submit">Update</button>
							</div>
						</div>
					</div>
				</form>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>

</div>

@endsection