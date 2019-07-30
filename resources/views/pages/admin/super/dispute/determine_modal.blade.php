<?php
/**
 *
 * @author KCG
 * @since Jan 09, 2018
 * @version 2.0
*/
use iJobDesk\Models\Ticket;
?>

<div id="modal_determine" class="modal fade modal-scroll" tabindex="-1" aria-hidden="true" data-width="600">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">
			Determine
		</h4>
	</div>
	<form action="{{ route('admin.super.dispute.determine', ['id' => $ticket->id]) }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />

		<div class="modal-body">

			{{ show_messages() }}

			<div class="form-group row">
				<label class="col-md-3 col-md-offset-1 control-label text-left bold">Contract</label>
				<div class="col-md-7 control-label text-left">{{ $contract->title }}</div>
			</div>
			<div class="form-group row">
				<label class="col-md-3 col-md-offset-1 control-label text-left bold">Buyer</label>
				<div class="col-md-7 control-label text-left">{{ $contract->buyer->fullname() }}<br />({{ $buyer_dispute_counts }} disputes, {{ $buyer_dispute_win_counts }} won)</div>
			</div>
			<div class="form-group row">
				<label class="col-md-3 col-md-offset-1 control-label text-left bold">Freelancer</label>
				<div class="col-md-7 control-label text-left">{{ $contract->contractor->fullname() }}<br />({{ $freelancer_dispute_counts }} disputes, {{ $freelancer_dispute_win_counts }} won)</div>
			</div>
			<div class="form-group row">
				<label class="col-md-3 col-md-offset-1 control-label text-left bold">Action Type <span class="required">*</span></label>
				<div class="col-md-7 control-label text-left">
					<select id="archive_type" name="archive_type" class="select2" data-auto-submit="false" data-width="250" data-rule-required="true">
	                    <option value="">Select...</option>
	                    @foreach (Ticket::getOptions('dispute_result') as $type => $label)
	                    <option value="{{ $type }}">{{ $label }}</option>
	                    @endforeach
	                </select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-md-3 col-md-offset-1 control-label text-left bold">Comment <span class="required">*</span></label>
				<div class="col-md-7">
					<textarea name="reason" class="form-control maxlength-handler" rows="5" maxlength="1000" data-rule-required="true"></textarea>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
			<button type="submit" class="modal-button btn blue">Determine</button>
		</div>
	</form>
</div>