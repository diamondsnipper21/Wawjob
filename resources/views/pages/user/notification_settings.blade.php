<?php
/**
 * Notification Settings Page (user/notification-settings)
 *
 * @author  - Ro Un Nam
 */
?>
@extends(!$current_user->isAdmin()?'layouts/user/index':'layouts/admin/super/user')

@section('content')
<div class="title-section">
    <span class="title admin-title caption-subject font-green-sharp bold">
        <i class="icon-settings title-icon"></i>
        {{ trans('page.user.notification_settings.title') }}
    </span>
</div>
<div class="page-content-section user-notification-settings-page">
    <div id="notification_settings" class="form-section">
        <form id="notification_settings_form" class="form-horizontal" method="post" action="{{ route('user.notification_settings') }}" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
          
            {{ show_messages() }}

            <div class="mb-4">
                {{ trans('user.notification_settings.send_email_notification') }} {{ $user->email }} {{ trans('user.notification_settings.when') }}
            </div>

            @foreach ($notification_settings as $category => $settings)
                @if (in_array($category, ['freelancer_job_recommendations', 'job_recommendations']) && $user->isBuyer())
                @elseif ($category == 'recruiting' && $user->isFreelancer())
                @else
                    <fieldset class="pt-4">
                        <div class="row form-group sub-header">
                            <div class="col-sm-12">{{ trans('user.notification_settings.' . $category) }}</div>
                        </div>            
                        @foreach ($settings as $setting => $value)
                        <div class="row form-group setting">
                            <div class="col-sm-10 col-xs-9 mt-2">
                                <label for="{{ $setting }}" class="control-label">{{ trans('user.notification_settings.' . $setting) }}</label>
                            </div>
                            <div class="col-sm-2 col-xs-3 toggle-checkbox">
                                <input type="checkbox" class="pull-right" id="{{ $setting }}" name="notification_settings[{{ $setting }}]" value="1"{{ $value == 1 ? ' checked' : '' }} {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                            </div>
                        </div>                
                        @endforeach

                    </fieldset>
                @endif
            @endforeach

            <div class="row form-group pt-4 actions">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.save') }}</button>
                    <a href="{{ route('user.contact_info') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection