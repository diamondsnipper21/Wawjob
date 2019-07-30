<?php
/**
* Cronjob Overview Page on Super Admin
*
* @author Ro Un Nam
* @since Dec 12, 2017
* @version 1.0
*/

use iJobDesk\Models\Cronjob;
?>
@extends('layouts/admin/super')

@section('content')

<div id="cronjobs">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-bar-chart font-green-sharp hide"></i>
                <span class="caption-helper"><span class="caption-subject font-green-sharp bold"><i class="icon-user"></i>&nbsp;&nbsp;Cronjobs</span></span>
            </div>
        </div>
        <div class="portlet-body">
		    <form action="{{ route('admin.super.cronjobs') }}" class="form-datatable" method="post">
		        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
		        <input type="hidden" name="_action" value="" />

	            {{ show_messages() }}

			    <div class="row margin-bottom-10">
			        <div class="col-md-6 pull-right">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="{{ Cronjob::STATUS_PROCESSING }}">Process</option>
								<option value="{{ Cronjob::STATUS_READY }}">Enable</option>
								<option value="{{ Cronjob::STATUS_DISABLED }}">Disable</option>
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
								<th class="sorting{{ $sort == 'type' ? $sort_dir : '' }}" data-sort="type">Type</th>
								<th width="15%">Frequency</th>
								<th class="sorting{{ $sort == 'max_runtime' ? $sort_dir : '' }}" data-sort="max_runtime" width="15%">Max Runtime</th>
								<th class="sorting{{ $sort == 'done_at' ? $sort_dir : '' }}" data-sort="done_at" width="15%">Done At</th>
								<th class="sorting{{ $sort == 'status' ? $sort_dir : '' }}" data-sort="status" width="15%">Status</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[type]" value="{{ old('filter.type') }}" />
								</th>
								<th></th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[max_runtime]" value="{{ old('filter.max_runtime') }}" />
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
								<!-- Status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2">
										<option value="">Select...</option>
										@foreach (Cronjob::getOptions('status') as $status => $label)
											<option value="{{ $status }}" {{ ("$status" == old('filter.status') || "$status" == $filter['status']) ? 'selected' : '' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($cronjobs as $inx => $cr)
							<tr{{ $cr->isDisabled() ? ' class=disabled' : '' }}>
								<td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $cr->id }}" {{ Cronjob::availableActionsByStatus($cr->status) }} /></td>
								<td>
									{{ $cr->type_string() }}
									<i class="icon icon-question pull-right mr-2" data-toggle="tooltip" data-placement="top" title="{{ $cr->cronType->desc }}"></i>
								</td>
								<td>{{ $cr->cronType->frequency }}</td>
								<td>{{ $cr->max_runtime }} sec(s)</td>
								<td>{{ $cr->done_at }}</td>
								<td>{{ $cr->status_string() }}</td>
							</tr>
							@empty
							<tr>
								<td colspan="6">No Cronjobs</td>
							</tr>
							@endforelse
						</tbody>
					</table>
	            </div>
			</form>
        </div>
    </div>
</div>


@endsection