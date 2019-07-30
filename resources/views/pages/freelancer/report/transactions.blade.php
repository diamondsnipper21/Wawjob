<?php
/**
 * Transactions Page (report/transactions)
 *
 * @author Ro Un Nam
 * @since Jun 11, 2017
 */

use iJobDesk\Models\TransactionLocal;
?>
@extends($current_user->isAdmin()?'layouts/admin/super/user':'layouts/default/index')

@section('content')
<script type="text/javascript">
@if (isset($dates))
	var date_from = '{{ date("Y-m-d", strtotime($dates['from'])) }}';
	var date_to   = '{{ date("Y-m-d", strtotime($dates['to'])) }}';
@endif
</script>
<div class="title-section">
	<div class="row">
		<div class="col-sm-8">
			<span class="title">
				<span>{{ trans('page.' . $page . '.title') }}</span>
				<span class="admin-title hide caption-subject font-green-sharp bold"><i class="fa fa-calculator font-green-sharp"></i>&nbsp;Transations History</span>
			</span>
		</div>

		<div class="col-sm-4 text-right">
			<label>{{ trans('common.balance') }}: 
				<span>
				@if ( $balance >= 0 )
					${{ formatCurrency($balance) }}
				@else
					(${{ formatCurrency(abs($balance)) }})
				@endif
				</span>
			</label>
		</div>
	</div>
</div>
<div class="page-content-section freelancer-report-page report-transactions-page">
	<div class="filter-section">
		<form id="from_transactions_filter" method="post">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="row">
				<div class="col-md-4 col-sm-5">
					<div class="date-filter-section form-group">
						<div class="date-filter">
							@if ($prev)
							<a class="btn btn-link prev-unit" data-range="{{ $prev }}"><i class="fa fa-angle-left"></i></a>
							@endif
							<div class="input-group" id="date_range">
								<input type="text" class="form-control" name="date_range" value="{{ date($format_date2, strtotime($dates['from'])) . ' - ' . date($format_date2, strtotime($dates['to'])) }}">
								<span class="input-group-btn">
									<button class="btn default date-range-toggle" type="button"><i class="fa icon-calendar"></i></button>
								</span>
							</div>
							@if ($next)
							<a class="btn btn-link next-unit" data-range="{{ $next }}"><i class="fa fa-angle-right"></i></a>
							@endif
						</div>

						<div class="clearfix"></div>
					</div>
				</div>

				<div class="col-md-5 col-sm-3 col-xs-6">
					<div class="contract-filter-section">
						@include('pages.report.section.contract_selector')
					</div>
				</div>

				<div class="col-md-3 col-sm-4 col-xs-6">
					<button type="submit" class="btn btn-primary pull-right ml-2">{{ trans('common.go') }}</button>
					
					<div class="transaction-type-section pull-right w-75">
						<select class="form-control select2 " id="transaction_type" name="transaction_type" placeholder="{{ trans('report.all_transactions') }}">
							<option value="" {{ $type == '' ? 'selected' : '' }}>{{ trans('report.all_transactions') }}</option>
							<option value="{{ TransactionLocal::TYPE_FIXED }}" {{ $type == (string)TransactionLocal::TYPE_FIXED ? 'selected' : '' }}>{{ trans('common.fixed_price') }}</option>
							<option value="{{ TransactionLocal::TYPE_HOURLY }}" {{ $type == (string)TransactionLocal::TYPE_HOURLY ? 'selected' : '' }}>{{ trans('common.hourly') }}</option>
							<option value="{{ TransactionLocal::TYPE_BONUS }}" {{ $type == (string)TransactionLocal::TYPE_BONUS ? 'selected' : '' }}>{{ trans('common.bonus') }}</option>
							<option value="{{ TransactionLocal::TYPE_CHARGE }}" {{ $type == (string)TransactionLocal::TYPE_CHARGE ? 'selected' : '' }}>{{ trans('common.deposit') }}</option>
							<option value="{{ TransactionLocal::TYPE_WITHDRAWAL }}" {{ $type == (string)TransactionLocal::TYPE_WITHDRAWAL ? 'selected' : '' }}>{{ trans('common.withdrawal') }}</option>
							<option value="{{ TransactionLocal::TYPE_REFUND }}" {{ $type == (string)TransactionLocal::TYPE_REFUND ? 'selected' : '' }}>{{ trans('common.refund') }}</option>
							<option value="{{ TransactionLocal::TYPE_AFFILIATE }}" {{ $type == (string)TransactionLocal::TYPE_AFFILIATE ? 'selected' : '' }}>{{ trans('common.affiliate') }}</option>
						</select>
					</div>
				</div>
			</div><!-- .row -->
		</form>
	</div><!-- .filter-section -->

	@include ('pages.freelancer.report.section.transactions_section')

	@include ('pages.freelancer.report.section.transactions_statement')

</div>
@endsection