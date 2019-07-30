<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\Ticket;
use iJobDesk\Models\User;
?>

<div id="modal_assign" class="modal fade modal-scroll" tabindex="-1" data-width="460" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">Confirm</h4>
	</div>
	<div class="modal-body">
		<div class="alert alert-danger display-hide hide">
			<button class="close" data-close="alert"></button>
			You have some form errors. Please check below.
		</div>
		<div class="alert alert-success display-hide hide">
			<button class="close" data-close="alert"></button>
			Tickets has been assigned successfully.
		</div>
		<div class="alert alert-danger display-hide alert-confirm hide">
			<button class="close" data-close="alert"></button>
			The selected tickets are already assigned.
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-md-3 control-label">To&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<select id="assigners" name="assigners" class="form-control form-filter input-sm select2" data-placeholder="Choose assginers" data-select2-show-users="1">
						@foreach ($admins as $admin)
							<option data-role-css="{{ $admin['user']->role_css_class() }}" data-role-name="{{ $admin['user']->role_name() }}" data-role-short-name="{{ $admin['user']->role_short_name() }}" value="{{ $admin['id'] }}">{{ $admin['name'] }}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
		<button type="submit" id="assign-btn" class="save-button btn blue">Submit</button>
	</div>
</div>