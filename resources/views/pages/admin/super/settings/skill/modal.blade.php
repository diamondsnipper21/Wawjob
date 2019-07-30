<?php
/**
 *
 * @author KCG
 * @since April 04, 2017
 * @version 1.0
*/
?>
<div id="modal_skill_page" class="modal fade modal-scroll" tabindex="-1" data-width="720" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{{ empty($id)?'Add New':'Edit' }} Skill</h4>
	</div>
	<form action="{{ Request::url() }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="_action" value="SAVE" />

		<div class="modal-body">

			{{ show_messages() }}

			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Name&nbsp;<span class="required">*</span></label>
				<div class="col-md-8">
					<input type="text" class="form-control" name="name" data-rule-required="true" data-rule-remote="{{ route('admin.super.settings.skill.validate.name', ['id' => $id]) }}" data-auto-submit="false" value="{{ $skill->name }}" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 col-md-offset-1 control-label">Description&nbsp;<span class="required">*</span></label>
				<div class="col-md-8">
					<textarea name="desc" class="form-control maxlength-handler" rows="5" maxlength="1000" data-rule-required="true" data-auto-submit="false">{!! $skill->desc !!}</textarea>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
			<button type="submit" class="save-button btn blue">Save</button>
		</div>
	</form>
</div>