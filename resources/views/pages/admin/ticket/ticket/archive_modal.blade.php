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

<div id="modal_archive" class="modal fade modal-scroll" tabindex="-1" data-width="460" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">Archive</h4>
	</div>
	<form action="{{ route('admin.'.$role_id.(!empty($user)?'.user':'').'.ticket.solve', ['id' => $ticket->id, 'user_id' => !empty($user)?$user->id:null]) }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<div class="modal-body">
			<div class="form-group">
				<label class="col-md-4 control-label">To&nbsp;*</label>
				<div class="col-md-7">
					<select id="archived" name="archived" class="form-control form-filter input-sm select2" data-placeholder="Choose assginers" data-rule-required="true" data-width="100%">
						<option value="0">Solved Successfully</option>
						<option value="1">Solved Personally (Themselves)</option>
						<option value="2">Punishment to Buyer</option>
						<option value="3">Punishment to Freelancer</option>
						<option value="4">Other</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4">&nbsp;</label>
				<div class="col-md-7">
					<textarea name="reason" class="form-control maxlength-handler" rows="5" maxlength="1000"></textarea>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="col-md-4"></div>
			<div class="col-md-7">
				<button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
				<button type="submit" class="save-button btn blue">Submit</button>
			</div>
		</div>
	</form>
</div>