<!-- Withdraw proposal -->
<div class="modal fade" id="modalWithdraw" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="withdrawModalLabel">{{ trans('common.withdraw_proposal') }}</h4>
			</div>
			<form id="formWithdraw" class="form-horizontal" method="post" action="{{ _route('job.application_detail', ['id' => $application->id]) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="type" value="W">

				<div class="modal-body">
					<div class="row block-section">
						<div class="col-md-3"><label class="control-label">{{ trans('common.reason') }}</label></div>
						<div class="col-md-6">
							<select name="reason" class="form-control select2">
								<option value="1">{{ trans('job.applied_by_mistake') }}</option>
								<option value="2">{{ trans('common.no_responsive') }}</option>
								<option value="3">{{ trans('job.schedule_conflict') }}</option>
								<option value="4">{{ trans('job.no_desirable_skills') }}</option>
								<option value="5">{{ trans('common.other') }}</option>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ trans('common.withdraw') }}</button>
					<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>