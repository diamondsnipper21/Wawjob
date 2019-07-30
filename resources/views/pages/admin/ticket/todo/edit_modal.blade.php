<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\Todo;
use iJobDesk\Models\File;

?>

<div id="modal_todo" class="modal fade modal-scroll" tabindex="-1" data-width="70%" aria-hidden="true" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{{ empty($todo->id)?'New':'Edit' }} TODO</h4>
	</div>
	<form action="{{ route('admin.' . $role_id . '.todo.edit', ['id' => $todo->id]) }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		
		<div class="modal-body">
			<div class="alert alert-danger display-hide">
				<button class="close" data-close="alert"></button>
				You have some form errors. Please check below.
			</div>
			<div class="alert alert-success display-hide" style="display: none;">
				<button class="close" data-close="alert"></button>
				@if (empty($todo->id))
					New ToDo has been created successfully.
				@else
					#{{ $todo->id }} ToDo has been updated successfully.
				@endif
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Subject&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<input type="text" class="form-control" name="subject" data-rule-required="true" value="{{ $todo->subject }}" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Type&nbsp;<span class="required">*</span></label>
				<div class="col-md-5">
					<select name="type" class="form-control form-filter input-sm select2" data-rule-required="true">
						<option value="">Select...</option>
						@foreach (Todo::options('type') as $name => $type)
						<option value="{{ $type }}" {{ $type == $todo->type?'selected':'' }}>{{ $name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Assign To&nbsp;<span class="required">*</span></label>
				<div class="col-md-5">
					<select id="assigners" name="assigner_ids[]" class="form-control form-filter input-sm select2" multiple data-placeholder="Choose assginers" data-rule-required="true" data-select2-show-users="1">
						@foreach ($admins as $admin)
						<option data-role-css="{{ $admin['user']->role_css_class() }}" data-role-name="{{ $admin['user']->role_name() }}" data-role-short-name="{{ $admin['user']->role_short_name() }}" value="{{ $admin['id'] }}" {{ array_search($admin['id'], explode_bracket($todo->assigner_ids)) !== FALSE?'selected':'' }}>{{ $admin['name'] }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Priority&nbsp;<span class="required">*</span></label>
				<div class="col-md-5">
					<select name="priority" class="form-control form-filter input-sm select2" data-rule-required="true" data-with-color="1">
						<option value="">Select...</option>
						@foreach (Todo::options('priority') as $name => $priority)
						<option value="{{ $priority }}" {{ $priority == $todo->priority?'selected':'' }}>{{ $name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Due Date&nbsp;<span class="required">*</span></label>
				<div class="col-md-5">
					<div class="input-icon">
						<i class="fa fa-calendar"></i>
						<input type="text" name="due_date" class="form-control datepicker-due-date" placeholder="Due Date..." data-rule-required="true" value="{{ !empty($todo->due_date)?format_date('m/d/Y', $todo->due_date):format_date('m/d/Y', date('Y-m-d H:i:s')) }}" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Related Ticket</label>
				<div class="col-md-5">
					<select name="related_ticket_id" class="form-select2-control select2-ajax select2-related-ticket" data-placeholder="Search for a ticket" data-ajax-url="{{ route('admin.' . $role_id . '.todo.tickets') }}">
						@if ($todo->related_ticket_id)
			            <option value="{{ $todo->related_ticket_id }}" selected>{{ $todo->related_ticket->subject }}</option>
			            @endif
			        </select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Comment&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<textarea name="description" class="form-control maxlength-handler" rows="5" maxlength="1000" data-rule-required="true">{{ $todo->description }}</textarea>
				</div>
			</div>

			<div class="form-group {{ empty($todo->id)?'hidden':'' }}">
				<label class="col-md-2 col-md-offset-1 control-label">Status&nbsp;<span class="required">*</span></label>
				<div class="col-md-5">
					<select name="status" class="form-control form-filter input-sm select2" data-rule-required="true">
						@foreach (Todo::options('status') as $name => $status)
						<option value="{{ $status }}" {{ $status == $todo->status?'selected':'' }}>{{ $name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3">&nbsp;</label>
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-6">{!! render_file_element(File::TYPE_TODO, $todo->files) !!}</div>
						<div class="col-md-6 text-right padding-top-40">
							<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
							<button type="submit" class="save-button btn blue">Save</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>