<?php
/**
 *
 * @author KCG
 * @since July 28, 2017
 * @version 1.0
*/
?>

<div id="modal_withdraw" class="modal fade modal-scroll" tabindex="-1" data-width="760" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">Withdraw</h4>
	</div>
	<form action="{{ Request::url() }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="_action" value="WITHDRAW" />

		<div class="modal-body">
			<div class="form-group">
				<label class="col-md-3 control-label">Available Balance</label>
				<div class="col-md-7 control-label">
					<div class="text-left current-balance" data-balance="{{ $total }}">${{ formatCurrency($total) }}</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">New Balance</label>
				<div class="col-md-7 control-label">
					<div class="text-left new-balance">$0.00</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Amount <span class="required" aria-required="true">*</span></label>
				<div class="col-md-7">
					<div class="input-group have-group-addon">
						<span class="input-group-addon">$</span>
						<input type="text" class="form-control" name="amount" id="amount" 
						data-rule-number="true" data-rule-required="true" data-rule-min="1" data-rule-max="{{ $total }}" data-auto-submit="false" />
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Comment <span class="required" aria-required="true">*</span></label>
				<div class="col-md-7">
					<textarea name="note" class="form-control maxlength-handler" rows="5" maxlength="1000" data-rule-required="true" data-auto-submit="false"></textarea>
				</div>
			</div>			
		</div>
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
			<button type="submit" class="btn btn-submit-withdraw blue">Withdraw</button>
		</div>
	</form>
</div>