@extends('layouts/auth/signup')

@section('content')
<div id="main_body" class="container-fluid shadow-box">
    <div class="row">
        <div class="col-sm-12">
            <div class="summary-block">
                <div class="form-group mb-4">
                    <div class="col-md-12 text-center">     
                        <h4>{{ trans('page.auth.signup.'.$role.'.title') }}</h4>
                    </div>
                </div>
                <div class="content-block">
                    <div class="text-center content_12">
                    @if ($role == 'buyer')
                        {{ trans('page.auth.signup.buyer.looking_work')}}? <a href="{{ route('user.signup.user', ['role' => 'freelancer']) }}{{ $ref ? '?ref=' . $ref : '' }}">{{ trans('page.auth.signup.buyer.signup_as_freelancer')}}</a>
                    @else
                        {{ trans('page.auth.signup.freelancer.looking_hire')}}? <a href="{{ route('user.signup.user', ['role' => 'buyer']) }}{{ $ref ? '?ref=' . $ref : '' }}">{{ trans('page.auth.signup.freelancer.signup_as_buyer')}}</a>
                    @endif
                    </div>
                </div>
            </div>
            <div id="register_panel">
                <form id="frm_register" method="post" data-check="{{ route('user.signup.checkfield') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="ref" value="{{ $ref }}">

                    {{ show_messages() }}

                    <div class="row mb-4">
                        <!-- First Name -->
                        <div class="col-sm-6 col-first-name">
                            <div class="input-group form-group">
                                <span class="input-group-addon"><i class="icon-finance-067 u-line-icon-pro"></i></span>
                                <input type="text" class="form-control" id="ele_first_name" name="first_name" autocomplete="off" data-rule-required="true"{{ $submitted ? ' data-submitted=true' : '' }} value="{{ old('first_name') }}" placeholder="{{ trans('auth.first_name')}}">
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-sm-6 col-last-name">
                            <div class="input-group form-group">
                                <span class="input-group-addon"><i class="icon-finance-067 u-line-icon-pro"></i></span>
                                <input type="text" class="form-control" id="ele_last_name" name="last_name" autocomplete="off" data-rule-required="true"{{ $submitted ? ' data-submitted=true' : '' }} value="{{ old('last_name') }}" placeholder="{{ trans('auth.last_name')}}">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <!-- Email Address -->
                        <div class="col-md-12 col-email">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-communication-062 u-line-icon-pro"></i></span>
                                <input type="text" class="form-control" id="ele_email" name="email" autocomplete="off" data-rule-required="true" data-rule-email="true" data-rule-remote="{{ route('user.signup.checkfield', ['field' => 'email']) }}"{{ $submitted ? ' data-submitted=true' : '' }} value="{{ old('email') }}" placeholder="{{ trans('auth.email')}}">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <!-- Username -->
                        <div class="col-sm-6 col-username">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-education-052 u-line-icon-pro u-line-icon-pro"></i></span>
                                <input type="text" class="form-control" id="ele_username" name="username" value="{{ old('username') }}" autocomplete="off" data-rule-required="true" data-rule-minlength="4" data-rule-maxlength="30" data-rule-username="true" data-rule-remote="{{ route('user.signup.checkfield', ['field' => 'username']) }}"{{ $submitted ? ' data-submitted=true' : '' }} placeholder="{{ trans('auth.username') }}">
                            </div>
                        </div>
                        <!-- Country -->
                        <div class="col-sm-6 col-country">
                            <select name="country" id="ele_country" class="form-control select2-country edited" data-rule-required="true">
                                <option value="" selected>{{ trans('auth.country')}}</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->charcode }}"{{ old('country') == $country->charcode/* || (!old('country') && $country->charcode == $defaults['country'])*/ ? ' selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- Password -->
                        <div class="col-sm-6 col-password">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-media-094 u-line-icon-pro"></i></span>
                                <input type="password" class="form-control" id="ele_password" name="password" autocomplete="off" data-rule-required="true" data-rule-minlength="8" data-rule-password_alphabetic="true" data-rule-password_number="true"{{ $submitted ? ' data-submitted=true' : '' }} placeholder="{{ trans('auth.password') }}">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-sm-6 col-password-confirm">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-media-094 u-line-icon-pro"></i></span>
                                <input type="password" class="form-control" id="ele_password2" autocomplete="off" data-rule-required="true" data-rule-equalto="#ele_password"{{ $submitted ? ' data-submitted=true' : '' }} placeholder="{{ trans('auth.confirm_password') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 col-sm-6 col-xs-6 captcha-img">{!! app('captcha')->img('default') !!}</div>
                        <div class="col-md-8 col-sm-6 col-xs-6">
                            <div class="input-group">
                                <span class="input-group-addon captcha-refresh"><i class="hs-admin-reload"></i></span>
                                <input type="text" class="form-control" id="ele_captcha" name="captcha" autocomplete="off" data-rule-required="true"{{ $submitted ? ' data-submitted=true' : '' }} placeholder="{{ trans('auth.type_letters') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 terms">
                            <div class="checkbox" data-rule-terms_of_service="true">
                                <label class="pl-0">
                                    <input type="checkbox" id="terms_of_service"><div class="pull-left">{!! trans('common.accept_terms_policy') !!}</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-actions">
                        <button type="submit" class="btn btn-primary center-align" disabled >{{ trans('auth.get_started')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection