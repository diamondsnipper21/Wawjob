@extends('layouts/auth/login')

@section('content')

<div id="forgot_password" class="border-box">
	<form id="forgot_form" class="form-horizontal form-without-legend" method="post" action="{{ route('forgot') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		
		<div class="col-md-12">
			{{ show_messages() }}
		</div>

		<div class="form-group mb-4">
			<div class="col-md-12 text-center">	
				<h4>{{ trans('page.auth.forgot.title') }}</h4>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12">
				<p>{{ trans('page.auth.forgot.description') }}</p>
			</div>
		</div>

		<div class="form-group m-0">
			<div class="input-group">
				<span class="input-group-addon"><i class="icon-finance-067 u-line-icon-pro"></i></span>
				<input type="text" class="form-control" id="email" name="email" value="{{ isset($email) ? $email : '' }}" placeholder="{{ trans('common.email') }}" data-rule-email="true" data-rule-required="true">
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12 padding-top-10">
				<a href="{{ route('user.login') }}" class="pull-left margin-top-10"><i class="fa fa-angle-left"></i> {{ trans('page.auth.forgot.back_to_login') }}</a>
				<button type="submit" class="btn btn-primary pull-right">{{ trans('page.auth.forgot.reset_password') }}</button>
			</div>
		</div>
		
	</form>
</div>
@endsection