@extends('layouts/auth/signup')

@section('content')
<div class="border-box default-boxshadow">
	<div class="row form-group mb-4">
		<div class="col-md-12 text-center">		
			<h4>{{ trans('page.auth.signup.success.verify_your_email_address')}}</h4>
		</div>
	</div>

	<div class="row">
		<div id="success_signup" class="col-md-12 col-sm-12">
			{{ show_messages() }}
			<form id="success_signup_form" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_action" value="change">

				<div class="heading">
					{{ trans('page.auth.signup.success.sent_email_description', ['email' => $user->email]) }}
				</div>

				<p>{{ trans('page.auth.signup.success.check_email_description') }} <a class="btn btn-link" id="btn_change_email">{{ trans('page.auth.signup.success.change_email') }}</a></p>

				<div class="change-box">
					<div class="row">
						<div class="col-md-9">
							<div class="form-group">
								<label class="control-label">{{ trans('page.auth.signup.success.new_email') }}</label>
								<div class="input-group">
		                            <span class="input-group-addon bg-transparent"><i class="icon-communication-062 u-line-icon-pro"></i></span>
		                            <input type="text" name="new_email" id="new_email" class="form-control border-left-0">
		                        </div>
							</div>
							<div class="form-group">
								<button type="button" class="btn btn-primary" id="btn_submit_change">{{ trans('common.change') }}</button>
								<button type="button" class="btn btn-link" id="btn_cancel_change">{{ trans('common.cancel') }}</button>
							</div>
						</div>
					</div>
				</div>

				<a id="btn_resend_email" class="btn btn-link">{{ trans('page.auth.signup.success.resend_verification_email') }}</a>
			</form>
		</div>
	</div>
</div>
@endsection