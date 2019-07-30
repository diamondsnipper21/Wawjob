<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\Todo;
use iJobDesk\Models\File;

?>

@if ($current_user->isSuper())

<div id="modal_create_thread" class="modal fade modal-scroll" tabindex="-1" data-width="70%" aria-hidden="true" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">New Message</h4>
	</div>
	<form action="{{ route('admin.' . $role_id . '.thread.create') }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		
		<div class="modal-body">
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">To&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<select id="to" name="to[]" class="form-select2-control select2-ajax" multiple data-placeholder="Choose users" data-url="{{ route('admin.super.user.ajax.search_users') }}" data-maximum-selection-length="10" data-sortable="1" data-rule-required="true" data-width="100%">
			        </select>
					<i class="icon icon-question pull-right icon-question-to" data-toggle="tooltip" title="" data-original-title="Emails, User ID#, Username and Fullname can be used." data-placement="left"></i>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Subject&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<input type="text" class="form-control" name="subject" data-rule-required="true" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Message&nbsp;<span class="required">*</span></label>
				<div class="col-md-7">
					<textarea name="message" class="form-control maxlength-handler" rows="5" maxlength="1000" data-rule-required="true"></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3">&nbsp;</label>
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-6"></div>
						<div class="col-md-6 text-right padding-top-40">
							<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
							<button type="submit" class="save-button btn blue">Save</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

@endif