<?php

use iJobDesk\Models\File;
use iJobDesk\Models\Ticket;

?>
<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-labelledby="ticketCreateModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="createForm" class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('admin.super.ticket.create', ['user_id' => $user->id]) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="callType" value="" />

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ticketCreateModalLabel">{{ trans('ticket.modal.Create_A_Ticket') }} </h4>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label for="type" class="col-sm-2 control-label"><b>{{ trans('ticket.modal.type') }} <span class="form-required">*</span></b></label>
						<div class="col-sm-10">
							<select class="form-control select2" name="type" id="type" data-rule-required="true" data-width="50%">
								<option value=""> - {{ trans('ticket.modal.select') }} - </option>
								@foreach ($optionTypeArry as $key => $optionType)
									@if ($optionType != Ticket::TYPE_ID_VERIFICATION)
									<option value="{{ $optionType }}">{{ trans('common.' . $key) }}</option>
									@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="subject" class="col-sm-2 control-label"><b>{{ trans('ticket.modal.Subject') }} <span class="form-required">*</span></b></label>
						<div class="col-sm-10">
							<input type="text" name="subject" id="subject" class="form-control maxlength-handler" data-rule-required="true" maxlength="200">
						</div>	
					</div>
					<div class="form-group">
						<label for="content" class="col-sm-2 control-label"><b>{{ trans('ticket.modal.content') }} <span class="form-required">*</span></b></label>
						<div class="col-sm-10">
							<textarea class="form-control maxlength-handler" maxlength="5000" rows="10" name="content" id="content" data-rule-required="true"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label file-label"><b>{{ trans('ticket.modal.Attachment') }}</b></label>
						<div class="col-sm-10">
							<div>{!! render_file_element(File::TYPE_TICKET) !!}</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ trans('common.submit') }}</button>
					<a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
				</div>
			</form>
		</div>
	</div>
</div>