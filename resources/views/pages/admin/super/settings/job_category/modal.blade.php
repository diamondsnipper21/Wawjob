<?php
/**
 *
 * @author KCG
 * @since August 1, 2017
 * @version 1.0
*/

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title">{{ $action == 'edit'?'Edit Category':'Add New Category' }}</h4>
</div>
<form action="{{ route('admin.super.settings.job_category.edit', ['id' => $action == 'edit'?$id:null]) }}" method="post" class="form-horizontal">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	<input type="hidden" name="action" value="save" />

	<div class="modal-body">
		
		{{ show_messages() }}

		@foreach (['EN', 'KP', 'CH'] as $lang)
		<div class="form-group">
			<label class="col-md-3 control-label"><span class="label-{{ $lang }}">Name&nbsp;*</span></label>
			<div class="col-md-7">
				<div class="input-group margin-bottom-10">
					<span class="input-group-addon input-circle-left"><img src="/assets/images/common/lang_flags/{{ strtolower($lang) }}.png" /></span>
					<input id="name_{{ $lang }}" type="text" class="form-control" name="name[{{ $lang }}]" {!! $lang == 'EN'?'data-rule-required="true"':'' !!} data-auto-submit="false" value="{{ parse_multilang($job_category->name, $lang) }}" />
				</div>
			</div>
		</div>
		@endforeach

		@if ($action == 'add')
		<div class="form-group">
			<label class="col-md-3 control-label">Parent Category</label>
			<div class="col-md-7 parent-category-name">
				{{ $parent_category->name?parse_multilang($parent_category->name):'ROOT' }}
			</div>
			<input type="hidden" name="parent_id" value="{{ $parent_category->id }}" />
		</div>
		@endif
		
		<div class="form-group">
			<label class="col-md-3 control-label">Description&nbsp;*</label>
			<div class="col-md-7">
				<textarea name="desc" class="form-control maxlength-handler" rows="5" maxlength="1000" data-rule-required="true" data-auto-submit="false">{{ parse_multilang($job_category->desc) }}</textarea>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
		<button type="submit" class="save-button btn blue">Save</button>
	</div>
</form>