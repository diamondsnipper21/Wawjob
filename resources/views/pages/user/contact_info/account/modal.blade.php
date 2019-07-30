<?php

use iJobDesk\Models\File;

?>
<div class="modal fade modal-edit-account" id="modalEditAccount" style="overflow:hidden;" aria-hidden="false">
	<form name="edit_comment" class="form-horizontal " id="frm_edit_account" method="POST" action="{{ route('user.contact_info', ['section' => 'account']) }}" enctype="multipart/form-data">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		
		<input type="hidden" name="x1" id="x1" />
		<input type="hidden" name="y1" id="y1" />
		<input type="hidden" name="width" id="w" />
		<input type="hidden" name="height" id="h" />

		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">{{ trans('common.edit_account') }}</h4>
				</div>

				<div class="modal-body">
					<div class="row pb-4">
						<div class="col-md-12 info fs-13">
							{!! trans('user.note_invisible_contact_fields') !!}
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							{{-- User ID --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.user_id') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<input type="text" class="form-control" name="userId" data-rule-required="true" value="{{ ($user->username != null)? $user->username : "" }}" disabled />
								</div>
							</div>

							{{-- Type --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.type') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<select type="text" class="form-control select2" id="is_company" name="is_company" data-rule-required="true">
										<option value="">{{ trans('common.please_select') }}</option>
										<option value="0" {{ !$user->isCompany()?'selected':'' }}>{{ trans('common.individual') }}</option>
										<option value="1" {{ $user->isCompany()?'selected':'' }}>{{ trans('common.company') }}</option>
									</select>
								</div>
							</div>

							<div class="individual-fields">
								{{-- First Name --}}
								<div class="form-group row">
									<div class="col-xs-4 control-label">
										<div class="pre-summary">{{ trans('common.first_name') }} <span class="form-required"> *</span></div>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="firstName" name="firstName" data-rule-required="true" data-rule-validname="true" id="firstName" value="{{ ($user->contact->first_name != null)? $user->contact->first_name : '' }}" data-value="{{ ($user->contact->first_name != null)? $user->contact->first_name : '' }}" />
									</div>
								</div>

								{{-- Last Name --}}
								<div class="form-group row">
									<div class="col-xs-4 control-label">
										<div class="pre-summary">{{ trans('common.last_name') }} <span class="form-required"> *</span></div>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="lastName" name="lastName" data-rule-required="true" data-rule-validname="true" value="{{ ($user->contact->last_name != null)? $user->contact->last_name : "" }}" data-value="{{ ($user->contact->last_name != null)? $user->contact->last_name : '' }}" />
									</div>
								</div>
							</div>

							{{-- Email --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.email') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<input type="text" class="form-control" id="email" name="email" data-rule-required="true" data-rule-email="true" data-rule-remote="{{ route('user.signup.checkfield', ['field' => 'email', 'id' => $user->id]) }}" value="{{ ($user->email != null)? $user->email : "" }}" />
								</div>
							</div>

							{{-- Photo --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.portrait') }}</div>
								</div>
								<div class="col-xs-8">
									<label class="control-value-avatar"><img src="{{ avatar_url($user) }}" width="100" height="100" class="user-avatar img-circle" />
							 			@if ($current_user->existAvatar())
							 			<a href="{{ route('user.my_profile.remove_avatar') }}" title="{{ trans('common.delete') }}"><i class="hs-admin-trash"></i></a>
							 			@endif
							 		</label>
									<div class="file-upload-container">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<span class="btn btn-success green btn-file">
												<span class="fileinput-new "><i class="icon-cloud-upload"></i>&nbsp;&nbsp;{{ trans('common.select') }}</span> 
												<span class="fileinput-exists">{{ trans('common.change') }}</span>
												
												<input type="file" id="avatar" class="form-control" name="attached_files"  {!! render_file_validation_options(File::TYPE_USER_AVATAR) !!} />
												<input type="hidden" name="file_ids">
												<input type="hidden" name="file_type" value="{{ File::TYPE_USER_AVATAR }}" />
											</span>
											<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>&nbsp;&nbsp;&nbsp;
										</div>
										<button class="btn btn-danger btn-border btn-upload-cancel hide pull-right" type="button">{{ trans('common.cancel') }}</button>
									</div>
									<div id="temp-avatar"></div>
								</div>
							</div>
						</div><!-- .col-md-6 -->

						<div class="col-md-6">
							{{-- Address1 --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.address') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<input type="text" class="form-control" id="address" name="address" data-rule-required="true" value="{{ ($user->contact->address != null) ? $user->contact->address : '' }}" placeholder="{{ trans('common.address1') }}" />
								</div>
							</div>

							{{-- Address2 --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<!-- <div class="pre-summary">{{ trans('common.address2') }}</div> -->
								</div>
								<div class="col-xs-8">
									<input type="text" class="form-control" id="address2" name="address2" value="{{ ($user->contact->address2 != null) ? $user->contact->address2 : '' }}" placeholder="{{ trans('common.address2') }}" />
								</div>
							</div>

							{{-- City --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.city') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<input type="text" class="form-control" id="city" name="city" data-rule-required="true" value="{{ ($user->contact->city != null) ? $user->contact->city : '' }}" />
								</div>
							</div>

							{{-- State --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.state') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<input type="text" class="form-control" id="state" name="state" data-rule-required="true" value="{{ ($user->contact->state != null) ? $user->contact->state : '' }}" />
								</div>
							</div>

							{{-- Country --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.country') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<select type="text" class="form-control select2" id="countryCode" name="countryCode">
										<option value="">{{ trans('common.please_select') }}</option>
										@foreach ($countries as $country)
										   <option value="{{ $country->charcode }}" data-phone-prefix="{{ $country->country_code }}" {{ ($user->contact->country_code == $country->charcode) ? 'selected' : '' }}>{{ $country->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							{{-- Phone --}}
							<div class="form-group row phone-input">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.phone') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<div class="input-group">
										<span class="input-group-addon"></span>
										<input type="text" class="form-control" name="phone" data-rule-required="true" id="phone" value="{{ ($user->contact->phone != null) ? $user->contact->phone : '' }}" />
									</div>
								</div>
							</div>

							{{-- Timezone --}}
							<div class="form-group row">
								<div class="col-xs-4 control-label">
									<div class="pre-summary">{{ trans('common.timezone') }} <span class="form-required"> *</span></div>
								</div>
								<div class="col-xs-8">
									<select type="text" class="form-control select2" id="timezoneId" name="timezoneId" style="width: 100%">
										@foreach ($timezones as $timezone)
										   <option value="{{ $timezone->id }}" {{ ($user->contact->timezone_id == $timezone->id) ? 'selected' : '' }}>{{ $timezone->label }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div><!-- .col-md-6 -->
					</div><!-- .row -->
				</div><!-- .modal-body -->

				<div class="modal-footer">
					@if ((!$user->isBuyer() && $user->id_verified != 1) || ($user->isBuyer() && $user->myBalance() == 0))
					<p id="note_require_id_verification" class="pull-left hide">{!! trans('user.require_id_verification_username_changed') !!}</p>
					@endif
					<button type="submit" class="btn btn-primary btn-save">{{ trans('common.save') }}</button>
					<button type="button" class="btn btn-link btn-cancel" data-dismiss="modal">{{ trans('common.cancel') }}</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</form>
</div>
