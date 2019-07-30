<div class="modal fade modal-edit-detail" id="modalEditDetail" aria-hidden="false">
	<form name="edit_comment"  class="form-horizontal" id="frm_edit_detail" method="POST" action="{{ route('user.contact_info', ['section' => 'detail']) }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">{{ trans('page.user.detail.title') }}</h4>
				</div>

				<div class="modal-body">
					{{-- Company Name --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.company_name') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							@if ($user->company != null)
							<input type="text" class="form-control" name="name" data-rule-required="true" value="{{ ($user->company->name != null ? $user->company->name : '') }}"/>
							@else
							<input type="text" class="form-control" name="name" data-rule-required="true" value=""/>
							@endif
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Website --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.website') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							@if ($user->company != null)
							<input type="text" class="form-control" name="website" data-rule-required="true" value="{{ ($user->company->website != null ? $user->company->website : '') }}" data-rule-url="true" />
							@else
							<input type="text" class="form-control" name="website" data-rule-required="true"  data-rule-url="true" />
							@endif	
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Tagline --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.tagline') }}</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							@if ($user->company != null)
							<input type="text" class="form-control" name="tagline" value="{{ ($user->company->tagline != null ? $user->company->tagline : '') }}"/>
							@else
							<input type="text" class="form-control" name="tagline"/>
							@endif
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Description --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.description') }}</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<textarea id="detail_description" class="form-control maxlength-handler" name="description" maxlength="1000" rows="7">{{ $user->company != null ? $user->company->description : '' }}</textarea>
						</div>
						<div class="clear-div"></div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-save">{{ trans('common.save') }}</button>
					<button type="button" class="btn btn-link btn-cancel" data-dismiss="modal">{{ trans('common.cancel') }}</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</form>
</div>
