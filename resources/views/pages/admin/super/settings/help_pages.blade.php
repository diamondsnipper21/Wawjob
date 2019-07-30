<?php
/**
* Help Page on Super Admin Settings
*/

use iJobDesk\Models\HelpPage;

?>
@extends('layouts/admin/super')

@section('content')
<div id="help_pages">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Help Pages</span>
	        </div>
	        <div class="pull-right">
	            <a class="btn green edit-modal-link" href="{{ route('admin.super.settings.help_page.edit') }}">Add New <i class="fa fa-plus"></i></a>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($help_pages) }}</div>
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
								<option value="{{ HelpPage::STATUS_NO_PUBLISH }}">No Publish</option>
								<option value="{{ HelpPage::STATUS_PUBLISH }}">Publish</option>
								<option value="{{ HelpPage::STATUS_DELETE }}">Delete</option>
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
								<th class="sorting{{ $sort == 'title'?$sort_dir:'' }}" data-sort="title">Title</th>
								<th width="10%" class="sorting{{ $sort == 'type'?$sort_dir:'' }}" data-sort="type">Type</th>
								<th width="20%" class="sorting{{ $sort == 'parent_id'?$sort_dir:'' }}" data-sort="parent_id">Main Category</th>
								<th width="5%" class="sorting{{ $sort == 'order'?$sort_dir:'' }}" data-sort="order">Pos</th>
								<th width="20%" class="sorting{{ $sort == 'second_parent_id'?$sort_dir:'' }}" data-sort="second_parent_id">Second Category</th>
								<th width="5%" class="sorting{{ $sort == 'second_order'?$sort_dir:'' }}" data-sort="second_order">Pos</th>
								<th width="10%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}" data-sort="status">Is Publish</th>
								<th width="8%">Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- title -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[title]" value="{{ old('filter.title') }}" placeholder="" />
								</th>

								<!-- type -->
								<th>
									<select name="filter[type]" class="form-control form-filter input-sm select2" id="type">
										<option value="0">All</option>
										<option value="1" {{ old('filter.type') != '' && HelpPage::TYPE_FREELANCER == old('filter.type')?'selected':'' }}>Freelancer</option>
										<option value="2" {{ HelpPage::TYPE_BUYER == old('filter.type')?'selected':'' }}>Buyer</option>
									</select>
								</th>
								<!-- parent -->
								<th>
									<select name="filter[parent_id]" class="form-control form-filter input-sm select2-category">
										<option value="0" {{ old('filter.parent_id') === 0 ? 'selected' : '' }} data-for="0">Select</option>
										@if ( count($parent_pages) )
											@foreach ( $parent_pages as $parent )
											<option value="{{ $parent->id }}" {{ old('filter.parent_id') == $parent->id ? 'selected' : '' }} data-for="{{ $parent->type }}">{{ parse_json_multilang($parent->title, 'en') }} {{ HelpPage::TYPE_FREELANCER == $parent->type?'(Freelancer)':(HelpPage::TYPE_BUYER == $parent->type?'(Buyer)':'') }}</option>
												@if ( count($parent->child) )
													@foreach ( $parent->child as $child )
													<option value="{{ $child->id }}" {{ old('filter.parent_id') == $child->id ? 'selected' : '' }} data-parent="{{ $parent->id }}" data-for="{{ $child->type }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ parse_json_multilang($child->title, 'en') }}</option>
													@endforeach
												@endif			
											@endforeach
										@endif
									</select>
								</th>

								<!-- Position -->
								<th>&nbsp;</th>

								<!-- second parent -->
								<th>
									<select name="filter[second_parent_id]" class="form-control form-filter input-sm select2-category">
										<option value="0" {{ old('filter.second_parent_id') === 0 ? 'selected' : '' }} data-for="0">Select</option>
										@if ( count($parent_pages) )
											@foreach ( $parent_pages as $parent )
											<option value="{{ $parent->id }}" {{ old('filter.second_parent_id') == $parent->id ? 'selected' : '' }} data-for="{{ $parent->type }}">{{ parse_json_multilang($parent->title, 'en') }} {{ HelpPage::TYPE_FREELANCER == $parent->type?'(Freelancer)':(HelpPage::TYPE_BUYER == $parent->type?'(Buyer)':'') }}</option>
												@if ( count($parent->child) )
													@foreach ( $parent->child as $child )
													<option value="{{ $child->id }}" {{ old('filter.second_parent_id') == $child->id ? 'selected' : '' }} data-parent="{{ $parent->id }}" data-for="{{ $child->type }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ parse_json_multilang($child->title, 'en') }}</option>
													@endforeach
												@endif			
											@endforeach
										@endif
									</select>
								</th>

								<!-- Position -->
								<th>&nbsp;</th>
								<!-- status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										<option value="{{ HelpPage::STATUS_NO_PUBLISH }}" {{ old('filter.status') != '' && HelpPage::STATUS_NO_PUBLISH == old('filter.status')?'selected':'' }}>No Published</option>
										<option value="{{ HelpPage::STATUS_PUBLISH }}"  {{ HelpPage::STATUS_PUBLISH == old('filter.status')?'selected':'' }}>Published</option>
									</select>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($help_pages as $help_page)
								<tr class="odd gradeX">
				                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $help_page->id }}" {{ HelpPage::enableStatusChanged($help_page) }} /></td>
									<td>{{ parse_json_multilang($help_page->title, 'EN') }}</td>
									<td align="center">{{ $help_page->type==1?'Freelancer':($help_page->type==2?'Buyer':'All') }}</td>
									<td>
										@if ( $help_page->parent )
											{{ parse_json_multilang($help_page->parent->title, 'EN') }}
										@else

										@endif
									</td>
									<td align="center">{{ $help_page->order }}</td>
									<td>
										@if ( $help_page->second_parent )
											{{ parse_json_multilang($help_page->second_parent->title, 'EN') }}
										@else

										@endif
									</td>
									<td align="center">{{ $help_page->second_order }}</td>
									<td align="center">{{ $help_page->status==1?'Yes':'No' }}</td>
									<td align="center"><a href="{{ route('admin.super.settings.help_page.edit', ['id' => $help_page->id]) }}" class="btn btn-sm blue edit-modal-link edit"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</a></td>
								</tr>
							@empty
				                <tr class="odd gradeX">
				                    <td colspan="12" align="center">No Help Pages</td>
				                </tr>
							@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($help_pages) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $help_pages->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>

@endsection