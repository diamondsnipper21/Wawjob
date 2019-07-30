<?php
/**
 *
 * @author KCG
 * @since July 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\EmailTemplate;
?>
<script type="text/x-tmpl" id="tmpl_email_template">
	<div id="modal_email_template" class="modal fade modal-scroll" data-backdrop="static" tabindex="-1" data-width="90%" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			<h4 class="modal-title bold">
				{% if (!template.id) { %}
					Add New Email template
				{% } else { %}
					Edit #{%= template.id %} Email Template
				{% } %}
			</h4>
		</div>
		<form action="{{ route('admin.super.settings.email_template.edit') }}{%= template.id?('/' + template.id):'' %}" method="post" class="form-horizontal">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<input type="hidden" name="_temp" class="temp" value="{%= (!template.id?'ADD':'EDIT') %}" />

			<div class="modal-body">

				{{ show_messages() }}
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row margin-top-10">
							<label class="col-sm-3 control-label bold">Slug&nbsp;<span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control slug" name="slug" value="{%= template.slug %}" data-rule-required="true" data-auto-submit="false" />						
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group row margin-top-10">
							<label class="col-sm-3 control-label bold">For&nbsp;<span class="required">*</span></label>
							<div class="col-sm-9">
								<select name="select_for" id="select_for" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-for" data-auto-submit="false">
									<option value="">Select...</option>
									@foreach (EmailTemplate::getOptions('for') as $key => $label)
									<option value="{{ $key }}" {%= (template.for == {{ $key }}?'selected':'') %}>{{ $label }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="tabbable-custom nav-justified">
					<ul class="nav nav-tabs nav-justified">
					@foreach ( config('menu.lang_menu') as $lang => $menu )
						<li class="{{ $lang == 'en'?'active':'' }}">
							<a href="#tab_{{ $lang }}" data-toggle="tab" aria-expanded="true"><img src="/assets/images/common/lang_flags/{{ $lang }}.png">&nbsp;{{ $menu['label'] }}</a>
						</li>
					@endforeach
					</ul>
					<div class="tab-content">
					@foreach ( array_keys(config('menu.lang_menu')) as $lang )
						<div class="tab-pane {{ $lang == 'en'?'active':'' }}" id="tab_{{ $lang }}">
							<div class="form-group row margin-top-10">
								<label class="col-sm-2 control-label bold">Subject&nbsp;<span class="required">*</span></label>
								<div class="col-sm-10">
									<input type="text" class="form-control subject-{{ $lang }}" name="subject[{{ $lang }}]" id="subject_{{ $lang }}" value="{%= Global.parseJsonMultiLang(template.subject, '{{ $lang }}') %}" data-rule-required="true" data-auto-submit="false" />
								</div>
							</div>
							<div class="form-group row margin-top-10">
								<label class="col-sm-2 control-label bold">Content&nbsp;<span class="required">*</span></label>
								<div class="col-sm-10">
									<textarea name="content[{{ $lang }}]" id="content_{{ $lang }}" class="form-control ckeditor content-{{ $lang }}" rows="8" data-rule-required="true" data-auto-submit="false">{%= Global.parseJsonMultiLang(template.content, '{{ $lang }}') %}</textarea>
								</div>
							</div>
						</div>
					@endforeach
					</div>
				</div><!-- .tabbable-custom -->
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
				<button type="submit" class="modal-button btn blue">Save</button>
			</div>
		</form>
	</div>
</script>