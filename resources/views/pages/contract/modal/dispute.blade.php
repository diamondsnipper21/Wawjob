<?php

use iJobDesk\Models\File;

?>
<div class="modal fade" id="modalDispute" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">{{ trans('common.file_dispute') }}</h4>
			</div>

			@if ( $current_user->isBuyer() && $contract->isHourly() )
			<div class="modal-body pb-0">
				<div class="pt-2">
					<div class="form-group row">
						<div class="col-md-offset-1 col-md-10">
							<label class="control-label">{{ trans('common.choose_reason') }} <span class="form-required">*</span></label>
							<div class="w-80">
								<select id="reason" name="reason" class="select2" data-width="100%" data-rule-required="true">
									<option value="">{{ trans('common.please_select') }}</option>
									@if ( $contract->isHourly() )
									<option value="1"{{ !$is_in_review ? ' disabled' : '' }}>{{ trans('report.billing_for_last_week') }} ({{ $last_week_from . ' - ' . $last_week_to }})</option>
									@endif
									<option value="2">{{ $current_user->isFreelancer() ? trans('contract.trouble_with_buyer') : trans('contract.trouble_with_freelancer') }}</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>

			<form id="formDisputeLastWeek" method="post" role="form" enctype="multipart/form-data" action="{{ route('contract.dispute.refund', ['id' => $contract->id]) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<div class="modal-body">
					
	 				<input type="hidden" name="_action" value="dispute_last_week">

					<div class="dispute-last-week hidden">
						<div class="form-group row">
							<div class="col-md-offset-1 col-md-10">
								{{ trans('contract.dispute_last_week_description') }}
							</div>
						</div>

						<div class="form-group row pt-2">
							<div class="col-md-offset-1 col-md-10">
								<div class="chk">
									<label class="pl-0">
										<input type="checkbox" name="confirm_refund" id="confirm_refund" value="1" data-rule-required="true" value="1">
										{{ trans('contract.want_to_get_refunded') }}
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-dispute-refund" disabled>{{ trans('common.submit') }}</button>
					<button data-dismiss="modal" class="btn btn-link">{{ trans('common.cancel') }}</button>
				</div>
			</form>
			@endif

			<form id="formDisputeTicket" method="post" role="form" enctype="multipart/form-data" action="{{ route('contract.dispute.create', ['id' => $contract->id]) }}" class="{{ $current_user->isBuyer() && $contract->isHourly() ? 'hidden' : '' }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<div class="modal-body">
					<div class="form-group row">
						<div class="col-md-offset-1 col-md-10">
							<label class="control-label">
								{{ trans('common.comment') }} <span class="form-required">*</span>
							</label>
							<div>
								<textarea class="form-control maxlength-handler" name="message" rows="5" data-rule-required="true" placeholder="{{ trans('common.type_your_comment_here') }}" maxlength="2000"></textarea> 
							</div>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-offset-1 col-md-10">
							<div class="w-80">
								{!! render_file_element(File::TYPE_TICKET_COMMENT) !!}
							</div>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-offset-1 col-md-10">
							{!! trans('contract.file_a_dispute_description', ['client' => $current_user->isFreelancer() ? 'client' : 'freelancer']) !!}

							@if ( $current_user->isFreelancer() )
								<br />{{ trans('contract.dispute_financial_suspended_description') }}
							@endif
						</div>
					</div>

					<div class="form-group row pt-2">
						<div class="col-md-offset-1 col-md-10">
							<div class="chk">
								<label class="pl-0">
									<input type="checkbox" name="confirm_file_dispute" id="confirm_file_dispute" value="1" data-rule-required="true" value="1">
									{{ trans('contract.confirm_to_file_dispute') }}
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-send-ticket" disabled>{{ trans('common.submit') }}</button>
					<button data-dismiss="modal" class="btn btn-link">{{ trans('common.cancel') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>