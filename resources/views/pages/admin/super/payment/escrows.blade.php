<?php
/**
* Escrow Listing Page on Super Admin
*
* @author KCG
* @since July 24, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\ContractMilestone;

$statusList = ContractMilestone::getOptions('fund_status');

?>
@extends('layouts/admin/super')

@section('content')
<div id="escrows">
	<div class="portlet light">
	    <div class="portlet-title">
    		
    		<div class="col-md-6">
		        <div class="caption pull-left">
		            <i class="fa fa-cogs font-green-sharp"></i>
		            <span class="caption-subject font-green-sharp bold">Escrow</span>
		        </div>
		        <div class="pull-left totals">
		        	<strong>${{ number_format($totals, 2) }}</strong> in escrow now
		        </div>
		    </div>
		    <div class="col-md-6">
		    	<p class="text-right margin-top-10">*Note: It has pending period when you release funds to the freelancers.</p>
		    </div>

	    </div><!-- .portlet-title -->

	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />
    			<input type="hidden" name="_reason" />
    			<input type="hidden" name="_reason_option" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($escrows) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select name="fund_status" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-with-color="true" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="{{ ContractMilestone::FUND_PAID }}" data-color-key="active">Release(to Freelancer)</option>
								<option value="{{ ContractMilestone::FUND_REFUNDED }}" data-color-key="refunded">Refund(to Buyer)</option>
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
								<th width="5%" class="sorting{{ $sort == 'id' ? $sort_dir : '' }}" 			data-sort="id">ID #</th>
								<th class="sorting{{ $sort == 'c.title' ? $sort_dir : '' }}" 				data-sort="c.title">Contract</th>
								<th width="10%" class="sorting{{ $sort == 'price' ? $sort_dir : '' }}" 		data-sort="price">Amount</th>
								<th width="10%" class="sorting{{ $sort == 'b.fullname' ? $sort_dir : '' }}" data-sort="b.fullname">Buyer</th>
								<th width="10%" class="sorting{{ $sort == 'f.fullname' ? $sort_dir : '' }}" data-sort="f.fullname">Freelancer</th>
								<th width="10%" class="sorting{{ $sort == 'created_at' ? $sort_dir : '' }}" data-sort="created_at">Funded At</th>
								<th width="10%" class="sorting{{ $sort == 'updated_at' ? $sort_dir : '' }}" data-sort="updated_at">Updated At</th>
								<th width="12%" class="sorting{{ $sort == 'fund_status' ? $sort_dir : '' }}" data-sort="fund_status">Status</th>
								<th width="5%">Actions</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- #ID -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
								</th>
								<!-- Contract -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[contract_title]" value="{{ old('filter.contract_title') }}" placeholder="#ID or Title" />
								</th>
								<!-- Amount -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[amount]" value="{{ old('filter.amount') }}" placeholder="Amount" />
								</th>
								<!-- Buyer -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[buyer_name]" value="{{ old('filter.buyer_name') }}" placeholder="#ID or Name" />
								</th>
								<!-- Freelancer -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[freelancer_name]" value="{{ old('filter.freelancer_name') }}" placeholder="#ID or Name" />
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
								<!-- Updated At -->
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
								<!-- Status -->
								<th>
									<select name="filter[fund_status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
											<option value="{{ $status }}" {{ ("$status" == old('filter.fund_status') || "$status" == $filter['fund_status']) ? 'selected' : '' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<label class="control-label">Doer</label>
									<select name="filter[performed_by]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										<option value="{{ ContractMilestone::PERFORMED_BY_BUYER }}" {{ old('filter.performed_by') != '' && ContractMilestone::PERFORMED_BY_BUYER == old('filter.performed_by') ? 'selected' : '' }}>Buyer</option>
										<option value="{{ ContractMilestone::PERFORMED_BY_SUPER_ADMIN }}" {{ old('filter.performed_by') != '' && ContractMilestone::PERFORMED_BY_SUPER_ADMIN == old('filter.performed_by') ? 'selected':'' }}>Admin</option>
									</select>
								</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($escrows as $escrow)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[{{ $escrow->id }}]" value="{{ $escrow->contract_id }}"{{ $escrow->isRefunded() || $escrow->isPending() || $escrow->isReleased() ? ' disabled' : '' }} {{ ContractMilestone::availableActionsByStatus($escrow->fund_status) }} /></td>
								<td align="center">{{ $escrow->id }}</td>
								<td class="td-status">
									<a href="{{ route('admin.super.contract', ['contractId' => $escrow->contract_id]) }}">{{ $escrow->contract->title }}</a>
									@if ( $escrow->contract->isSuspended() )
									<span class="label label-danger">{{ trans('common.suspended') }}</span>
									@elseif ( $escrow->contract->isPaused() )
									<span class="label label-warning">{{ trans('common.paused') }}</span>
									@endif
									<br />
									<div class="milestone-title">M - {{ $escrow->name }}</div>
								</td>
								<td align="right">${{ formatCurrency($escrow->getPrice()) }}</td>
								<td align="left"><a href="{{ route('admin.super.user.overview', ['user_id' => $escrow->contract->buyer_id]) }}">{!! $escrow->contract->buyer->fullname(true) !!}</a></td>
								<td align="left"><a href="{{ route('admin.super.user.overview', ['user_id' => $escrow->contract->contractor_id]) }}">{!! $escrow->contract->contractor->fullname(true) !!}</a></td>
								<td align="center">{{ format_date('Y-m-d H:i', $escrow->funded_at) }}</td>
								<td align="center">{{ format_date('Y-m-d H:i', $escrow->updated_at) }}</td>
								<td align="center">
								@if ( $escrow->isPending() )
									<span class="label label-pending">{{ $statusList[ContractMilestone::FUND_PENDING] }}</span>
								@else
									<span class="label label-{{ str_replace(' ', '-', strtolower($statusList[$escrow->fund_status])) }}">{{ $statusList[$escrow->fund_status] }}</span>
								@endif
								</td>
								<td align="center">
									<a class="btn-view blue" data-json="{{ $escrow->getJson() }}">View</a>
								</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="10" align="center">No Escrows</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($escrows) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $escrows->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection