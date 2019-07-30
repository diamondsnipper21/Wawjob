<?php
/**
* User Messages Page on Super Admin
*
* @author KCG
* @since July 14, 2017
* @version 1.0
*/

use iJobDesk\User

?>
@extends('layouts/admin/super/user')

@section('content')
<div id="user_messages">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Messages</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ route('admin.super.user.messages', ['user_id' => $user->id]) }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($threads) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select class="table-group-action-input form-control input-inline input-small input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="DELETE">Delete</option>
							</select>
							<button class="btn btn-sm yellow table-group-action-submit button-submit" type="button" disabled data-auto-submit="false"><i class="fa fa-check"></i> Submit</button>
						</div>
			        </div>
			    </div>
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="2%"><input type="checkbox" class="group-checkable" /></th>
								@if (!$user->isFreelancer())
								<th width="20%" class="sorting{{ $sort == 'freelancer_name'?$sort_dir:'' }}" 	data-sort="freelancer_name">Freelancer</th>
								@endif
								@if (!$user->isBuyer())
								<th width="20%" class="sorting{{ $sort == 'buyer_name'?$sort_dir:'' }}" 		data-sort="buyer_name">Buyer</th>
								@endif
								<th width="20%" class="sorting{{ $sort == 'job_posting'?$sort_dir:'' }}" 		data-sort="job_posting">Job Posting</th>
								<th width="20%" class="sorting{{ $sort == 'related_job'?$sort_dir:'' }}"     	data-sort="related_job">Related Contract</th>
								<th width="15%" class="sorting{{ $sort == 'created_at'?$sort_dir:'' }}" 		data-sort="created_at">Created At</th>
								<th width="15%" class="sorting{{ $sort == 'last_reply_date'?$sort_dir:'' }}" 	data-sort="last_reply_date">Last Reply Date</th>
								<th>Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Freelancer -->
								@if (!$user->isFreelancer())
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[freelancer_name]" value="{{ old('filter.freelancer_name') }}" placeholder="ID # or Name" />
								</th>
								@endif
								<!-- Buyer -->
								@if (!$user->isBuyer())
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[buyer_name]" value="{{ old('filter.buyer_name') }}" placeholder="ID # or Name" />
								</th>
								@endif
								<!-- Job Posting -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[job_posting]" value="{{ old('filter.job_posting') }}" />
								</th>
								<!-- Related Job -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[related_job]" value="{{ old('filter.related_job') }}" />
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
								<!-- Last Reply Date -->
								<th>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[last_reply_date][from]" placeholder="From" value="{{ old('filter.last_reply_date.from') }}" data-value="{{ old('filter.last_reply_date.from') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[last_reply_date][to]" placeholder="To" value="{{ old('filter.last_reply_date.to') }}" data-value="{{ old('filter.last_reply_date.to') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($threads as $thread)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $thread->thread_id }}" /></td>
								@if (!$user->isFreelancer())
								<td><a href="{{ route('admin.super.user.overview', ['user_id' => $thread->freelancer_id]) }}">{{ $thread->freelancer_name }}</a></td>
								@endif
								@if (!$user->isBuyer())
								<td><a href="{{ route('admin.super.user.overview', ['user_id' => $thread->buyer_id]) }}">{{ $thread->buyer_name }}</a></td>
								@endif
								<td><a href="{{ route('admin.super.job.overview', ['id' => $thread->project_id]) }}">{{ $thread->job_posting }}</a></td>
								<td><a href="{{ route('admin.super.user.contract', ['userId' => $user->id, 'contractId' => $thread->contract_id]) }}">{{ $thread->related_job }}</a></td>
								<td align="center">{{ format_date('Y-m-d H:i:s', $thread->created_at) }}</td>
								<td align="center">{{ $thread->last_reply_date }}</td>
								<td align="center"><a href="{{ route('admin.super.user.messages.thread', ['user_id' => $user->id, 'thread_id' => $thread->thread_id]) }}" class="blue">View</a></td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="7" align="center">No Messages</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($threads) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $threads->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection