<?php
/**
 * Security Question Page (user/security-question)
 *
 * @author  - Ro Un Nam
 */
?>
@extends('layouts/default/index')

@section('content')
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 border-box">
		<div class="form-group mb-4">
			<div class="col-md-12 text-center">		
				<h4>{{ trans('page.' . $page . '.title') }}</h4>
			</div>
		</div>
		<div class="page-content-section user-security-question-page">
			<div class="form-section">
				<form id="security_question_form" class="form-horizontal" method="post" action="{{ route('user.security_question')}}" enctype="multipart/form-data">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					{{ show_messages() }}

					<fieldset>
						<div class="form-group row">
							<div class="col-sm-3 control-label">
								<div class="pre-summary">{{ trans('user.change_security_question.question') }}</div>
							</div>
							<div class="col-sm-9">
								<div class="w-75">
									<label class="control-label">{{ parse_json_multilang($user_security_question->question) }}</label>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-3 control-label">
								<div class="pre-summary">{{ trans('user.change_security_question.answer') }}</div>
							</div>
							<div class="col-sm-9">
								<div class="w-75">
									<input type="password" class="form-control" id="answer" name="answer" autocomplete="off" data-rule-required="true" value="{{ $remember_answer }}">
								</div>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-3 control-label">
							</div>
							<div class="col-sm-9">
								<div class="w-75">                    
									<div class="chk">
										<label><input type="checkbox" id="remember" name="remember">{{ trans('user.change_security_question.remeber_this_computer') }}</label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group row pt-4">
							<div class="col-sm-9 col-sm-offset-3">
								<button type="submit" class="btn btn-primary">{{ trans('common.submit') }}</button>
								<a href="{{ $back_url }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
								<input type="hidden" name="back_url" value="{{ $back_url }}">
							</div>
						</div>

					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection