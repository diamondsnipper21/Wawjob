<?php
/**
* Overview Page on Super Admin
*
* @author KCG
* @since July 23, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;

?>
@extends('layouts/admin/super')

@section('content')

<script type="text/javascript">
@if (isset($dates))
	var date_from = '{{ date("Y-m-d", strtotime($dates['from'])) }}';
	var date_to   = '{{ date("Y-m-d", strtotime($dates['to'])) }}';
@endif
</script>

<form id="frm_transactions_filter" method="POST">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="view" value="{{ $view }}">

	<div id="transactions" class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Transactions</span>
	        </div>
	        <div class="tools">
	        	<span class="balance">Balance: {{ $balance > 0 ? '$' . formatCurrency($balance) : '($' . formatCurrency(abs($balance)) . ')' }}</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<div class="row">
	    		<div class="col-md-2">
	    			<div class="show-lifetime">
    					<label><input type="checkbox" id="all" {{ $life_time ? 'checked' : '' }} />Show Life Time</label>
    				</div>
	    		</div>

	    		<div class="col-md-4">
					<div class="date-filter">
						@if ($prev)
						<a class="prev-unit" href="#" data-range="{{ $prev }}"><i class="glyphicon glyphicon-chevron-left"></i></a>
						@endif
						<div class="input-group" id="date_range">
							<input type="text" class="form-control" name="date_range" value="{{ date('M j, Y', strtotime($dates['from']))." - ".date('M j, Y', strtotime($dates['to'])) }}">
							<span class="input-group-btn">
								<button class="btn default date-range-toggle" type="button"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
						@if ($next)
						<a class="next-unit" href="#" data-range="{{ $next }}"><i class="glyphicon glyphicon-chevron-right"></i></a>
						@endif
					</div>
				</div>

				<div class="col-md-2">
					<select class="form-control select2" id="transaction_type" name="transaction_type" placeholder="Transaction Type">
						<option value="" {{ $type == '' ? 'selected':'' }}>{{ trans('report.all_transactions') }}</option>
						<option value="{{ TransactionLocal::TYPE_FIXED }}" {{ $type == (string)TransactionLocal::TYPE_FIXED ? 'selected' : '' }}>{{ trans('common.fixed_price') }}</option>
						<option value="{{ TransactionLocal::TYPE_HOURLY }}" {{ $type == (string)TransactionLocal::TYPE_HOURLY ? 'selected' : '' }}>{{ trans('common.hourly') }}</option>
						<option value="{{ TransactionLocal::TYPE_BONUS }}" {{ $type == (string)TransactionLocal::TYPE_BONUS ? 'selected' : '' }}>{{ trans('common.bonus') }}</option>
						<option value="{{ TransactionLocal::TYPE_CHARGE }}" {{ $type == (string)TransactionLocal::TYPE_CHARGE ? 'selected' : '' }}>{{ trans('common.deposit') }}</option>
						<option value="{{ TransactionLocal::TYPE_WITHDRAWAL }}" {{ $type == (string)TransactionLocal::TYPE_WITHDRAWAL ? 'selected' : '' }}>{{ trans('common.withdrawal') }}</option>
						<option value="{{ TransactionLocal::TYPE_REFUND }}" {{ $type == (string)TransactionLocal::TYPE_REFUND ? 'selected' : '' }}>{{ trans('common.refund') }}</option>
						<option value="{{ TransactionLocal::TYPE_AFFILIATE }}" {{ $type == (string)TransactionLocal::TYPE_AFFILIATE ? 'selected' : '' }}>{{ trans('common.affiliate') }}</option>
						<option value="{{ TransactionLocal::TYPE_IJOBDESK_EARNING }}" {{ $type == (string)TransactionLocal::TYPE_IJOBDESK_EARNING ? 'selected' : '' }}>{{ config('app.name') }} Earning</option>
					</select>
	    		</div>

	    		<div class="col-md-2 box-users">
	    			<select id="user_id" name="user_id" class="form-control select2-user" data-ajax-url="{{ Request::url() }}">
	    				@if ( $user_id )
	    				<option value="{{ $user_id }}">{{ $user->fullname() }}</option>
	    				@endif
				    </select>
					@if ( $user_id )
					<i class="fa fa-times"></i>
					@endif
	    		</div>

	    		<div class="col-md-2">
	    			<div class="row">
	    				<div class="col-md-7 text-right">
	    					<label class="margin-top-5">Rows Per Page</label>
	    				</div>
	    				<div class="col-md-5">
							<select class="form-control select2" id="view_by" name="view_by">
								<option value="20" {{ $view_by == '20' ? 'selected' : '' }}>20</option>
								<option value="50" {{ $view_by == '50' ? 'selected' : '' }}>50</option>
								<option value="100" {{ $view_by == '100' ? 'selected' : '' }}>100</option>
							</select>
						</div>
					</div>
	    		</div>
	    	</div>

	    	<div class="transactions-section table-scrollable">
				<table class="table">
					<thead>
						<tr>
							<th width="8%">{{ trans('common.ref_id') }}</th>
							<th width="10%">{{ trans('common.date') }}</th>
							<th width="12%">{{ trans('common.type') }}</th>
							<th>{{ trans('common.description') }}</th>
							<th width="12%">Payer</th>
							<th width="12%">Receiver</th>
							<th width="12%" class="text-right">{{ trans('common.amount') }}</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($transactions as $t)
							@include ('pages.admin.super.payment.section.transaction_row')
						@empty
						<tr>
							<td colspan="6">
						        <div class="not-found-result">
					                <div class="text-center">
					                    <div class="heading">{{ trans('common.no_transactions') }}</div>
					                </div>
						        </div>						
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<div class="row margin-top-10">
                <div class="col-md-6">
                    <div role="status" aria-live="polite">{{ render_admin_paginator_desc($transactions) }}</div>
                </div>
                <div class="col-md-6">
                    <div class="datatable-paginate pull-right">{!! $transactions->render() !!}</div>
                </div>
            </div>

            @include ('pages.freelancer.report.section.transactions_statement')
	    </div><!-- .portlet-body -->
	</form>
</div>

@endsection