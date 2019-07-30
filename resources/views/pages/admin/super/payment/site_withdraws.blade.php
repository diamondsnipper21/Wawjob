<?php
/**
* iJobDesk withdraws Listing Page on Super Admin
*
* @author KCG
* @since July 28, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\TransactionLocal;
?>
@extends('layouts/admin/super')

@section('content')
<div id="site_withdraws">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption w-45">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">iJobDesk Withdrawal History</span>
	            <div class="fs-13 mt-2 ml-3 pl-3">It shows iJobDesk withdrawals that we withdraw funds from the site.</div>
	        </div>
	        <div class="tools w-45 text-right">
	            <button class="btn green" data-toggle="modal" data-target="#modal_withdraw">Withdraw Now <i class="fa fa-plus"></i></button>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($withdraws) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select name="action" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="{{ TransactionLocal::STATUS_PROCEEDING }}">Proceed</option>
								<option value="DELETE">Delete</option>
							</select>
							<button class="btn btn-sm yellow table-group-action-submit button-submit" type="button" disabled><i class="fa fa-check"></i> Submit</button>
						</div>
			        </div>
			    </div>
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="2%"><input type="checkbox" class="group-checkable" /></th>
								<th width="10%" class="sorting{{ $sort == 'amount' ? $sort_dir : '' }}"	data-sort="amount">Amount</th>
								<th class="sorting{{ $sort == 'note' ? $sort_dir : '' }}" data-sort="note">Comment</th>
								<th width="15%" class="sorting{{ $sort == 'fullname' ? $sort_dir : '' }}" data-sort="fullname">Doer</th>
								<th width="15%" class="sorting{{ $sort == 'created_at' ? $sort_dir : '' }}"	data-sort="created_at">Date</th>
								<th width="15%" class="sorting{{ $sort == 'status' ? $sort_dir : '' }}" data-sort="status">Status</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Amount -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[amount]" value="{{ old('filter.amount') }}" placeholder="Amount" />
								</th>
								<!-- Comment -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[note]" value="{{ old('filter.note') }}" placeholder="" />
								</th>
								<!-- Doer -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[fullname]" value="{{ old('filter.fullname') }}" />
								</th>
								<!-- Created At -->
								<th>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][from]" placeholder="From" value="{{ old('filter.created_at.from') }}" data-value="{{ old('filter.created_at.from') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][to]" placeholder="To" value="{{ old('filter.created_at.to') }}" data-value="{{ old('filter.created_at.to') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</th>
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										<option value="{{ TransactionLocal::STATUS_AVAILABLE }}" {{ "{TransactionLocal::STATUS_AVAILABLE}" == old('filter.status') || TransactionLocal::STATUS_AVAILABLE == $filter['status'] ? 'selected' : '' }}>In Queue</option>
										<option value="{{ TransactionLocal::STATUS_PROCEEDING }}" {{ "{TransactionLocal::STATUS_PROCEEDING}" == old('filter.status') || TransactionLocal::STATUS_PROCEEDING == $filter['status'] ? 'selected' : '' }}>Proceeding</option>
										<option value="{{ TransactionLocal::STATUS_DONE }}" {{ TransactionLocal::STATUS_DONE == old('filter.status')  || TransactionLocal::STATUS_DONE == $filter['status'] ? 'selected' : '' }}>Completed</option>
									</select>
								</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($withdraws as $withdraw)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $withdraw->id }}" {{ TransactionLocal::enableStatusChanged($withdraw) }} /></td>
								<td>${{ formatCurrency(abs($withdraw->amount)) }}</td>
								<td>{{ $withdraw->note }}</td>
								<td><a href="{{ route('admin.super.admin_users.list') }}">{!! $withdraw->user->fullname(true) !!}</a></td>
								<td align="center">{{ format_date('Y-m-d H:i', $withdraw->created_at) }}</td>
								<td align="center"><span class="label label-{{ strtolower($withdraw->status_string()) }}">{{ $withdraw->isDone() ? 'Completed' : ($withdraw->isAvailable() ? 'In Queue' : $withdraw->status_string()) }}</span></td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="6" align="center">No Withdraws</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($withdraws) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $withdraws->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
	@include('pages.admin.super.payment.site_withdraws.modal')
</div>

@endsection