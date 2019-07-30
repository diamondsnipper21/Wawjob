<?php

use iJobDesk\Models\Todo;
?>

<form id='change_password_form' action="{{ route('admin.' . ($user->isTicket()?'ticket':'super') . '.account', ['action' => 'change-password']) }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="form-group">
        <label class="control-label">Old Password</label>
        <input type="password" name="old_password" class="form-control" data-rule-required="true" data-rule-validname="true" value="{{ old('old_password') }}">
    </div>
    <div class="form-group">
        <label class="control-label">New Password</label>
        <input type="password" id="new_password" name="new_password" class="form-control" data-rule-required="true" data-rule-validname="true" data-rule-minlength="8" value="{{ old('new_password') }}">
    </div>
    <div class="form-group">
        <label class="control-label">Confirm New Password</label>
        <input type="password" name="confirm_new_password" class="form-control" data-rule-required="true" data-rule-validname="true" data-rule-equalto="#new_password" value="{{ old('confirm_new_password') }}">
    </div>
    <div class="margin-top-10">
        <input type="submit" class="btn green-haze" />
        <a href="#" class="btn default cancel-btn">
        Cancel </a>
    </div>
</form>