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
		<h4 class="modal-title">{{ !empty($ticket) && $ticket->type == Ticket::TYPE_ID_VERIFICATION?'Determine':'Archive' }}</h4>
	</div>
	<form action="{{ route('admin.'.$role_id.(!empty($user)?'.user':'').'.ticket.solve', ['user_id' => !empty($user)?$user->id:null]) }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="return_page" value="" />
		@for($i = 0; $i < $per_page; $i++)
			<input type="hidden" name="ticket_id[]" value="" />
		@endfor
		
		<div class="modal-body">
			<div class="form-group">
				<label class="col-md-4 control-label">{{ !empty($ticket) && $ticket->type == Ticket::TYPE_ID_VERIFICATION?'Type':'Reason' }}&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<select id="archived" name="archived" class="form-control form-filter select2" data-placeholder="Choose assginers" data-rule-required="1" style="width: 100%">
					@foreach (Ticket::getOptions(!empty($ticket) && $ticket->type == Ticket::TYPE_ID_VERIFICATION?'id_verification_result':'common_result') as $value => $label)
						<option value="{{ $value }}">{{ $label }}</option>
					@endforeach
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4">&nbsp;</label>
				<div class="col-md-7">
					<textarea name="reason" class="form-control maxlength-handler" rows="5" maxlength="1000" placeholder="Comment here"></textarea>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-7">
					<button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
					<button type="submit" class="save-button btn blue">Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>