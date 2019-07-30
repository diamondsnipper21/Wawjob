@extends('layouts/default/index')

@section('content')

<div class="page-content-section no-padding">
	<div class="view-section job-content-section">
        <div class="row">
            <div class="col-md-9">
                <div class="box-section page-content">

					{{ show_warnings() }}
					{{ show_messages() }}

                	<div class="job-top-section mb-4">
						<div class="title-section border-0">
							<span class="title">{{ trans('profile.you_are_almost_ready_to_start') }}</span>
						</div>
					</div>

					<div class="border-light-bottom pb-4 mb-4">
						{{ trans('profile.profile_start_instruction') }}
					</div>

					<div class="text-large border-light-bottom pb-4 mb-4">
						1. {{ trans('profile.set_your_security_question') }}
					</div>

					<div class="text-large border-light-bottom pb-4 mb-4">
						2. {{ trans('profile.tell_us_about_you') }}
					</div>

					<div class="pt-4 pb-4">
						<form method="post" action="{{ Request::url() }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<button type="submit" class="btn btn-primary">{{ trans('common.apply') }}</button>
						</form>
					</div>
                </div>
            </div><!-- .col-md-9 -->

            <div class="col-md-3 page-content">
                <div class="instruction">
                    <div class="title">{{ trans('profile.why_apply') }}</div>
                    <ul class="pb-4">
                        <li class="mb-4">{{ trans('profile.belong_to_an_exclusive_network') }}</li>
                        <li class="mb-4">{{ trans('profile.get_highly_relevant_jobs_to_your_skills') }}</li>
                    </ul>
                </div>
            </div>
        </div><!-- .roww -->
	</div><!-- .view-section -->
</div><!-- .page-content-section -->

@endsection