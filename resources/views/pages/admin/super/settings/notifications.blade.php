<?php
/**
* Notifications Listing Page on Super Admin
*
* @author KCG
* @since July 30, 2017
* @version 1.0
*/

use iJobDesk\Models\Notification;
$statusList = Notification::options('status');

?>
@extends('layouts/admin/super')

@section('content')
<div id="notifications">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Notifications</span>
	        </div>
	        <div class="tools">
	            <button class="btn green add-link add">Add New <i class="fa fa-plus"></i></button>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($notifications) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span></span>
							<select name="select_action" id="select_action" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="0">Disable</option>
								<option value="1">Enable</option>
								<option value="2">Delete</option>
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
								<th width="22%" class="sorting{{ $sort == 'slug'?$sort_dir:'' }}" 					data-sort="slug">Slug</th>
								<th class="sorting{{ $sort == 'content'?$sort_dir:'' }}" 	data-sort="content">Content</th>
								<th width="11%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}"     	data-sort="status">Status</th>
								<th width="15%" class="sorting{{ $sort == 'updated_at'?$sort_dir:'' }}" data-sort="updated_at">Last Updated</th>
								<th width="8%">Action</th>
								<th width="8%">View</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Slug -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[slug]" value="{{ old('filter.slug') }}" placeholder="" />
								</th>
								<!-- Content -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[content]" value="{{ old('filter.content') }}" />
								</th>
								<!-- Status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.status')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>								
								<!-- Last Update -->
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
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($notifications as $notification)
							<tr class="odd gradeX" data-object='@json($notification)'>
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $notification->id }}" {{ Notification::enableStatusChanged($notification) }} /></td>
								<td>{{ $notification->slug }}</td>
								<td>{{ parse_json_multilang($notification->content, 'EN') }}</td>
								<td align="center"><span class="label label-{{ strtolower($statusList[$notification->status]) }}">{{ $statusList[$notification->status] }}</span></td>
								<td align="center">{{ format_date('Y-m-d', $notification->updated_at) }}</td>
								<td align="center"><a href="#" data-object="@json(new Notification())" class="btn btn-sm blue edit-link edit"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</a></td>
								<td align="center" class="drop">
									<a href="#notification_{{ $notification->id }}" class="action-link view-link" data-toggle="modal">View</a>
									<div id="notification_{{ $notification->id }}" class="modal fade view-modal" tabindex="-1" data-width="720">
	                                    <div class="modal-header">
	                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	                                        <h4 class="modal-title">Notification</h4>
	                                    </div>
	                                    <div class="modal-body">
	                                    	<div class="form-horizontal">
		                                    	<div class="form-group row margin-top-10">
		                                    		<div class="col-md-2 col-md-offset-1 bold">Slug</div>
			                                        <div class="col-md-9">
			                                    		{{ $notification->slug }}
			                                    	</div>
		                                    	</div>
												@foreach ( config('menu.lang_menu') as $lang => $menu )
		                                    	<div class="form-group row margin-top-10">
		                                    		<div class="col-md-2 col-md-offset-1 bold"><img src="/assets/images/common/lang_flags/{{ $lang }}.png">&nbsp;&nbsp;Content</div>
			                                        <div class="col-md-9">
			                                    		{!! parse_json_multilang($notification->content, $lang) !!}
			                                    	</div>
		                                    	</div>
												@endforeach
											</div>
										</div>
	                                    <div class="modal-footer">
	                                        <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
	                                    </div>
	                                </div>
								</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="7" align="center">No Notifications</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($notifications) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $notifications->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>

	<!-- Modal -->
	<div id="modal_notification_container"></div>

	@include ('pages.admin.super.settings.notification.modal')
</div>

@endsection