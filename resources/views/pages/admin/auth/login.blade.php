<?php
/**
* Login Page
*
* @author KCG
* @since June 8, 2017
* @version 1.0
*/
?>
@extends('layouts/admin/single')

@section('content')
<!-- BEGIN LOGIN FORM -->
<form class="login-form" action="{{ route('admin.user.login') }}" method="post">
    
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <h3 class="form-title">Sign In</h3>

    {{ show_messages() }}

    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">Username</label>
        <input class="form-control form-control-solid placeholder-no-fix" type="text" placeholder="Username" name="username" data-rule-required="true" />
    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">Password</label>
        <input class="form-control form-control-solid placeholder-no-fix" type="password" placeholder="Password" name="password" data-rule-required="true" />
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-success uppercase">Login</button>
        <label class="rememberme check"><input type="checkbox" name="remember" value="1"/>Remember </label>
    </div>
</form>
<!-- END LOGIN FORM -->
@endsection