<div class="modal fade modal-edit-contact" id="modalEditContact" aria-hidden="false">
	<form name="edit_comment"  class="form-horizontal" id="frm_edit_contact" method="POST" action="{{ route('user.contact_info', ['section' => 'location']) }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">{{ trans('page.user.contact.title') }}</h4>
				</div>

				<div class="modal-body">
					{{-- Address --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.address') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="address" data-rule-required="true" value="{{ ($user->company_contact->address != null) ? $user->company_contact->address : '' }}" />							
						</div>
						<div class="clear-div"></div>
					</div>

					{{-- City --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.city') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="city" data-rule-required="true" value="{{ ($user->company_contact->city != null) ? $user->company_contact->city : '' }}" />							
						</div>
						<div class="clear-div"></div>
					</div>

					{{-- State --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.state') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="state" data-rule-required="true" value="{{ ($user->company_contact->state != null) ? $user->company_contact->state : '' }}" />
						</div>
						<div class="clear-div"></div>
					</div>
					
					{{-- Country --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.country') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<select type="text" class="form-control select2" name="country_code">
								@foreach ($countries as $country)
								   <option value="{{ $country->charcode }}" data-phone-prefix="{{ $country->country_code }}" {{ ($user->company_contact->country_code == $country->charcode) ? 'selected' : '' }}>{{$country->name}}</option>
								@endforeach
							</select>						
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
								<input type="text" class="form-control" name="phone" data-rule-required="true" value="{{ ($user->company_contact->phone != null) ? $user->company_contact->phone : '' }}" />
							</div>							
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Timezone --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('common.timezone') }} <span class="form-required"> *</span></div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<select type="text" class="form-control select2" name="timezone_id">
								@foreach ($timezones as $timezone)
								   <option value="{{ $timezone->id }}" {{ ($user->company_contact->timezone_id == $timezone->id) ? 'selected' : '' }}>{{$timezone->label}}</option>
								@endforeach
							</select>
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
