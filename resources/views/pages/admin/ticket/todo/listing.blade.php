<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Todo;

?>
<form id="todo_list" action="{{ route('admin.' . $role_id . '.todo.list', ['tab' => $tab]) }}" method="post">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="_action" value="" />

    {{ show_messages() }}

    <div class="row margin-bottom-10">
        <div class="col-md-4 margin-top-10">
            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($todos) }}</div>
        </div>
        <div class="col-md-4 text-center margin-top-10 filter-show-only-mine">
            <label>
            	<input type="checkbox" name="filter[show_only_mine]" {{ old('filter.show_only_mine') == 'on'?'checked':'' }} />
            	Show my todos only
           	</label>
        </div>
        <div class="col-md-4">
        	<div class="toolbar toolbar-table pull-right">
				<span><strong>Action</strong>&nbsp;</span>
				<select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-status" data-auto-submit="false">
					<option value="">Select...</option>
					@if ($tab == 'opening')
					<option value="{{ Todo::STATUS_COMPLETE }}">Complete</option>
					<option value="{{ Todo::STATUS_CANCEL }}">Cancel</option>
					@else
					<option value="{{ Todo::STATUS_OPEN }}">Reopen</option>
					@endif
				</select>
				<button class="btn btn-sm yellow table-group-action-submit button-change-status" type="submit" disabled><i class="fa fa-check"></i> Submit</button>
			</div>
        </div>
    </div>

    <div class="row margin-bottom-10">
     	<div class="col-md-6 margin-top-10">
            <a href="#" class="clear-filter">Clear filters</a>
        </div>
    </div>

	<div class="table-container">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr role="row" class="heading">
					<th width="2%"><input type="checkbox" class="group-checkable" /></th>
					<th width="5%"  class="sorting{{ $sort == 'id'?$sort_dir:'' }}"  			data-sort="id">ID #</th>
					<th             class="sorting{{ $sort == 'subject'?$sort_dir:'' }}"  		data-sort="subject">Subject</th>
					<th width="9%"  class="sorting{{ $sort == 'priority'?$sort_dir:'' }}" 		data-sort="priority">Priority</th>
					<th width="9%"  class="sorting{{ $sort == 'type'?$sort_dir:'' }}"     		data-sort="type">Type</th>
					<th width="13%" class="sorting{{ $sort == 'creator'?$sort_dir:'' }}" 		data-sort="creator">Creator</th>
					<th width="13%" class="sorting{{ $sort == 'assigner_names'?$sort_dir:'' }}" data-sort="assigner_names">Assign To</th>
					@if ($tab != 'opening')
					<th width="10%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}" 			data-sort="status">Status</th>
					@endif
					<th width="12%" class="sorting{{ $sort == 'due_date'?$sort_dir:'' }}"   	data-sort="due_date">Due Date</th>
					<th width="12%" class="sorting{{ $sort == 'created_at'?$sort_dir:'' }}" 	data-sort="created_at">Created At</th>
					<!-- <th width="10%" class="sorting">Actions</th> -->
				</tr>
				<tr role="row" class="filter">
					<th>&nbsp;</th>
					<th>
						<input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
					</th>
					<th>
						<input type="text" class="form-control form-filter input-sm" name="filter[subject]" value="{{ old('filter.subject') }}" />
					</th>
					<th>
						<select name="filter[priority]" class="form-control form-filter input-sm select2" data-with-color="1">
							<option value="">Select...</option>
							@foreach (Todo::options('priority') as $name => $priority)
							<option value="{{ $priority }}" {{ $priority == old('filter.priority')?'selected':'' }}>{{ $name }}</option>
							@endforeach
						</select>
					</th>
					<th>
						<select name="filter[type]" class="form-control form-filter input-sm select2">
							<option value="">Select...</option>
							@foreach (Todo::options('type') as $name => $type)
							<option value="{{ $type }}" {{ $type == old('filter.type')?'selected':'' }}>{{ $name }}</option>
							@endforeach
						</select>
					</th>
					<th>
						<select name="filter[creator]" class="form-control form-filter input-sm select2" data-select2-show-users="1">
							<option value="">Select...</option>
							@foreach ($creators as $name => $creator)
							<option data-role-css="{{ $creator->role_css_class() }}" data-role-name="{{ $creator->role_name() }}" data-role-short-name="{{ $creator->role_short_name() }}" value="{{ $creator->id }}" {{ $creator->id == old('filter.creator')?'selected':'' }}>{{ $creator->fullname() }}</option>
							@endforeach
						</select>
					</th>
					<th>
						<select name="filter[assigner_names]" class="form-control form-filter input-sm select2" data-select2-show-users="1">
							<option value="">Select...</option>
							@foreach ($assigners as $name => $assigner)
							<option data-role-css="{{ $assigner->role_css_class() }}" data-role-name="{{ $assigner->role_name() }}" data-role-short-name="{{ $assigner->role_short_name() }}" value="{{ $assigner->id }}" {{ $assigner->id == old('filter.assigner_names')?'selected':'' }}>{{ $assigner->fullname() }}</option>
							@endforeach
						</select>
					</th>
					@if ($tab != 'opening')
					<th>
						<select name="filter[status]" class="form-control form-filter input-sm select2 select2" data-with-color="1">
							<option value="">Select...</option>
							@foreach (Todo::options('status') as $name => $status)
								@if ($status != Todo::STATUS_OPEN))
								<option value="{{ $status }}" {{ $status == old('filter.status')?'selected':'' }}>{{ $name }}</option>
								@endif
							@endforeach
						</select>
					</th>
					@endif
					<th>
						<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
							<input type="text" class="form-control form-filter input-sm" readonly name="filter[due_date][from]" placeholder="From" value="{{ old('filter.due_date.from') }}" data-value="{{ old('filter.due_date.from') }}" />
							<span class="input-group-btn">
								<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
						<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
							<input type="text" class="form-control form-filter input-sm" readonly name="filter[due_date][to]" placeholder="To" value="{{ old('filter.due_date.to') }}" data-value="{{ old('filter.due_date.to') }}" />
							<span class="input-group-btn">
								<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
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
					<!-- <th>
						<div class="margin-bottom-5">
							<button class="btn btn-sm yellow filter-submit margin-bottom" type="submit"><i class="fa fa-search"></i> Search</button>
						</div>
						<button class="btn btn-sm red filter-cancel" type="reset"><i class="fa fa-times"></i> Reset</button>
					</th> -->
				</tr>
			</thead>
			<tbody>
			@forelse ($todos as $t)
				<tr class="odd gradeX">
					@if ($tab == 'opening')
					<td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $t->id }}" data-status-{{ Todo::STATUS_COMPLETE }}="true" data-status-{{ Todo::STATUS_CANCEL }}="true" /></td>
					@else
					<td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $t->id }}" data-status-{{ Todo::STATUS_OPEN }}="true" /></td>
					@endif
					<td align="center">{!! $t->isOverdue()?'<span class="label label-warning" data-toggle="tooltip" title="Overdue">'.$t->id.'</span>':$t->id !!}</td>
                    <td>
                    		<a href="{{ route('admin.' . $role_id . '.todo.detail', ['id' => $t->id]) }}">{{ $t->subject }}</a>
                    		&nbsp;&nbsp;
                    		@if ($t->unread_message_count != 0)
                    		<i class="fa fa-comment-o new-message-label" data-toggle="tooltip" title="Unread {{ $t->unread_message_count }} Messages"></i>
                    		@endif
                    </td>
                    <td align="center"><span class="label label-{{ strtolower(array_search($t->priority, Todo::options('priority'))) }}">{{ array_search($t->priority, Todo::options('priority')) }}</span></td>
                    <td align="center">{{ array_search($t->type, Todo::options('type')) }}</td>
                    <td>{!! $t->creator->getUserNameWithIcon() !!}</td>
                    <td>{!! $t->getAssignerNames() !!}</td>
                    @if ($tab != 'opening')
                    <td align="center">{{ array_search($t->status, Todo::options('status')) }}</td>
                    @endif
                    <td align="center">{{ format_date('Y-m-d H:i', $t->due_date) }}</td>
                    <td align="center">{{ format_date('Y-m-d H:i', $t->created_at) }}</td>
                    <!-- <td>&nbsp;</td> -->
				</tr>
            @empty
                <tr class="odd gradeX">
                    <td colspan="{{ $tab != 'opening'?10:9 }}" align="center">No ToDos</td>
                </tr>
            @endforelse
			</tbody>
		</table>
        <div class="row">
            <div class="col-md-6">
                <div role="status" aria-live="polite">{{ render_admin_paginator_desc($todos) }}</div>
            </div>
            <div class="col-md-6">
                <div class="datatable-paginate pull-right">{!! $todos->render() !!}</div>
            </div>
        </div>
	</div>
</form>
@include('pages.admin.ticket.todo.edit_modal')