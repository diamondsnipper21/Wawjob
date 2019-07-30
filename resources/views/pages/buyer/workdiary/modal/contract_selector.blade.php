<?php
use iJobDesk\Models\Contract;
?>
<div class="form-group">
	<div class="input-field">
		<select class="job-category select2 form-control" id="contract_selector" name="contract_selector" data-required="1" aria-required="true" data-url="{{ $current_user->isAdmin() ? route('admin.super.user.workdiary.view_first', ['user_id' => $user->id]):route('workdiary.view_first') }}">
			@foreach ($contracts as $project_subject => $p_contracts)
				<optgroup label="{{ $project_subject }}">
					@foreach($p_contracts as $c)
					<option value="{{ $c->id }}" data-url="{{ _route('workdiary.view', ['cid' => $c->id]) }}" {{ $contract && $c->id == $contract->id ? ' selected' : '' }}>
						{{ $c->contractor->fullname() }} - {{ $c->title }}
						@if ( $c->isPaused() )
							&nbsp;[{{ trans('common.paused') }}]
						@elseif ( $c->isSuspended() )
							&nbsp;[{{ trans('common.suspended') }}]
						@endif
					</option>
					@endforeach
				</optgroup>
			@endforeach
		</select>
	</div>
</div>