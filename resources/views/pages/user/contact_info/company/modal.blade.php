<div class="modal fade modal-edit-company" id="modalEditCompany" aria-hidden="false">
	<form name="edit_company"  class="form-horizontal" id="frm_edit_company" method="POST" action="{{ route('user.contact_info', ['section' => 'company']) }}">
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
							<input type="text" class="form-control" name="tagline" value="{{ ($user->company->tagline != null ? $user->company->tagline : '') }}" />
							@else
							<input type="text" class="form-control" name="tagline"/>
							@endif
						</div>
						<div class="clear-div"></div>
					</div>

					{{-- Address --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.address') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="address1" placeholder="{{ __('common.address1') }}" value="{{ ($user->company->address1 != null ? $user->company->address1 : '') }}" data-rule-required="true" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label"></div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="address2" placeholder="{{ __('common.address2') }}" value="{{ ($user->company->address2 != null ? $user->company->address2 : '') }}" />
						</div>
						<div class="clear-div"></div>
					</div>

					{{-- Phone --}}
					<div class="form-group phone-input">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.phone') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<div class="input-group">
								<span class="input-group-addon"></span>
								<input type="text" class="form-control" name="phone" data-rule-required="true" value="{{ ($user->company->phone != null) ? $user->company->phone : '' }}" />
							</div>							
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
