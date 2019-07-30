<?php
/**
 *
 * @author KCG
 * @since June 30, 2017
 * @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Timezone;
use iJobDesk\Models\File;

?>
<div id="modal_admin_user" class="modal fade modal-scroll" data-backdrop="static" tabindex="-1" data-width="90%" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{{ empty($admin_user->id)?'Add New Admin':'Edit Admin' }}</h4>
	</div>
	<form method="post" class="form-horizontal" enctype="multipart/form-data" action="{{ route('admin.super.admin_users.edit', ['id' => $admin_user->id]) }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="_action" value="SAVE" />

	    <div class="modal-body">

	    	{{ show_messages() }}

			<div class="row">
				<div class="col-md-6 col-sm-6">
					<div class="form-group">
						<label class="col-md-5 control-label">UserID&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="username" 

											data-rule-required="true" 
											data-rule-minlength="5" 
											data-rule-password_alphabetic="true" 
											data-rule-remote="{{ route('admin.super.admin_users.check_duplicated', ['id' => $admin_user->id, 'field' => 'username']) }}"

											value="{{ $admin_user->username }}" autocomplete="off" {{ $admin_user->id?'disabled':'' }} />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Password&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<input type="password" class="form-control" id="ele_password" name="password" 
											data-rule-minlength="8" 
											data-rule-password_alphabetic="true" 
											data-rule-password_number="true" 
											{!! $admin_user->id?'':'data-rule-required="true"' !!}
							placeholder="{{ trans('auth.password')}}"  autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Confirm Password&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<input type="password" class="form-control" id="ele_password2" 
											data-rule-equalto="#ele_password" 
											placeholder="{{ trans('auth.confirm_password')}}"  autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">First Name&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="first_name" 
											data-rule-required="true" value="{{ $admin_user->contact?$admin_user->contact->first_name:'' }}" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Last Name&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="last_name" 
											data-rule-required="true" value="{{ $admin_user->contact && $admin_user->contact->last_name?$admin_user->contact->last_name:'' }}" />
						</div>
					</div>
				</div>

				<div class="col-md-6 col-sm-6">
					<div class="form-group">
						<label class="col-md-3 control-label">Type&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-5">
							<select name="role" class="form-control form-filter input-sm select2" data-width="100%" data-rule-required="true">
								<option value="">Select...</option>
								@foreach (User::adminType() as $key => $value)
								<option value="{{ $key }}" {{ $key == $admin_user->role ? 'selected' : '' }} >{{ $value }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Email&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="email" 
											data-rule-required="true" 
											data-rule-email="true" 
											data-rule-remote="{{ route('admin.super.admin_users.check_duplicated', ['id' => $admin_user->id, 'field' => 'email']) }}"
											value="{{ $admin_user->email }}" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Timezone&nbsp;<span class="form-required">*</span></label>
						<div class="col-md-7">
							<select name="timezone" class="form-control form-filter input-sm select2" data-width="100%" data-rule-required="true" data-select2-show-search="true">
								<option value="">Select...</option>
								@foreach (Timezone::orderBy('gmt_offset', 'ASC')->get() as $timezone)
								<option value="{{ $timezone->id }}" {{ $admin_user->contact && $timezone->id == $admin_user->contact->timezone_id ? 'selected' : '' }} >{{ $timezone->label }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Avatar&nbsp;</label>
				        <div class="col-md-5">
				            <div class="image-container margin-bottom-10 user-avatar show" id="user-avatar">
				            	@if ($admin_user->id)
				            	<img src="{{ avatar_url($admin_user)}}" width="50" class="img-circle">
				            	@endif
				            </div>
				            <div class="image-container margin-bottom-10 crop-preview hide" id="temp-avatar">
				            	<img id="tempImage" width="100%" height="100%" />
				            </div>

							<input type="hidden" name="x1" id="x1" />
						    <input type="hidden" name="y1" id="y1" />
						    <input type="hidden" name="width" id="w" />
						    <input type="hidden" name="height" id="h" />
							<input type="hidden" name="file_ids" />
							<input type="hidden" name="file_type" value="{{ File::TYPE_USER_AVATAR }}" />

				            <div class="fileinput fileinput-new" data-provides="fileinput">
					            <span class="btn green btn-file">
					                <span class="fileinput-new">Select Image</button></span>
					                <span class="fileinput-exists">Change</span>
					                <input type="file" id="avatar" name="attached_files" />
					            </span>
					            <a href="javascript:;" id="file_remove" class="btn default fileinput-exists" data-dismiss="fileinput">Remove</a>
					        </div>
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
			<button type="submit" class="save-button btn blue">Save</button>
		</div>
	</form>
</div>