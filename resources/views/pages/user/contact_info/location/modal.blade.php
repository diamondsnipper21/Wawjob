
<div class="modal fade modal-edit-location" id="modalEditLocation" aria-hidden="false">
	<form name="edit_comment"  class="form-horizontal" id="frm_edit_location" method="POST" action="/user/contact-info/location">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Edit Location</h4>
				</div>

				<div class="modal-body">
					{{-- Address --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('user.location.address') }} <span class="form-required">*</span>:</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="address" data-rule-required="true" value="{{ ($user->contact->address != null) ? $user->contact->address : '' }}" />							
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- city --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('user.location.city') }} <span class="form-required">*</span>:</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="city" data-rule-required="true" value="{{ ($user->contact->city != null) ? $user->contact->city : '' }}" />							
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- state --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('user.location.state') }} <span class="form-required">*</span>:</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="state" data-rule-required="true" value="{{ ($user->contact->state != null) ? $user->contact->state : '' }}" />
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Country --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('user.location.country') }} <span class="form-required">*</span>:</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<select type="text" class="form-control select2" name="countryCode">
								@foreach ($countries as $country)
								   <option value="{{ $country->charcode }}" {{ ($user->contact->country_code == $country->charcode) ? 'selected' : '' }}>{{$country->name}}</option>
								@endforeach
							</select>						
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Phone --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('user.location.phone') }} <span class="form-required">*</span>:</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<input type="text" class="form-control" name="phone" data-rule-required="true" value="{{ ($user->contact->phone != null) ? $user->contact->phone : '' }}" />							
						</div>
						<div class="clear-div"></div>
					</div>
					{{-- Timezone --}}
					<div class="form-group">
						<div class="col-sm-3 col-xs-4 control-label">
							<div class="pre-summary">{{ trans('user.location.timezone') }} <span class="form-required">*</span>:</div>
						</div>
						<div class="col-sm-9 col-xs-8">
							<select type="text" class="form-control select2" name="timezoneId">
								@foreach ($timezones as $timezone)
								   <option value="{{ $timezone->id }}" {{ ($user->contact->timezone_id == $timezone->id) ? 'selected' : '' }}>{{$timezone->label}}</option>
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
