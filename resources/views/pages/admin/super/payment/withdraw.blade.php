<?php
/**
* Withdraw Listing Page on Super Admin
*
* @author PYH
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
<div id="withdraws">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cog font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Withdrawal Requests</span>
	        </div>
	        <div class="pull-left border-light w-40 mt-3 ml-5 p-3">
	        	<div class="row">
	        		<div class="col-md-3 text-center border-light-right">
	        			<div class="fs-24 mb-3">{{ $total_overdue }}</div>
	        			<span class="label label-warning pull-left ml-3">Overdue</span>
	        			<i class="icon icon-question pull-right mr-2" data-toggle="tooltip" title="More than 3 days delayed. You have to proceed them quickly."></i>
	        		</div>
	        		<div class="col-md-3 text-center border-light-right">
	        			<div class="fs-24 mb-3">{{ $total_in_queue }}</div>
	        			<span class="label label-active pull-left ml-3">In Queue</span>
	        			<i class="icon icon-question pull-right mr-2" data-toggle="tooltip" title="You have to proceed it."></i>
	        		</div>
	        		<div class="col-md-3 text-center border-light-right">
	        			<div class="fs-24 mb-3">{{ $total_proceeding }}</div>
	        			<span class="label label-proceeding pull-left">Proceeding</span>
	        			<i class="icon icon-question pull-right" data-toggle="tooltip" title="Cronjob will make it completed an hour after you proceed. Mean while, you may Suspend proceeding."></i>
	        		</div>
	        		<div class="col-md-3 text-center">
	        			<div class="fs-24 mb-3">{{ $total_suspended }}</div>
	        			<span class="label label-suspended pull-left">Suspended</span>
	        			<i class="icon icon-question pull-right" data-toggle="tooltip" title="You suspended proceeding the request. It won't be updated by cronjob."></i>
	        		</div>
	        	</div>
	        </div>
	    </div><!-- .portlet-title -->

	    <div class="portlet-body">
	    	<form id="withdraw_list" class="form-datatable" action="{{ Request::url() }}" method="post">
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
							<select name="withdraw_status" class="table-group-action-input form-control input-inline select2 select-action" data-auto-submit="false" data-width="150">
								<option value="">Select...</option>
								@if ( $financial_managers && $current_user->isSuper() )
								<option value="{{ TransactionLocal::STATUS_NOTIFIED }}">Notify Financial Manager</option>
								@endif
								<option value="{{ TransactionLocal::STATUS_PROCEEDING }}">Proceed</option>
								<option value="{{ TransactionLocal::STATUS_SUSPENDED }}">Suspend</option>
								<option value="{{ TransactionLocal::STATUS_CANCELLED }}">Cancel</option>
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
								<th class="sorting{{ $sort == 'username'?$sort_dir:'' }}" data-sort="username">User Name</th>
								<th width="6%" class="sorting{{ $sort == 'role' ? $sort_dir : '' }}" data-sort="role">User Role</th>
								<th width="15%" class="sorting{{ $sort == 'user_location' ? $sort_dir : '' }}" data-sort="user_location">User Location</th>
								<th class="sorting{{ $sort == 'gateway' ? $sort_dir : '' }}" data-sort="gateway">Gateway</th>
								<th width="5%" class="sorting{{ $sort == 'amount' ? $sort_dir : '' }}" data-sort="amount">Amount</th>
								<th width="10%" class="sorting{{ $sort == 'transactions.created_at' ? $sort_dir : '' }}" data-sort="created_at">Accepted At</th>
								<th width="10%" class="sorting{{ $sort == 'done_at' ? $sort_dir : '' }}" data-sort="done_at">Updated At</th>
								<th width="12%" class="sorting{{ $sort == 'status' ? $sort_dir : '' }}" data-sort="status">Status</th>
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
									<select name="filter[role]" class="form-control form-filter input-sm select2">
	                                    <option value="">Select...</option>
	                                    <option value="{{ User::ROLE_USER_FREELANCER }}"  {{ User::ROLE_USER_FREELANCER == old('filter.role')?'selected':'' }}>Freelancer</option>
	                                    <option value="{{ User::ROLE_USER_BUYER }}" {{ User::ROLE_USER_BUYER == old('filter.role')?'selected':'' }}>Buyer</option>
	                                </select>
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[user_location]" value="{{ old('filter.user_location') }}" placeholder="Country Name" />
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
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[done_at][from]" placeholder="From" value="{{ old('filter.done_at.from') }}" data-value="{{ old('filter.done_at.from') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[done_at][to]" placeholder="To" value="{{ old('filter.done_at.to') }}" data-value="{{ old('filter.done_at.to') }}" />
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
										<option value="{{ TransactionLocal::STATUS_SUSPENDED }}" {{ TransactionLocal::STATUS_REVIEW == old('filter.status') || TransactionLocal::STATUS_REVIEW == $filter['status'] ? 'selected' : '' }}>Suspended</option>
										<option value="{{ TransactionLocal::STATUS_CANCELLED }}" {{ TransactionLocal::STATUS_CANCELLED == old('filter.status') || TransactionLocal::STATUS_CANCELLED == $filter['status'] ? 'selected' : '' }}>Cancelled</option>
										<option value="{{ TransactionLocal::STATUS_DONE }}" {{ TransactionLocal::STATUS_DONE == old('filter.status')  || TransactionLocal::STATUS_DONE == $filter['status'] ? 'selected' : '' }}>Completed</option>
									</select>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($withdraws as $withdraw)
							<tr class="odd gradeX{{ $withdraw->isAvailable() && $withdraw->isOverdue() ? ' past' : '' }}">
			                    <td><input type="checkbox" class="checkboxes" name="ids[{{ $withdraw->id }}]" value="{{ $withdraw->id }}" {{ TransactionLocal::enableStatusChanged($withdraw) }} {{ $withdraw->user->isSuspended() || $withdraw->user->isFinancialSuspended() || $withdraw->user->isLoginBlocked() || $withdraw->user->isDeleted() ? 'data-status-disabled=true' : '' }}{{ $withdraw->isReview() ? ' data-inreview=true' : '' }} {{ $withdraw->isDone() || $withdraw->isCancelled() ? ' disabled' : '' }} /></td>
								<td align="center" class="withdraw-id"><span{{ $withdraw->isAvailable() && $withdraw->isOverdue() ? ' data-toggle=tooltip data-placement=top title=Overdue' : '' }}>{{ $withdraw->id }}</span><i class="label label-warning"{{ $withdraw->isAvailable() && $withdraw->isOverdue() ? ' data-toggle=tooltip data-placement=top title=Overdue' : '' }}>{{ $withdraw->id }}</i></td>
								<td class="td-status">
									<a href="{{ route('admin.super.user.overview', ['id' => $withdraw->user_id]) }}">{!! $withdraw->user->fullname(true) !!}</a>
									@if ( $withdraw->user->isSuspended() )
									<span class="label label-danger">{{ trans('common.suspended') }}</span>
									@elseif ( $withdraw->user->isFinancialSuspended() )
									<span class="label label-warning">{{ trans('common.financial_suspended') }}</span>
									@elseif ( $withdraw->user->isLoginBlocked() )
									<span class="label label-warning">{{ trans('common.login_blocked') }}</span>
									@elseif ( $withdraw->user->isDeleted() )
									<span class="label label-danger">{{ trans('common.deleted') }}</span>
									@endif
								</td>
								<td>{{ array_search($withdraw->role, User::getOptions('role')) }}</td>
								<td> <i class="fa fa-map-marker"></i> {{ $withdraw->user_location }}</td>
								<td>{{ $withdraw->gateway_string() }}</td>
								<td align="right">${{ formatCurrency(-$withdraw->amount) }}</td>
								<td align="center">{{ format_date('Y-m-d H:i', $withdraw->created_at) }}</td>
								<td align="center">{{ format_date('Y-m-d H:i', $withdraw->done_at) }}</td>
								<td align="center">
									<span class="label label-{{ strtolower($withdraw->status_string()) }}">{{ $withdraw->isDone() ? 'Completed' : ($withdraw->isAvailable() ? 'In Queue' : $withdraw->status_string()) }}</span>
									@if ( $withdraw->isAvailable() && $withdraw->notify_sent )
									<div class="mt-2"><span class="label label-info">Notify Sent</span></div>
									@endif
								</td>
								<td align="center">
									<a class="btn-view blue" data-json="{{ $withdraw->getJson() }}">View</a>
								</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="12" align="center">No withdrawal requests</td>
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

	    	<div class="hidden">
	    		<form id="withdraw_process" action="{{ Request::url() }}" method="post">
	    			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    				<input type="hidden" name="_action" value="" />
    				<input type="hidden" name="withdraw_status" value="" />
    				<input type="hidden" name="ids" value="" />
	    		</form>
	    	</div>
	    </div><!-- .portlet-body -->
	</div>
</div>

@if ( $financial_managers && $current_user->isSuper() && $total_in_queue )
<div id="modal_notify" class="modal fade modal-scroll" tabindex="-1" data-width="500" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">Notify Financial Manager</h4>
	</div>
	<form action="{{ Request::url() }}" method="post">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="_action" value="NOTIFY" />
		<input type="hidden" name="ids" value="" />
		
		<div class="modal-body">
			<div class="form-group row">
				<label class="col-sm-6 mt-2 control-label">Financial Manager</label>
				<div class="col-sm-6">
					<select name="notify_manager" class="form-control input-inline select2" data-width="100%" data-rule-required="true">
						<option value="">Select...</option>
						@foreach ( $financial_managers as $admin )
							<option value="{{ $admin->id }}">{{ $admin->fullname() }}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
			<button type="submit" class="btn-send-notify btn blue">Send</button>
		</div>
	</form>
</div>
@endif

@endsection