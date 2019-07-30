<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\User;
?>
@extends('layouts/admin/super')

@section('content')
<div class="portlet light administrators-page">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-cogs font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">Administrators</span>
        </div>
        <div class="tools">
            <button class="btn green open-modal" data-url="{{ route('admin.super.admin_users.edit') }}">Add New <i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="portlet-body">
    	<form id="admin_list" action="{{ route('admin.super.admin_users.list') }}" method="post">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		    <input type="hidden" name="_action" value="" />
		    <textarea name="_reason" class="hide"></textarea>

		    {{ show_messages() }}

		    <div class="row margin-bottom-10">
		        <div class="col-md-6 margin-top-10">
		            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($admins) }}</div>
		        </div>
		    </div>

		    <div class="row margin-bottom-10">
		        <div class="col-md-6 margin-top-10">
                    <a href="#" class="clear-filter">Clear filters</a>
                </div> 
		        <div class="col-md-6">
		        	<div class="toolbar toolbar-table pull-right">
						<span><strong>Action</strong>&nbsp;</span>
						<select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-status" data-auto-submit="false">
							<option value="">Select...</option>
							<option value="{{ User::STATUS_AVAILABLE }}">Active</option>
							<option value="{{ User::STATUS_SUSPENDED }}">Suspend</option>
							<option value="{{ User::STATUS_DELETED }}">Delete</option>
						</select>
						<button class="btn btn-sm yellow table-group-action-submit button-change-status" type="button" disabled><i class="fa fa-check"></i> Submit</button>
					</div>
		        </div>
		    </div>

			<div class="table-container">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr role="row" class="heading">
							<th width="2%"><input type="checkbox" class="group-checkable" /></th>
							<th width="7%">Avatar</th>
							<th             class="sorting{{ $sort == 'fullname'?$sort_dir:'' }}"  	data-sort="fullname">Name</th>
							<th             class="sorting{{ $sort == 'email'?$sort_dir:'' }}" 		data-sort="email">Email</th>
							<th width="13%" class="sorting{{ $sort == 'role'?$sort_dir:'' }}" 		data-sort="role">Type</th>
							<th width="13%"  class="sorting{{ $sort == 'status'?$sort_dir:'' }}"     data-sort="status">Status</th>
							<th width="13%" class="sorting{{ $sort == 'created_at'?$sort_dir:'' }}" data-sort="created_at">Created At</th>
							<th width="13%" class="sorting{{ $sort == 'updated_at'?$sort_dir:'' }}" data-sort="updated_at">Updated At</th>

							<th width="7%">Action</th>
							<!-- <th width="10%" class="sorting">Actions</th> -->
						</tr>
						<tr role="row" class="filter">
							<th>&nbsp;</th>
							<th></th>
							<th>
								<input type="text" class="form-control form-filter input-sm" name="filter[fullname]" value="{{ old('filter.fullname') }}" />
							</th>
							<th>
								<input type="text" class="form-control form-filter input-sm" name="filter[email]" value="{{ old('filter.email') }}" />
							</th>
							<th>
								<select name="filter[role]" class="form-control form-filter input-sm select2">
									<option value="">Select...</option>
									@foreach (User::adminType() as $key => $value)
									<option value="{{ $key }}" {{ $key == old('filter.role')?'selected':'' }}>{{ $value }}</option>
									@endforeach
								</select>
							</th>
							<th>
								<select name="filter[status]" class="form-control form-filter input-sm select2-status">
									<option value="">Select...</option>
									@foreach (User::adminStatus() as $key => $value)
									<option value="{{ $key }}" {{ $key == old('filter.status')?'selected':'' }}>{{ $value }}</option>
									@endforeach
								</select>
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
							<th></th>
						</tr>
					</thead>
					<tbody>
					@forelse ($admins as $admin)
						<tr class="odd gradeX">
		                    <td align="center"><input type="checkbox" class="checkboxes" name="id[]" value="{{ $admin->id }}" {{ $admin->enableAdminStatusChanged() }} /></td>
		                    <td align="center"><img src="{{avatar_url($admin)}}" width="50" class="img-circle" /></td>
		                    <td>{{ $admin->fullname() }}</td>
		                    <td>{{ $admin->email }}</td>
		                    <td align="center">{{ array_get(User::adminType(), $admin->role) }}</td>
		                    <td align="center"><span class="label label-{{ strtolower(array_get(User::adminStatus(), $admin->status)) }}">{{ array_get(User::adminStatus(), $admin->status) }}</span></td>
		                    <td align="center">{{ format_date('Y-m-d', $admin->created_at) }}</td>
		                    <td align="center">{{ format_date('Y-m-d', $admin->updated_at) }}</td>
		                    <td align="center">
		                    	<button class="btn btn-sm blue filter-cancel open-modal" data-url="{{ route('admin.super.admin_users.edit', ['id' => $admin->id]) }}"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</button>
		                    </td>
						</tr>
		            @empty
		                <tr class="odd gradeX">
		                    <td colspan="8" align="center">No Administrators</td>
		                </tr>
		            @endforelse
					</tbody>
				</table>
		        <div class="row">
		            {{-- <div class="col-md-6">
		                <div role="status" aria-live="polite">{{ render_admin_paginator_desc($admins) }}</div>
		            </div>
		            <div class="col-md-6">
		                <div class="datatable-paginate pull-right">{!! $admins->render() !!}</div>
		            </div> --}}
		        </div>
			</div>

			<div class="row margin-bottom-10">
		        <div class="col-md-6 margin-top-10">
		            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($admins) }}</div>
		        </div>
		        <div class="col-md-6">
                    <div class="datatable-paginate pull-right">{!! $admins->render() !!}</div>
                </div>
		    </div>

		</form>
    </div>
</div>

<div id="modal_admin_user_container"></div>
@endsection
