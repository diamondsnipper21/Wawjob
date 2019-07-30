<?php
/**
 * Profile Settings Page /profile-settings
 */

use iJobDesk\Models\UserProfile;

?>
@extends('layouts/user/index')

@section('content')

<div class="title-section">
    <span class="title admin-title caption-subject font-green-sharp bold">
        <i class="icon-settings title-icon"></i>
        {{ trans('page.freelancer.user.profile_settings.title') }}
    </span>
</div>

<div class="page-content-section profile-settings-page">
    <div id="profile_settings" class="form-section">
        <form id="profile_settings_form" class="form-horizontal" method="post" action="{{ route('user.profile_settings') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
          
            {{ show_warnings() }}
			{{ show_messages() }}

			<!-- Visiblilty -->
			<div class="form-group">
			 	<label class="col-md-3 control-label">{{ trans('profile.profile_visibility') }}</label>
			 	<div class="col-md-9">
			 		@foreach (UserProfile::visibilities() as $key => $label)
			 		<div class="radio-box mt-2">
				        <label>
				         	<input type="radio" name="profile_share" value="{{ $key }}" {{ $key == $user->profile->share?'checked':'' }} /> {{ $label }}
				        </label>
				    </div>
			 		@endforeach
			 	</div>
			</div>

			<!-- Hide Earning -->
			<div class="form-group">
			 	<label class="col-md-3 control-label">{{ trans('profile.earning_privacy') }}</label>
			 	<div class="col-md-9 pt-2">
					<label><input type="checkbox" id="hide_earning" name="profile_hide_earning" value="1" {{ $user->profile->hide_earning ? 'checked' : '' }}>{{ trans('profile.hide_my_earning') }}</label>

					<div class="info pt-2">{{ trans('profile.hide_my_earning_note') }}</div>
				</div>
			</div>

            <div class="row form-group pt-5 actions">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.save') }}</button>
                    <a href="{{ route('user.profile_settings') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection