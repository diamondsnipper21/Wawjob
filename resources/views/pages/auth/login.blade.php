@extends('layouts/auth/login')

@section('content')

<div class="border-box">
	<form id="login_form" class="form-horizontal form-without-legend" method="post" action="{{ route('user.login') }}{{ $from ? '?from=' . $from : '' }}">		
		
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />

		<div class="col-md-12">
			{{ show_messages() }}
		</div>

		<div class="form-group mb-4">
			<div class="col-md-12 text-center">		
				<h4>{{ trans('page.auth.login.login_and_work')}}</h4>
			</div>
		</div>

		<div class="input-group mb-4">
			<span class="input-group-addon"><i class="icon-finance-067 u-line-icon-pro"></i></span>
			<input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="{{ trans('page.auth.login.username_or_email')}}" index="-9999">
		</div>

		<div class="input-group mb-4">
			<span class="input-group-addon"><i class="icon-media-094 u-line-icon-pro"></i></span>
			<input type="password" class="form-control" id="password" name="password" placeholder="{{ trans('page.auth.login.password') }}" index="1">
		</div>

        @if ( $show_captcha == '1' )
        <div class="form-group mb-4">
        	<div class="col-md-5 captcha-img">
        		{!! app('captcha')->img('default') !!}
        	</div>
        	<div class="col-md-7">
        		<input type="text" class="form-control" name="captcha" id="captcha" placeholder="{{ trans('auth.short_type_letters') }}">
        		<input type="hidden" name="has_captcha" value="1">
        	</div>
        </div>
        @endif

        <div class="row mb-4">
        	<div class="col-xs-6">
        		<div class="chk">
        			<label class="fs-13"><input type="checkbox" name="remember" value="1"{{ old('remember') ? ' checked' : ''}}> {{ trans('page.auth.login.remember') }}</label>
        		</div>
        	</div>
        	<div class="col-xs-6 text-right">
        		<a href="{{ route('forgot') }}">{{ trans('page.auth.login.forgot') }}?</a>
        	</div>
        </div>

		<div class="form-group">
			<div class="col-md-12">
				<button type="submit" class="btn btn-primary btn-login">{{ trans('page.auth.login.title') }}</button>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12 padding-top-10 text-center">
				{!! trans('page.auth.login.signup', ['link' => route('user.signup')]) !!}
			</div>
		</div>
	</form>
</div>
@endsection