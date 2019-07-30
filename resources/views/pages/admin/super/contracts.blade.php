<?php
/**
* Freelancer Contracts Page on Super Admin
*
* @author KCG
* @since July 14, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Contract;

$statusList = Contract::$str_contract_status;

?>
@extends('layouts/admin/super'.(!empty($user)?'/user':''))

@section('content')
<div id="user_contracts" class="freelancer">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Contracts</span>
	        </div>
			<!-- BEGIN PAGE BREADCRUMB -->
			@if (false && empty($user))
			@include('layouts.admin.commons.breadcrumbs')
			@endif
			<!-- END PAGE BREADCRUMB -->
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($contracts) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="{{ Contract::STATUS_OPEN }}">Activate</option>
								<option value="{{ Contract::STATUS_SUSPENDED }}">Suspend</option>
								<!-- <option value="DELETE">Delete</option> -->
							</select>
							<button class="btn btn-sm yellow table-group-action-submit button-submit" disabled type="button"><i class="fa fa-check"></i> Submit</button>
						</div>
			        </div>
			    </div>
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="2%"><input type="checkbox" class="group-checkable" /></th>
								<th width="5%" 	class="sorting{{ $sort == 'contracts.id'?$sort_dir:'' }}" 	data-sort="id">ID #</th>
								<th width="5%" 	class="sorting{{ $sort == 'type'?$sort_dir:'' }}" 			data-sort="type">Type</th>
								<th width="20%" class="sorting{{ $sort == 'title'?$sort_dir:'' }}"  		data-sort="title">Contract Title</th>
								@if ($user && $user->isFreelancer() || !$user)
								<th width="" 	class="sorting{{ $sort == 'buyer_name'?$sort_dir:'' }}" 			data-sort="buyer_name">Buyer</th>
								@endif
								@if ($user && $user->isBuyer() || !$user)
								<th width="" 	class="sorting{{ $sort == 'contractor_name'?$sort_dir:'' }}" 	data-sort="contractor_name">Contractor</th>
								@endif
								<th width="15%" class="sorting{{ $sort == 'started_at'?$sort_dir:'' }}"     data-sort="started_at">Time Period</th>
								<th width="12%" >Terms</th>
								<th width="12%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}" 		data-sort="status">Status</th>
								<th width="8%" >Actions</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- #ID -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
								</th>
								<!-- Type -->
								<th>
									<select name="filter[type]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										@foreach (Contract::$str_contract_type as $type => $label)
										<option value="{{ $type }}" {{ "$type" == old('filter.type')?'selected':'' }}>{{ $label == 'Fixed Price'?'Fixed':$label }}</option>
										@endforeach
									</select>
								</th>
								<!-- Contract Title -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[title]" value="{{ old('filter.title') }}" />
								</th>
								@if ($user && $user->isFreelancer() || !$user)
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[buyer_name]" value="{{ old('filter.buyer_name') }}" placeholder="#ID or Name" />
								</th>
								@endif
								@if ($user && $user->isBuyer() || !$user)
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[contractor_name]" value="{{ old('filter.contractor_name') }}" placeholder="#ID or Name" />
								</th>
								@endif
								<!-- Period -->
								<th>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[period][from]" placeholder="From" value="{{ old('filter.period.from') }}" data-value="{{ old('filter.period.from') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[period][to]" placeholder="To" value="{{ old('filter.period.to') }}" data-value="{{ old('filter.period.to') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</th>
								<!-- Terms -->
								<th>&nbsp;</th>
								<!-- Status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach (Contract::onlyContractStatus() as $status)
										<option value="{{ $status }}" {{ "$status" == old('filter.status')?'selected':'' }}>{{ $statusList[$status] }}</option>
										@endforeach
									</select>
								</th>
								<!-- Actions -->
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($contracts as $contract)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $contract->id }}" {{ $contract->enableStatusChanged() }} /></td>
			                    <td align="center">{{ $contract->id }}</td>
								<td align="center">{{ str_replace('Price', '', Contract::$str_contract_type[$contract->type]) }}</td>
								<td>{{ $contract->title }}</td>
								@if ($user && $user->isFreelancer() || !$user)
								<td><a href="{{ route('admin.super.user.overview', ['user_id' => $contract->buyer_id]) }}">{!! $contract->buyer->fullname(true) !!}</a></td>
								@endif
								@if ($user && $user->isBuyer() || !$user)
								<td><a href="{{ route('admin.super.user.overview', ['user_id' => $contract->contractor_id]) }}">{!! $contract->contractor->fullname(true) !!}</a></td>
								@endif
								<td align="left">
									{{ date('Y-m-d', strtotime($contract->started_at)) }} 
									~ 
									{{ $contract->ended_at ? date('Y-m-d', strtotime($contract->ended_at)) :'' }}
								</td>
								<td>
									@if ( $contract->isHourly() )
										${{ number_format($contract->price) }}<br />
										@if ( $contract->isNoLimit() )
											No Limit
										@else
											{{ $contract->limit }} hrs/wk 
										@endif
									@else
										${{ number_format($contract->price) }}<br />
										{{ $contract->countMilestones() }} Milestone(s)
									@endif
								</td>
								<td align="center"><span class="label label-{{ strtolower($statusList[$contract->status]) }}">{{ $contract->status_string() }}</span></td>
								<td align="center">
									<div class="btn-group">
										<button class="btn blue btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
										Action <i class="fa fa-angle-down"></i>
										</button>
										<ul class="dropdown-menu pull-right" role="menu">
										<?php $message_thread_id = $contract->getMessageThreadId(); ?>
										@if (!$user)
											<li>
												<a href="{{ route('admin.super.contract', ['contractId'=>$contract->id]) }}">View</a>
											</li>
										@else
											<li>
												<a href="{{ route('admin.super.user.contract', ['userId' => $user->id, 'contractId' => $contract->id]) }}">View</a>
											</li>
										@endif
										@if ($contract->isHourly())
											<li>
												<a href="{{ route('admin.super.user.workdiary.view', ['user_id' => !$user?$contract->contractor_id:$user->id, 'cid' => $contract->id]) }}">View Work Diary</a>
											</li>
										@endif
										@if ($message_thread_id)
											<li>
												<a href="{{ route('admin.super.user.messages.thread', ['user_id' => !$user?$contract->contractor_id:$user->id, 'thread_id' => $message_thread_id]) }}">View Messages</a>
											</li>
										@endif
										</ul>
									</div>
								</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="10" align="center">No Contracts</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($contracts) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $contracts->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    	
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection