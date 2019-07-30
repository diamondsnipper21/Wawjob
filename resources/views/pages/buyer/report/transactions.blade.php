<?php
/**
 * Transactions Page (report/transactions)
 *
 * @author  - nada
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
	<span class="title">
		<span>{{ trans('page.' . $page . '.title') }}</span>
		<span class="admin-title hide caption-subject font-green-sharp bold"><i class="fa fa-calculator font-green-sharp"></i>&nbsp;Transations History</span>
	</span>
	<div class="balance pull-right">
		{{ trans('common.balance') }} : 
		@if ( $balance >= 0 )
			${{ formatCurrency($balance) }}
		@else
			(${{ formatCurrency(abs($balance)) }})
		@endif		
	</div>
	<div class="clearfix"></div>
</div>
<div class="page-content-section buyer-report-page report-transactions-page">
	<div class="filter-section clearfix">
		<form id="frm_transactions_filter" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			{{ show_messages() }}
			
			<div class="row">
				<div class="col-md-4 col-sm-5 col-xs-12">
					<div class="date-filter-section form-group pull-left">
						<div class="date-filter">
							@if ($prev)
							<a class="prev-unit" href="#" data-range="{{ $prev }}"><i class="glyphicon glyphicon-chevron-left"></i></a>
							@endif
							<div class="input-group" id="date_range">
								<input type="text" class="form-control" name="date_range" value="{{ date($format_date2, strtotime($dates['from'])) . ' - ' . date($format_date2, strtotime($dates['to'])) }}">
								<span class="input-group-btn">
									<button class="btn default date-range-toggle" type="button"><i class="fa icon-calendar"></i></button>
								</span>
							</div>
							@if ($next)
							<a class="next-unit" href="#" data-range="{{ $next }}"><i class="glyphicon glyphicon-chevron-right"></i></a>
							@endif
						</div>
					</div>
				</div>
				<div class="col-md-5 col-sm-3 col-xs-6">
					<div class="contract-filter-section">
						@include("pages.report.section.contract_selector")
					</div>
				</div>
				<div class="col-md-3 col-sm-4 col-xs-6">
					<div class="transaction-type-section clearfix">
						<button type="submit" class="btn btn-primary pull-right ml-2">{{ trans('common.go') }}</button>
						
						<div class="section-content pull-right w-75">
							<select class="form-control select2" id="transaction_type" name="transaction_type" placeholder="Transaction Type">
								<option value="" {{ $type==''? 'selected':'' }}>{{ trans('report.all_transactions') }}</option>
								<option value="{{ TransactionLocal::TYPE_FIXED }}" {{ $type==(string)TransactionLocal::TYPE_FIXED? 'selected':'' }}>{{ trans('common.fixed_price') }}</option>
								<option value="{{ TransactionLocal::TYPE_HOURLY }}" {{ $type==(string)TransactionLocal::TYPE_HOURLY? 'selected':'' }}>{{ trans('common.hourly') }}</option>
								<option value="{{ TransactionLocal::TYPE_BONUS }}" {{ $type==(string)TransactionLocal::TYPE_BONUS? 'selected':'' }}>{{ trans('common.bonus') }}</option>
								<option value="{{ TransactionLocal::TYPE_CHARGE }}" {{ $type==(string)TransactionLocal::TYPE_CHARGE? 'selected':'' }}>{{ trans('common.deposit') }}</option>
								<option value="{{ TransactionLocal::TYPE_WITHDRAWAL }}" {{ $type==(string)TransactionLocal::TYPE_WITHDRAWAL? 'selected':'' }}>{{ trans('common.withdrawal') }}</option>
								<option value="{{ TransactionLocal::TYPE_REFUND }}" {{ $type==(string)TransactionLocal::TYPE_REFUND? 'selected':'' }}>{{ trans('common.refund') }}</option>
								<option value="{{ TransactionLocal::TYPE_AFFILIATE }}" {{ $type == (string)TransactionLocal::TYPE_AFFILIATE ? 'selected' : '' }}>{{ trans('common.affiliate') }}</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div><!-- END OF .filter-section -->

	<div class="transactions-section table-scrollable">
		<div class="table">
			<div class="thead">
				<div class="tr">
					<div class="th rp-ref" style="width:8%">{{ trans('common.ref_id') }}</div>
					<div class="th rp-date" style="width:10%">{{ trans('common.date') }}</div>
					<div class="th rp-type" style="width:12%">{{ trans('common.type') }}</div>
					<div class="th rp-description" style="width:46%">{{ trans('common.description') }}</div>
					<div class="th rp-freelancer" style="width:12%">{{ trans('common.freelancer') }}</div>
					<div class="th rp-amount text-right" style="width:12%">{{ trans('common.amount') }}</div>
				</div>
			</div>
			<div class="tbody">
				@forelse ($transactions as $t)
					@include ('pages.buyer.report.section.transaction_row')
				@empty
				<div class="not-found-result">
		            <div class="row">
		                <div class="col-md-12 text-center">
		                    <div class="heading">{{ trans('common.no_transactions') }}</div>
		                </div>
		            </div>
		        </div>
				@endforelse
			</div>
		</div>
	</div><!-- END OF .transactions-section -->

	<div class="statement-section row">
		<div class="col-md-offset-8 col-md-4 col-sm-offset-6 col-sm-6 col-xs-12">
			<div class="statement-label text-right">
				<div><strong>{{ trans('report.statement_period') }}</strong></div>
				<div>{{ date($format_date2, strtotime($dates['from'])) . ' - ' . date($format_date2, strtotime($dates['to'])) }}</div>
			</div>
			<div class="statement-content">
				<table>
					<tbody>
						<tr>
							<td class="info-label"><strong>{{ trans('report.beginning_balance') }}</strong></td>
							<td class="amount"><strong>
								{{ $statement['beginning'] < 0 ? '(' . formatCurrency(abs($statement['beginning']), $currency_sign) . ')' : formatCurrency($statement['beginning'], $currency_sign) }}
							</strong></td>
						</tr>
						<tr>
							<td class="info-label">{{ trans('report.total_debits') }}</td>
							<td class="amount">
								{{ $statement['debits'] < 0 ? '(' . formatCurrency(abs($statement['debits']), $currency_sign) . ')' : formatCurrency($statement['debits'], $currency_sign) }}
							</td>
						</tr>
						<tr>
							<td class="info-label">{{ trans('report.total_credits') }}</td>
							<td class="amount">
								{{ $statement['credits'] < 0 ? '(' . formatCurrency(abs($statement['credits']), $currency_sign) . ')' : formatCurrency($statement['credits'], $currency_sign) }}
							</td>
						</tr>
						<tr>
							<td class="info-label">{{ trans('report.total_change') }}</td>
							<td class="amount">
								{{ $statement['change'] < 0 ? '(' . formatCurrency(abs($statement['change']), $currency_sign) .')' : formatCurrency($statement['change'], $currency_sign) }}
							</td>
						</tr>
						<tr>
							<td class="info-label last"><strong>{{ trans('report.ending_balance') }}</strong></td>
							<td class="amount last"><strong>
								{{ $statement['ending'] < 0 ? '(' . formatCurrency(abs($statement['ending']), $currency_sign) . ')' : formatCurrency($statement['ending'], $currency_sign) }}
							</strong></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection