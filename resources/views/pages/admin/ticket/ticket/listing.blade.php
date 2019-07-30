<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Ticket;

?>
<form id="ticket_list" action="{{ route('admin.'.$role_id.(!empty($user)?'.user':'').'.ticket.list', ['tab' => $tab, 'user_id' => !empty($user)?$user->id:null]) }}" method="post">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="_action" value="" />
    <input type="hidden" name="assigner" value="" />

    {{ show_messages() }}
    <div class="row margin-bottom-10">
        <div class="col-md-4 margin-top-10">
            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($tickets) }}</div>
            <div class="padding-top-10">
            	<a href="#" class="clear-filter">Clear filters</a>
			</div>
        </div>
    	<div class="col-md-4">
    	</div>
        @if (false)
        @elseif (false)
	        <div class="col-md-4 text-center margin-top-10 action-required-label">
	            <input type="checkbox" id="action-required-chk" name="filter[action_required]" class="checkboxes" value="1" {{ old('filter.action_required') ? "checked" : "" }}/>Show Action Required Tickets Only
	        </div>
	    @endif
        <div class="col-md-4">
        	@if ($tab != 'archived' && ($auth_user->isSuper() || ($auth_user->isTicket() && $tab == 'openings')))
	        	<div class="toolbar toolbar-table pull-right">
					<span><strong>Action</strong>&nbsp;</span>
					<select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-status" data-auto-submit="false">
						<option value="">Select...</option>
						@if ($tab == 'opening' || $tab == 'mine')
							<option value="{{ Ticket::STATUS_SOLVED }}">Archive</option>
						@endif
						@if ($tab == 'archived')
							<option value="{{ Ticket::STATUS_CLOSED }}">Reopen</option>
						@endif
					</select>
					<!-- <button class="btn btn-sm yellow table-group-action-submit button-change-status" type="submit" disabled><i class="fa fa-check"></i> Submit</button> -->
					<button id="btn-submit" class="btn blue btn-sm yellow table-group-action-submit button-change-status" data-toggle="modal" data-target="#modal_archive" disabled>
						<i class="fa fa-check"></i> Submit
					</button>
				</div>
			@endif
        </div>
    </div>

	<div class="table-container">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr role="row" class="heading">
					<th width="2%"><input type="checkbox" class="group-checkable" /></th>
					<th width="5%" class="sorting{{ $sort == 'id'?$sort_dir:'' }}" 	data-sort="id">ID #</th>
					<th width="8%" class="sorting{{ $sort == 'priority'?$sort_dir:'' }}" 	data-sort="priority">Priority</th>
					<th width="15%" class="sorting{{ $sort == 'type'?$sort_dir:'' }}"     	data-sort="type">Type</th>
					<th             class="sorting{{ $sort == 'subject'?$sort_dir:'' }}"  	data-sort="subject">Title</th>
					<th width="15%" class="sorting{{ $sort == 'assigner'?$sort_dir:'' }}"  	data-sort="assigner">Assignee</th>
					<th width="13%" class="sorting{{ $sort == 'tickets.updated_at'?$sort_dir:'' }}" data-sort="tickets.updated_at">Updated At</th>
					<!-- <th width="10%" class="sorting">Actions</th> -->
				</tr>
				<tr role="row" class="filter">
					<th>&nbsp;</th>
					<th><input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" /></th>
					<th>
						<select name="filter[priority]" class="form-control form-filter input-sm select2" data-with-color="true">
							<option value="">Select...</option>
							@foreach (Ticket::getOptions('priority') as $name => $priority)
								<option value="{{ $priority }}" {{ $priority == old('filter.priority')?'selected':'' }}>{{ $name }}</option>
							@endforeach
						</select>
					</th>
					<th>
						<select name="filter[type]" class="form-control form-filter input-sm select2" data-with-colored-icon="true">
							<option value="">Select...</option>
							@foreach (Ticket::getOptions('type') as $name => $type)
								<option value="{{ $type }}" {{ $type == old('filter.type')?'selected':'' }} data-icon="{{ Ticket::iconByType($type) }}">{{ $name }}</option>
							@endforeach
						</select>
					</th>
					<th>
						<div class="row">
							<div class="col-md-8">
								<input type="text" class="form-control form-filter input-sm" name="filter[subject]" value="{{ old('filter.subject') }}" />
							</div>
							<div class="col-md-4">
								<select name="filter[new]" class="form-control form-filter input-sm select2">
									<option value="">Select...</option>
									<option value="is_new" {{ old('filter.new') == 'is_new'?'selected':'' }}>New Ticket</option>
									<option value="is_unread_admin_msg" {{ old('filter.new') == 'is_unread_admin_msg'?'selected':'' }}>New admin messages</option>
								</select>
							</div>
						</div>
					</th>
					<th>
						<select name="filter[assigner]" class="form-control form-filter input-sm select2" data-select2-show-users="1">
							<option value="">Select...</option>
							<option value="-1" {{ -1 == old('filter.assigner') ? 'selected' : '' }}>-</option>
							@foreach ($ticket_managers as $admin)
								<option data-role-css="{{ $admin['user']->role_css_class() }}" data-role-name="{{ $admin['user']->role_name() }}" data-role-short-name="{{ $admin['user']->role_short_name() }}" value="{{ $admin['id'] }}" {{ $admin['id'] == old('filter.assigner') ? 'selected' : '' }}>{{ $admin['name'] }}</option>
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
							<input type="text" class="form-control form-filter input-sm" readonly name="filter[updated_at][to]" placeholder="To" value="{{ old('filter.updated_at.to') }}" data-value="{{ old('filter.updated_at.to') }}" />
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
			@forelse ($tickets as $t)
				<tr class="odd gradeX">
                    <td align="center"><input type="checkbox" class="checkboxes {{ $tab == 'archived' || (!$auth_user->isSuper() && $t->type == Ticket::TYPE_DISPUTE)?'disable-assign':'' }} {{ $t->admin_id?'assigned':'' }}" name="ids[]" value="{{ $t->id }}" {{ Ticket::enableStatusChanged($t, $me) }} /></td>
                    <td align="center">{{ $t->id }}</td>
                    <td align="center">
                    	<span class="label label-sm label-{{ strtolower(array_search($t->priority, Ticket::getOptions('priority'))) }}">
                    		{{ array_search($t->priority, Ticket::getOptions('priority')) }}
                    	</span>
                    </td>
                    <td>
                    	<div class="label-color-icon label-{{ str_replace(' ', '-', strtolower(array_search($t->type, Ticket::getOptions('type')))) }}">
                    		<i class="fa {{ Ticket::iconByType($t->type) }}"></i>
                    	</div>
                    	<div class="label-text">{{ array_search($t->type, Ticket::getOptions('type')) }}</div>
                    </td>
                    <td class="with-ribbon {{ !$t->isAssigned() && $t->isUnread()?'is-new':'' }}">
                    <?php
                    	$ticket_route = !empty($user)?route('admin.'.$role_id.'.user.ticket.detail', ['id' => $t->id, 'user_id' => $user->id]):route('admin.'.$role_id.'.ticket.detail', ['id' => $t->id]);
                    	$ticket_private_msg_route = !empty($user)?route('admin.'.$role_id.'.user.ticket.msg_admin', ['id' => $t->id, 'user_id' => $user->id]):route('admin.'.$role_id.'.ticket.msg_admin', ['id' => $t->id]);
                    ?>
                    	<a href="{{ $ticket_route }}">{{ $t->subject }}</a>

                    	@if (!$t->isAssigned())
                    		@if ($t->isUnread())
							<div class="corner-ribbon top-right shadow blue"><a href="{{ $ticket_route }}">New</a></div>
							@endif
                    	@endif

                    	@if ($t->unread_messages)
                    		<div class="unread-messages" data-toggle="tooltip" data-placement="left" title="Unread Comments">
								<span class="badge badge-primary"><a href="{{ $ticket_route}}">{{ $t->unread_messages }}</a></span>
							</div>
                    	@endif

                    	@if ($t->unread_admin_messages)
                    		<div class="unread-admin-messages" data-toggle="tooltip" data-placement="left" title="{{ $auth_user->isSuper()?'Unread Private Message':'Unread Admin Messages' }}">
								<span class="badge badge-warning"><a href="{{ $ticket_private_msg_route }}">{{ $t->unread_admin_messages }}</a></span>
							</div>
                    	@endif
                    </td>
                    <td data-assigner-id="{{ $t->admin_id }}">
                    	{!! $t->admin? $t->admin->getUserNameWithIcon() : "-" !!}
                    </td>
                    <td align="center">{{ format_date('Y-m-d H:i', $t->updated_at) }}</td>
                    <!-- <td>&nbsp;</td> -->
				</tr>
            @empty
                <tr class="odd gradeX">
                    <td colspan="8" align="center">No Tickets</td>
                </tr>
            @endforelse
			</tbody>
		</table>
        <div class="row">
            <div class="col-md-6">
                <div role="status" aria-live="polite">{{ render_admin_paginator_desc($tickets) }}</div>
            </div>
            <div class="col-md-6">
                <div class="datatable-paginate pull-right">{!! $tickets->render() !!}</div>
            </div>
        </div>
	</div>
</form>
@if ($user)
	@include('pages.admin.ticket.ticket.create_modal')
@endif

@include('pages.admin.ticket.ticket.assign_modal')
@include('pages.admin.ticket.ticket.solve_modal')