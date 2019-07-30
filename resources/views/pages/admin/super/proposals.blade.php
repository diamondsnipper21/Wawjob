<?php
/**
* Freelancer Proposals Page on Super Admin
*
* @author KCG
* @since July 13, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;

$statusList = ProjectApplication::getOptions('status');

?>
@extends('layouts/admin/super'.(!empty($user)?'/user':''))

@section('content')
<div id="proposals">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Proposals</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($proposals) }}</div>
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
								<th width="5%"  class="sorting{{ $sort == 'id'?$sort_dir:'' }}"                         data-sort="id">ID #</th>
								<th width="6%" class="sorting{{ $sort == 'type'?$sort_dir:'' }}" 			data-sort="type">Type</th>
								<th width="12%" class="sorting{{ $sort == 'created_at'?$sort_dir:'' }}" 	data-sort="created_at">Date</th>
								<th             class="sorting{{ $sort == 'project_title'?$sort_dir:'' }}"  data-sort="project_title">Job Title</th>
								@if (empty($user) || $user->isFreelancer())
								<th width="12%" class="sorting{{ $sort == 'buyer_name'?$sort_dir:'' }}"     data-sort="buyer_name">Buyer</th>
								@endif
								@if (empty($user))
								<th width="12%" class="sorting{{ $sort == 'freelancer_name'?$sort_dir:'' }}"data-sort="freelancer_name">Freelancer</th>
								@endif
								<th width="6%">Invited</th>
								<th width="12%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}" 		data-sort="status">Status</th>
								<th width="5%">Actions</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Id -->
								<th>
	                                <input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
	                            </th>
								<!-- Type -->
								<th>
									<select name="filter[type]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										@foreach (Project::options('type') as $type => $label)
										<option value="{{ $type }}" {{ "$type" == old('filter.type')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
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
								<!-- Job Title -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[project_title]" value="{{ old('filter.project_title') }}" placeholder="#Job ID or Title" />
								</th>
								<!-- Buyer Name -->
								@if (empty($user) || $user->isFreelancer())
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[buyer_name]" value="{{ old('filter.buyer_name') }}" placeholder="#ID or Name" />
								</th>
								@endif
								<!-- Freelancer Name -->
								@if (empty($user))
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[freelancer_name]" value="{{ old('filter.freelancer_name') }}" placeholder="#ID or Name" />
								</th>
								@endif
								<!-- Status -->
								<th>
									<select name="filter[invited]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										<option value="1" {{ "1" == old('filter.invited')?'selected':'' }}>Yes</option>
										<option value="0" {{ "0" == old('filter.invited')?'selected':'' }}>No</option>
									</select>
								</th>
								<!-- Status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $label => $status)
											@if ($status !== ProjectApplication::STATUS_HIRING_CLOSED && $status !== ProjectApplication::STATUS_PROJECT_EXPIRED)
											<option value="{{ $status }}" {{ "$status" == old('filter.status')?'selected':'' }}>{{ $label }}</option>
											@endif
										@endforeach
									</select>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($proposals as $proposal)
						<?php
							$proposal->status = $proposal->is_declined == ProjectApplication::IS_FREELANCER_DECLINED || $proposal->is_declined == ProjectApplication::IS_CLIENT_DECLINED?ProjectApplication::STATUS_WITHDRAWN:$proposal->status;
							
							$status_label = array_search($proposal->status, $statusList);
							$status_label = ($proposal->status == ProjectApplication::STATUS_HIRING_CLOSED || $proposal->status == ProjectApplication::STATUS_PROJECT_EXPIRED?'expired':$status_label);
						?>
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $proposal->id }}" data-status-DELETE="true" /></td>
			                    <td align="center">{{ $proposal->id }}</td>
								<td align="center">{{ Project::options('type')[$proposal->type] }}</td>
								<td align="center">{{ format_date('Y-m-d H:i:s', $proposal->created_at) }}</td>
								<td><a href="{{ route('admin.super.job.overview', ['user_id' => $proposal->project_id]) }}">{{ $proposal->project_title }}</a></td>
								
								@if (empty($user) || $user->isFreelancer())
								<td><a href="{{ route('admin.super.user.overview', ['user_id' => $proposal->buyer_id]) }}">{!! $proposal->project->user->fullname(true) !!}</a></td>
								@endif

								@if (empty($user) || $user->isBuyer())
								<td><a href="{{ route('admin.super.user.overview', ['user_id' => $proposal->freelancer_id]) }}">{!! $proposal->user->fullname(true) !!}</a></td>
								@endif
								
								<td align="center">{{ $proposal->provenance == ProjectApplication::PROVENANCE_INVITED?'Yes':'No' }}</td>
								<td align="center"><span class="label label-{{ strtolower($status_label) }}">{{ $status_label }}</span></td>
								<td align="center">
									@if (empty($user))
										<a href="{{ route('admin.super.proposal', ['proposal_id' => $proposal->id]) }}" class="blue">View</a>
									@else
										<a href="{{ route('admin.super.user.freelancer.proposal', ['user_id' => $user->id, 'proposal_id' => $proposal->id]) }}" class="blue">View</a>
									@endif
								</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="{{ empty($user)?10:10 }}" align="center">No Proposals</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($proposals) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $proposals->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection