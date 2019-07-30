<?php
/**
* Deposit Listing Page on Super Admin
*
* @author KCG
* @since July 28, 2017
* @version 1.0
*/
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\PaymentGateway;
?>
@extends('layouts/admin/super')

@section('content')
<div id="deposits">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cog font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Deposit Requests</span>
	        </div>
	        <div class="pull-left border-light w-30 mt-3 ml-5 p-3">
	        	<div class="row">
	        		<div class="col-md-4 text-center border-light-right">
	        			<div class="fs-24 mb-3">{{ $total_in_queue }}</div>
	        			<span class="label label-active pull-left ml-3">In Queue</span>
	        			<i class="icon icon-question pull-right mr-2" data-toggle="tooltip" title="You have to edit / proceed it."></i>
	        		</div>
	        		<div class="col-md-4 text-center border-light-right">
	        			<div class="fs-24 mb-3">{{ $total_proceeding }}</div>
	        			<span class="label label-proceeding pull-left">Proceeding</span>
	        			<i class="icon icon-question pull-right" data-toggle="tooltip" title="Cronjob will make it completed an hour after you proceed. Mean while, you may suspend proceeding, edit, or delete it."></i>
	        		</div>
	        		<div class="col-md-4 text-center">
	        			<div class="fs-24 mb-3">{{ $total_suspended }}</div>
	        			<span class="label label-suspended pull-left">Suspended</span>
	        			<i class="icon icon-question pull-right" data-toggle="tooltip" title="You suspended proceeding the request. It won't be updated by cronjob."></i>
	        		</div>
	        	</div>
	        </div>
	        <div class="tools">
	            <button id="btnAddDeposit" class="btn blue">Add New <i class="fa fa-plus"></i></button>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form id="deposit_list" class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($deposits) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select name="deposit_status" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="{{ TransactionLocal::STATUS_PROCEEDING }}">Proceed</option>
								<option value="{{ TransactionLocal::STATUS_SUSPENDED }}">Suspend</option>
								<option value="EDIT">Edit</option>
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
								<th width="5%" class="sorting{{ $sort == 'id' ? $sort_dir : '' }}" data-sort="id">ID #</th>
								<th width="12%" class="sorting{{ $sort == 'username' ? $sort_dir : '' }}" data-sort="username">User Name</th>
								<th class="sorting{{ $sort == 'gateway' ? $sort_dir:'' }}" data-sort="gateway">Gateway</th>
								<th width="15%" class="sorting{{ $sort == 'note' ? $sort_dir : '' }}" data-sort="note">Comment</th>
								<th width="5%" class="sorting{{ $sort == 'amount' ? $sort_dir : '' }}" data-sort="amount">Amount</th>
								<th width="10%" class="sorting{{ $sort == 'created_at' ? $sort_dir : '' }}" data-sort="created_at">Deposited At</th>
								<th width="10%" class="sorting{{ $sort == 'updated_at' ? $sort_dir : '' }}" data-sort="updated_at">Updated At</th>
								<th width="13%" class="sorting{{ $sort == 'status' ? $sort_dir:'' }}" data-sort="status">Status</th>
								<th width="5%">Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[username]" value="{{ old('filter.username') }}" placeholder="#ID or User Name" />
								</th>
								<th>
									<select name="filter[gateway]" class="form-control form-filter input-sm select2">
	                                    <option value="">Select Gateway...</option>
	                                    @foreach ( $payment_gateways as $gateway )
	                                    <option value="{{ $gateway->id }}" {{ old('filter.gateway') == $gateway->id ? 'selected' : '' }}>{{ parse_json_multilang($gateway->name) }}</option>
	                                    @endforeach
	                                </select>

	                                <div class="margin-top-10">
										<input type="text" class="form-control form-filter input-sm" name="filter[user_email]" value="{{ old('filter.user_email') }}" placeholder="Keyword" />
									</div>
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[note]" value="{{ old('filter.note') }}" placeholder="Comment" />
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[amount]" value="{{ old('filter.amount') }}" placeholder="Amount" />
								</th>
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
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[updated_at][from]" placeholder="From" value="{{ old('filter.updated_at.from') }}" data-value="{{ old('filter.updated_at.from') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[updated_at][to]" placeholder="To" value="{{ old('filter.updated_at.to') }}" data-value="{{ old('filter.updated_at.to') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</th>
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										<option value="{{ TransactionLocal::STATUS_AVAILABLE }}" {{ TransactionLocal::STATUS_AVAILABLE == old('filter.status') ? 'selected' : '' }}>In Queue</option>
										<option value="{{ TransactionLocal::STATUS_PROCEEDING }}" {{ TransactionLocal::STATUS_PROCEEDING == old('filter.status') ? 'selected' : '' }}>Proceeding</option>
										<option value="{{ TransactionLocal::STATUS_SUSPENDED }}" {{ TransactionLocal::STATUS_SUSPENDED == old('filter.status') ? 'selected' : '' }}>Suspended</option>
										<option value="{{ TransactionLocal::STATUS_DONE }}" {{ TransactionLocal::STATUS_DONE == old('filter.status') ? 'selected' : '' }}>Completed</option>
									</select>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($deposits as $deposit)
							<tr class="odd gradeX{{ $deposit->isAvailable() && $deposit->isOverdue() ? ' past' : '' }}">
			                    <td><input type="checkbox" class="checkboxes" name="ids[{{ $deposit->id }}]" value="{{ $deposit->id }}" {{ TransactionLocal::enableDepositStatusChanged($deposit) }} /></td>
								<td class="deposit-id" align="center">
									<span{{ $deposit->isAvailable() && $deposit->isOverdue() ? ' data-toggle=tooltip data-placement=top title=Overdue' : '' }}>{{ $deposit->id }}</span><i class="label label-warning"{{ $deposit->status == TransactionLocal::STATUS_AVAILABLE && $deposit->isOverdue() ? ' data-toggle=tooltip data-placement=top title=Overdue' : '' }}>{{ $deposit->id }}</i>
								</td>
								<td class="td-status">
									<a href="{{ route('admin.super.user.overview', ['id' => $deposit->user_id]) }}">{!! $deposit->user->fullname(true) !!}</a>
									@if ( $deposit->user->isSuspended() )
									<span class="label label-danger">{{ trans('common.suspended') }}</span>
									@elseif ( $deposit->user->isFinancialSuspended() )
									<span class="label label-warning">{{ trans('common.financial_suspended') }}</span>
									@elseif ( $deposit->user->isLoginBlocked() )
									<span class="label label-warning">{{ trans('common.login_blocked') }}</span>
									@elseif ( $deposit->user->isDeleted() )
									<span class="label label-danger">{{ trans('common.deleted') }}</span>
									@endif
								</td>
								<td>{{ $deposit->gateway_string() }}</td>
								<td>{!! $deposit->note !!}</td>
								<td align="right">${{ formatCurrency($deposit->amount) }}</td>
								<td align="center">{{ format_date('Y-m-d H:i', $deposit->created_at) }}</td>
								<td align="center">{{ format_date('Y-m-d H:i', $deposit->updated_at) }}</td>
								<td align="center">
									<span class="label label-{{ strtolower($deposit->isAvailable() ? 'active' : $deposit->status_string()) }}">{{ $deposit->isDone() ? 'Completed' : ($deposit->isAvailable() ? 'In Queue' : $deposit->status_string()) }}</span>
								</td>
								<td align="center">
									<a class="btn-view blue" data-json="{{ $deposit->getJson() }}">View</a>
								</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="11" align="center">No Deposit Requests</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($deposits) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $deposits->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>

@include('pages.admin.super.payment.modal.deposit')

@endsection