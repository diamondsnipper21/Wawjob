@extends('layouts/frontend/index')

@section('css')
<link rel="stylesheet" href="{{ url('assets/styles/frontend/contact_us.css') }}">
@endsection

@section('content')
<div id="contact_us_container" class="row">
	<div class="col-md-12">
		<div class="container page-contact-us">
			<div class="title text-center">
				<h1><h1>{{ trans('page.frontend.contact_us.title') }}</h1></h1>
				<div class="hover-line"></div>
				<!-- <div class="desc">{!! trans('page.frontend.contact_us.desc') !!}</div> -->
			</div>

			<div class="row">
				<div class="col-md-8 col-sm-6">

					<div class="row">

						<div class="col-md-10">

							{{ show_messages() }}
							<div class="content margin-top-20">
								@if ($sent)
									<div class="success-message text-center mt-4 pt-5">{{ trans('page.frontend.contact_us.success') }}</div>
								@else

								<div class="border-light-bottom mb-3 pb-2">
									<h4>{{ trans('page.frontend.contact_us.got_a_question') }}</h4>
								</div>

								<form id="formContact" class="pt-3" method="post" action="{{ route('frontend.contact_us') }}">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label"><strong>{{ trans('ticket.modal.your_name') }} <span class="form-required">*</span></strong></label>
												<div class="">
													<input type="text" name="fullname" class="form-control" data-rule-required="true" value="{{ $fullname }}">
												</div>	
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label"><strong>{{ trans('ticket.modal.Email') }} <span class="form-required">*</span></strong></label>
												<div class="">
													<input type="text" name="email" class="form-control" data-rule-required="true" data-rule-email="true" value="{{ $email }}">
												</div>	
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="control-label"><strong>{{ trans('ticket.modal.Subject') }} <span class="form-required">*</span></strong></label>
										<div class="">
											<input type="text" name="subject" class="form-control" data-rule-required="true" value="{{ $subject }}">
										</div>	
									</div>

									<div class="form-group">
										<label class="control-label"><strong>{{ trans('ticket.modal.message') }} <span class="form-required">*</span></strong></label>
										<div class="box-message">
											<textarea class="form-control maxlength-handler" rows="6" name="content" id="content" data-rule-required="true" maxlength="5000">{{ $content }}</textarea>
										</div>
									</div>

									<div class="form-group">
										<label class="control-label"><strong>{{ trans('ticket.modal.captcha') }} <span class="form-required">*</span></strong></label>
										<div class="row mt-3">
											<div class="col-md-4">
												<div class="captcha-img">{!! app('captcha')->img('default') !!}</div>
											</div>
											<div class="col-md-8 captcha">
												<div class="input-group">
					                                <span class="input-group-addon captcha-refresh"><i class="hs-admin-reload"></i></span>
					                                <input type="text" class="form-control" id="captcha" name="captcha" autocomplete="off" data-rule-required="true" placeholder="{{ trans('auth.type_letters') }}">
					                            </div>

					                            @if (!$captcha_result)
					                            <span id="captch_f_error" class="error">{{ trans('common.validation.match') }}</span>
					                            @endif
				                            </div>
				                        </div>
			                        </div>
			                        <div class="border-top mt-4"></div>
									<div class="form-group mt-4">
										<button type="submit" class="btn btn-primary">{{ trans('common.submit') }}</button>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<a href="/">{{ trans('common.cancel') }}</a>
									</div>
								</form>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-sm-6 contact-right">
					<div class="map border-light-bottom mt-5 pb-5 mb-5">
						<a href="https://maps.google.com/maps?ll=59.435926,24.770304&z=16&t=m&hl=en-US&gl=HK&mapclient=embed&q=Tina%2021%2010125%20Tallinn%20Estonia" target="_blank"><img src="{{ url('assets/images/about/map.png') }}" width="360" height="300" class="img-responsive" /></a>
					</div>

					<h3 class="mb-4 pb-4">{{ trans('ticket.contact_info') }}</h3>

					<div class="mb-2">
						<i class="icon-location-pin pull-left"></i>
						<div class="location">
							{{ $company_address }}
						</div>
					</div>

					<div class="pt-2 pb-5">
						<i class="icon-envelope-letter pull-left"></i>

						<div class="email">
							Email: <a href="mailto:{{ $contact_email }}">{{ $contact_email }}</a>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection