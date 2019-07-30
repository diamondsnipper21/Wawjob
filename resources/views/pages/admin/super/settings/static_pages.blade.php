<?php
/**
* Static Page on Super Admin Settings
*
* @author PYH
* @since Aug 9, 2017
* @version 1.0
*/

use iJobDesk\Models\StaticPage;

?>
@extends('layouts/admin/super')

@section('content')
<div id="static_pages">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Static Pages</span>
	        </div>
	        <div class="pull-right">
	            <a class="btn green edit-modal-link" href="{{ route('admin.super.settings.static_page.edit') }}">Add New <i class="fa fa-plus"></i></a>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($static_pages) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
			        		<span><strong>Action</strong>&nbsp;</span>
							<select name="page_action" id="page_action" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="{{ StaticPage::STATUS_NO_PUBLISH }}">No Publish</option>
								<option value="{{ StaticPage::STATUS_PUBLISH }}">Publish</option>
								<option value="{{ StaticPage::STATUS_DELETE }}">Delete</option>
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
								<th width="" class="sorting{{ $sort == 'title'?$sort_dir:'' }}"      data-sort="title">Title</th>
								<th width="15%" class="sorting{{ $sort == 'slug'?$sort_dir:'' }}" 	    data-sort="slug">Slug</th>
								<th width="12%" class="sorting{{ $sort == 'keyword'?$sort_dir:'' }}"    data-sort="keyword">Keyword</th>
								<th width="15%" class="sorting{{ $sort == 'desc'?$sort_dir:'' }}"  	    data-sort="desc">Description</th>
								<th width="8%"  class="sorting{{ $sort == 'is_publish'?$sort_dir:'' }}" data-sort="is_publish">Is Publish</th>
								<th width="8%" >Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- title -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[title]" value="{{ old('filter.title') }}" placeholder="" />
								</th>
								<!-- slug -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[slug]" value="{{ old('filter.slug') }}" />
								</th>
								<!-- keyword -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[keyword]" value="{{ old('filter.keyword') }}" />
								</th>
								<!-- description -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[desc]" value="{{ old('filter.desc') }}" />
								</th>
								<!-- status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										<option value="{{ StaticPage::STATUS_NO_PUBLISH }}" {{ old('filter.status') != '' && StaticPage::STATUS_NO_PUBLISH == old('filter.status')?'selected':'' }}>No Published</option>
										<option value="{{ StaticPage::STATUS_PUBLISH }}"  {{ StaticPage::STATUS_PUBLISH == old('filter.status')?'selected':'' }}>Published</option>
									</select>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($static_pages as $static_page)
								<tr class="odd gradeX">
				                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $static_page->id }}"  {{ StaticPage::enableStatusChanged($static_page) }} /></td>
									<td>{{ parse_json_multilang($static_page->title, 'EN') }}</td>
									<td>{{ $static_page->slug }}</td>
									<td>{{ parse_json_multilang($static_page->keyword, 'EN') }}</td>
									<td>{{ parse_json_multilang($static_page->desc, 'EN') }}</td>
									<td align="center">{{ $static_page->is_publish==1?'Yes':'No' }}</td>
									<td align="center"><a href="{{ route('admin.super.settings.static_page.edit', ['id' => $static_page->id]) }}" class="btn btn-sm blue edit-modal-link edit"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</a></td>
								</tr>
							@empty
				                <tr class="odd gradeX">
				                    <td colspan="7" align="center">No Static Pages</td>
				                </tr>
							@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($static_pages) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $static_pages->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>

@endsection