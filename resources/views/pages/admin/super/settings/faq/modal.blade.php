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
	<h4 class="modal-title">{{ $action == 'edit'?'Edit Faq':'Add New Faq' }}</h4>
</div>
<form action="{{ route('admin.super.settings.faq.edit', ['id' => $action == 'edit'?$faq_id:null]) }}" method="post" class="form-horizontal">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	<input type="hidden" name="action" value="save" />

	<div class="modal-body">
		
		{{ show_messages() }}

		<div class="tabbable-custom nav-justified">
			<ul class="nav nav-tabs nav-justified">
				@foreach (['EN', 'KP', 'CH'] as $lang)
					<li class="{{ $lang == 'EN' ? 'active' : '' }}">
						<a href="#tab_{{$lang}}" data-toggle="tab">
							<img src="/assets/images/common/lang_flags/{{ $lang }}.png" />
							@if($lang == 'EN')
								English
							@elseif($lang == 'KP')
								Korean
							@else
								Chinese
							@endif
						</a>
					</li>
				@endforeach
			</ul>
			<div class="tab-content">
				@foreach (['EN', 'KP', 'CH'] as $lang)
					<div class="tab-pane {{ $lang == 'EN' ? 'active' : '' }}" id="tab_{{ $lang }}">
						<div class="form-group">
							<label class="col-md-3 control-label"><span class="label-{{ $lang }}">Name&nbsp;*</span></label>
							<div class="col-md-7">
								<div class="input-group margin-bottom-10">
									<span class="input-group-addon input-circle-left"><img src="/assets/images/common/lang_flags/{{ strtolower($lang) }}.png" /></span>
									<input id="name_{{ $lang }}" type="text" class="form-control" name="name[{{ $lang }}]" {!! $lang == 'EN'?'data-rule-required="true"':'' !!} data-auto-submit="false" value="{{ parse_multilang($faq->title, $lang) }}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">Description&nbsp;*</label>
							<div class="col-md-7">
								<textarea id="desc_{{ $lang }}" name="desc[{{ $lang }}]" class="form-control maxlength-handler" rows="5" maxlength="1000" {!! $lang == 'EN'?'data-rule-required="true"':'' !!} data-auto-submit="false">{{ parse_multilang($faq->content, $lang) }}</textarea>
							</div>
						</div>
					</div><!-- .tab-pane -->
				@endforeach

				<div class="form-group">
					<label class="col-md-3 control-label">Type&nbsp;*</label>
					<div class="col-md-7">
						<select name="type" class="form-control form-filter input-sm select2" data-auto-submit="false" data-rule-required="true">
	                        <option value="">Select...</option>
	                        <option value="0" {{ "0" == $faq->type ? 'selected' : '' }}>Buyer</option>
	                        <option value="2" {{ "2" == $faq->type ? 'selected' : '' }}>Freelancer</option>
	                        <option value="1" {{ "1" == $faq->type ? 'selected' : '' }}>All</option>
	                    </select>
	                </div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">Visible&nbsp;*</label>
					<div class="col-md-7">
						<select name="visible" class="form-control form-filter input-sm select2" data-auto-submit="false" data-rule-required="true">
	                        <option value="">Select...</option>
	                        <option value="0" {{ "0" == $faq->visible ? 'selected' : '' }}>Hidden</option>
	                        <option value="1" {{ "1" == $faq->visible ? 'selected' : '' }}>Show</option>
	                    </select>
	                </div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">Category&nbsp;*</label>
					<div class="col-md-7">
						<select name="cat_id" class="form-control form-filter input-sm select2" data-auto-submit="false" data-rule-required="true">
	                        <option value="">Select...</option>
	                        @foreach($categories as $category)
	                            <option value="{{ $category->id }}" {{ $category->id == $faq->cat_id ? 'selected' : '' }}>{{ parse_multilang($category->name, "EN") }}</option>
	                        @endforeach
	                    </select>
	                </div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">Order&nbsp;*</label>
					<div class="col-md-7">
						<div class="input-group margin-bottom-10">
							<input id="order" type="text" class="form-control" name="order" data-auto-submit="false" value="{{ $faq->order }}" />
							(e.g, 0, 1, 2, ...)
						</div>
					</div>
				</div>
			</div><!-- .tab-content -->
		</div><!-- .tabbable-custom -->

	</div>
	<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
		<button type="submit" class="save-button btn blue">Save</button>
	</div>
</form>