<?php

use iJobDesk\Models\Ticket;

?>
@if ($ticket)
<div class="modal fade" id="modal_cancel_dispute" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		    <form method="post" role="form" action="{{ route('contract.dispute.cancel', ['id' => $contract->id, 'tid' => $ticket->id]) }}">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">{{ trans('contract.cancel_dispute') }}</h4>
				</div>
				<div class="modal-body">
					<div class="content-section">
		            	<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group row">
							<label class="col-md-3 control-label">
								{{ trans('common.reason') }} <span class="form-required">*</span>
							</label>
							<div class="col-md-9">
								<select id="archive_type" name="archive_type" class="select2" data-width="250" data-rule-required="true">
				                    <option value="">Select...</option>
				                    @foreach (Ticket::getOptions('dispute_cancel_result') as $type => $label)
				                    <option value="{{ $type }}">{{ $label }}</option>
				                    @endforeach
				                </select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 control-label">
								{{ trans('common.comment') }}
							</label>
							<div class="col-md-9">
								<textarea class="form-control maxlength-handler" name="reason" rows="5" placeholder="{{ trans('common.type_your_comment_here') }}" maxlength="2000"></textarea> 
							</div>
						</div>
					</div><!-- .content-section -->
				</div><!-- .modal-body -->
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-cancel-dispute">{{ trans('common.yes') }}</button>
					<button data-dismiss="modal" class="btn btn-link">{{ trans('common.no') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif