<?php
/**
* Skills Listing Page on Super Admin
*
* @author KCG
* @since April 04, 2017
* @version 1.0
*/

use iJobDesk\Models\EmailTemplate;

?>
@extends('layouts/admin/super')

@section('content')
<div id="skills">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Skills</span>
	        </div>
	        <div class="tools">
	            <button class="btn green edit-modal-link" data-url="{{ route('admin.super.settings.skill.edit') }}">Add New <i class="fa fa-plus"></i></button>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($skills) }}</div>
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
								<th class="sorting{{ $sort == 'name'?$sort_dir:'' }}" data-sort="name">Name</th>
								<th width="60%">Desc</th>
								<th width="10%" >Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Name -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[name]" value="{{ old('filter.name') }}" placeholder="" />
								</th>
								<!-- Desc -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[desc]" value="{{ old('filter.desc') }}" />
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($skills as $skill)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $skill->id }}" data-status-DELETE="true" data-status-{{ $skill->status == EmailTemplate::STATUS_ENABLE?'DISABLE':'ENABLE' }}="true" /></td>
								<td>{{ $skill->name }}</td>
								<td>{{ $skill->desc }}</td>
								<td align="center"><a href="#" data-url="{{ route('admin.super.settings.skill.edit', ['id' => $skill->id]) }}" class="btn btn-sm blue edit-modal-link"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</a></td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="4" align="center">No Skills</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($skills) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $skills->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>

	<!-- Modal -->
	<div id="modal_skill_page_container"></div>
</div>

@endsection