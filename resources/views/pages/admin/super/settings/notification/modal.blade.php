<?php
/**
 *
 * @author KCG
 * @since July 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Notification;
?>
<script type="text/x-tmpl" id="tmpl_notification">
	<div id="modal_notification" class="modal fade modal-scroll" data-backdrop="static" tabindex="-1" data-width="720" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			<h4 class="modal-title bold">
				{% if (!template.id) { %}
					Add New Notification
				{% } else { %}
					Edit #{%= template.id %} Notification
				{% } %}
			</h4>
		</div>
		<form action="{{ route('admin.super.settings.notifications.edit') }}{%= template.id?('/' + template.id):'' %}" method="post" class="form-horizontal">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<input type="hidden" name="_temp" class="temp" value="{%= (!template.id?'ADD':'EDIT') %}" />

			<div class="modal-body">

				{{ show_messages() }}
				
				<div class="form-group row margin-top-10">
					<label class="col-md-2 col-md-offset-1 control-label text-left bold">Slug&nbsp;<span class="required">*</span></label>
					<div class="col-md-8">
						<input type="text" class="form-control slug" name="slug" value="{%= template.slug %}" data-rule-required="true" data-auto-submit="false" />						
					</div>
				</div>

				@foreach ( array_keys(config('menu.lang_menu')) as $lang )
					<div class="form-group row margin-top-10">
						<div class="col-md-2 col-md-offset-1 control-label bold"><img src="/assets/images/common/lang_flags/{{ $lang }}.png">&nbsp;&nbsp;Content&nbsp;<span class="required">*</span></div>
						<div class="col-md-8">
							<textarea name="content[{{ $lang }}]" id="content_{{ $lang }}" class="form-control ckeditor content-{{ $lang }}" rows="3" data-rule-required="true" data-auto-submit="false">{%= Global.parseJsonMultiLang(template.content, '{{ $lang }}') %}</textarea>
						</div>
					</div>
				@endforeach
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
				<button type="submit" class="modal-button btn blue">Save</button>
			</div>
		</form>
	</div>
</script>