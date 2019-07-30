<?php

use iJobDesk\Models\User;

?>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr role="row" class="heading">
			<th width="2%"><input type="checkbox" class="group-checkable" /></th>
			<th width="5%"  class="sorting{{ $sort == 'id'?$sort_dir:'' }}" 					data-sort="id">ID #</th>
			<th>Photo</th>
			<th width="8%" class="sorting{{ $sort == 'username'?$sort_dir:'' }}" 				data-sort="username">User ID</th>
			<th width="10%" class="sorting{{ $sort == 'fullname'?$sort_dir:'' }}"     			data-sort="fullname">Full Name</th>
			<th width="10%" class="sorting{{ $sort == 'email'?$sort_dir:'' }}"     				data-sort="email">Email</th>
			<th width="10%" class="sorting{{ $sort == 'role'?$sort_dir:'' }}"     				data-sort="role">Type</th>
			<th width="10%" class="sorting{{ $sort == 'location'?$sort_dir:'' }}" 				data-sort="location">Location</th>
			<th width="10%" class="sorting{{ $sort == 'last_activity'?$sort_dir:'' }}" 			data-sort="last_activity">Last Active</th>
			<th width="10%" class="sorting{{ $sort == 'created_at'?$sort_dir:'' }}" 			data-sort="created_at">Created At</th>
			<th width="10%"  class="sorting{{ $sort == 'status'?$sort_dir:'' }}" 				data-sort="status">Status</th>
			<th>Action</th>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<th>
				<input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
			</th>
			<th>&nbsp;</th>
			<th>
				<input type="text" class="form-control form-filter input-sm" name="filter[username]" value="{{ old('filter.username') }}" placeholder="Username" />
			</th>
			<th>
				<input type="text" class="form-control form-filter input-sm" name="filter[fullname]" value="{{ old('filter.fullname') }}" placeholder="Full Name" />
			</th>
			<th>
				<input type="text" class="form-control form-filter input-sm" name="filter[email]" value="{{ old('filter.email') }}" />
			</th>
			<th>
				<select name="filter[role]" class="form-control form-filter input-sm select2">
					<option value="">Select...</option>
					@foreach (User::getOptions('role') as $name => $r)
						@if ($r != $role)
						<option value="{{ $r }}" {{ $r == old('filter.role')?'selected':'' }}>{{ $name }}</option>
						@endif
					@endforeach
				</select>
			</th>
			<th>
				<input type="text" class="form-control form-filter input-sm" name="filter[location]" value="{{ old('filter.location') }}" />
			</th>
			<th>
				<div class="input-group date datepicker" data-date-format="yyyy/mm/dd">
					<input type="text" class="form-control form-filter input-sm" readonly name="filter[last_activity][from]" placeholder="From" value="{{ old('filter.last_activity.from') }}" data-value="{{ old('filter.last_activity.from') }}" />
					<span class="input-group-btn">
						<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
				</div>
				<div class="input-group date datepicker" data-date-format="yyyy/mm/dd">
					<input type="text" class="form-control form-filter input-sm" readonly name="filter[last_activity][to]" placeholder="To" value="{{ old('filter.last_activity.to') }}" data-value="{{ old('filter.last_activity.to') }}" />
					<span class="input-group-btn">
						<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
				</div>
			</th>
			<th>
				<div class="input-group date datepicker" data-date-format="yyyy/mm/dd">
					<input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][from]" placeholder="From" value="{{ old('filter.created_at.from') }}" data-value="{{ old('filter.created_at.from') }}" />
					<span class="input-group-btn">
						<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
				</div>
				<div class="input-group date datepicker" data-date-format="yyyy/mm/dd">
					<input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][to]" placeholder="To" value="{{ old('filter.created_at.to') }}" data-value="{{ old('filter.created_at.to') }}" />
					<span class="input-group-btn">
						<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
				</div>
			</th>
			<th>
				<div class="mb-3">
					<select name="filter[status]" class="form-control form-filter input-sm select2">
						<option value="">Select...</option>
						@foreach (User::getOptions('status') as $name => $status)
						<option value="{{ $status }}" {{ "$status" == old('filter.status') || "$status_selected" == "$status" ?'selected':'' }}>{{ $name }}</option>
						@endforeach
					</select>
				</div>
				<select name="filter[idv_status]" class="form-control form-filter input-sm select2">
					<option value="">Select...</option>
					@foreach (['ID Verified' => User::STATUS_ID_VERFIED, 'ID Unverified' => User::STATUS_ID_UNVERFIED] as $name => $status)
					<option value="{{ $status }}" {{ $status == old('filter.idv_status')?'selected':'' }}>{{ $name }}</option>
					@endforeach
				</select>
			</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse ($users as $user)
		<tr>
			<td><input type="checkbox" class="group-checkable" name="ids[]" value="{{ $user->id }}" {{ User::enableStatusChanged($user) }} /></td>
			<td align="center">{{ $user->id }}</td>
			<td align="center" width="8%"><img src="{{ avatar_url($user) }}" class="img-circle" width="50" height="50" /></td>
			<td>{{ $user->username }}</td>
			<td>{{ $user->fullname }}</td>
			<td>{{ $user->email }}</td>
			<td align="center">{{ array_search($user->role, User::getOptions('role')) }}</td>
			<td align="center">{{ $user->country }}</td>
			<td align="center">{{ format_date('Y-m-d H:i', $user->last_activity) }}</td>
			<td align="center">{{ format_date('Y-m-d H:i', $user->created_at) }}</td>
			<td align="center">
				<span class="label label-{{ $user->colorByStatus() }} normal-case label-status" title="{{ $user->suspendedReason() }}">{{ $user->stringByStatus() }}</span>
				@if ( $user->isLoginBlocked() )
				<div class="mt-2">
					<span class="label label-warning normal-case">Login Blocked</span>
				</div>
				@endif
				@if ( $user->isIDVerified() )
				<div class="mt-2">
					<span class="label label-id-verified normal-case">ID Verified</span>
				</div>
				@endif
			</td>
			<td align="center"><a href="{{ route('admin.super.user.overview', ['user_id' => $user->id]) }}" class="blue">View</a></td>
		</tr>
		@empty
		<tr>
			<td colspan="12" align="center">No Users</td>
		</tr>
		@endforelse
	</tbody>
</table>