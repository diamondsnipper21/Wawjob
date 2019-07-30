@extends('layouts/auth/login')

@section('content')
<div id="reset_password" class="border-box">
	<form id="reset_password_form" class="form-horizontal" method="post" action="{{ route('forgot.reset', ['token' => $token]) }}" autocomplete="off">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		
		<div class="col-md-12">
			{{ show_messages() }}
		</div>

		<div class="form-group">
			<div class="col-md-12">
				<h4>{{ trans('page.auth.reset.title') }}</h4>
			</div>
		</div>

		<!-- Password -->
		<div class="form-group">
			<div class="col-md-12">
				<div class="input-group">
                    <span class="input-group-addon"><i class="icon-media-094 u-line-icon-pro"></i></span>
					<input type="password" class="form-control" id="password" name="password" placeholder="{{ trans('auth.password') }}" data-rule-required="true" data-rule-minlength="8" data-rule-password_alphabetic="true" data-rule-password_number="true">
				</div>
			</div>
		</div>

		<!-- Confirm Password -->
		<div class="form-group">
			<div class="col-md-12">
				<div class="input-group">
                    <span class="input-group-addon"><i class="icon-media-094 u-line-icon-pro"></i></span>
					<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ trans('auth.confirm_password') }}" data-rule-required="true" data-rule-equalto="#password">
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12 padding-top-10">
				<button type="submit" class="btn btn-primary">{{ trans('page.auth.reset.reset_password') }}</button>
			</div>
		</div>
	</form>
</div>
@endsection