<?php
/**
* Change Password Page (user/change-password)
*
* @author  - Ro Un Nam
*/
?>
@extends('layouts/user/index')

@section('content')
<div class="title-section">
    <span class="title">
        <i class="icon-lock title-icon"></i>
        {{ trans('page.' . $page . '.title') }}
    </span>
</div>
<div class="page-content-section user-change-password-page">
    <div class="form-section">
        <form id="change_password_form" class="form-horizontal" method="post" action="{{ route('user.change_password') }}" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            {{ show_messages() }}

            <div class="mb-4">
                {{ trans('user.change_password.in_order_to_change') }}
            </div>

            <fieldset>
                {{-- Old Password --}}
                <div class="form-group row">
                    <div class="col-sm-3 col-xs-6 control-label">
                        <div class="pre-summary">{{ trans('user.change_password.old') }}</div>
                    </div>
                    <div class="col-sm-9 col-xs-6">
                        <div class="input-group w-50">
                            <input type="password" class="form-control border-right-0" id="old_password" name="old_password" autocomplete="off" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }} 
                            value="{{ old('old_password') ? old('old_password') : "" }}">
                            <span class="input-group-addon bg-transparent"><i class="icon-lock"></i></span>
                        </div>
                    </div>
                </div>

                {{-- New Password --}}
                <div class="form-group row">
                    <div class="col-sm-3 col-xs-6 control-label">
                        <div class="pre-summary">{{ trans('user.change_password.new') }}</div>
                    </div>
                    <div class="col-sm-9 col-xs-6">
                        <div class="input-group w-50">
                            <input type="password" class="form-control border-right-0" id="new_password" name="new_password" autocomplete="off" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                            <span class="input-group-addon bg-transparent"><i class="icon-lock"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="row form-group">
                    <div class="col-sm-3 col-xs-6 control-label">
                        <div class="pre-summary">{{ trans('user.change_password.confirm') }}</div> 
                    </div>
                    <div class="col-sm-9 col-xs-6">
                        <div class="input-group w-50">
                            <input type="password" class="form-control border-right-0" id="confirm_password" name="confirm_password" autocomplete="off" data-rule-required="true" data-rule-equalto="#new_password" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                            <span class="input-group-addon bg-transparent"><i class="icon-lock"></i></span>
                        </div>
                    </div>
                </div>

                <div class="row form-group pt-4">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.save') }}</button>
                        <a href="{{ route('user.contact_info') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@endsection