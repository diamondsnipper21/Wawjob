@extends('layouts/default/index')

@section('content')

<div class="page-content-section no-padding">

    @include('pages.freelancer.step.header')
    
    <div class="view-section job-content-section">
        <div class="row">
            <div class="col-md-9">
                <div class="box-section page-content">

                    {{ show_warnings() }}
                    {{ show_messages() }}

                    <div class="job-top-section mb-4">
                        <div class="title-section">
                            <span class="title">1. {{ trans('page.user.security_question.title') }}</span>
                        </div>
                    </div>

                    <form id="security_question_form" class="form-horizontal" method="post" action="{{ Request::url() }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="mb-4 lh-2">
                            {!! trans('user.change_security_question.text_security_question') !!}
                            <a href="{{ route('frontend.help.detail', ['slug' => 'change-security-question']) }}" target="_blank">{{ trans('common.learn_more') }}</a>
                        </div>

                        @include('pages.partials.security_question')

                        <div class="form-group row pt-4">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.save') }}</button>
                                <a href="{{ Request::url() }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- .col-md-9 -->

            <div class="col-md-3 page-content">
            	@include('pages.freelancer.step.right_block')
            </div>
        </div><!-- .roww -->
    </div><!-- .view-section -->
</div><!-- .page-content-section -->

@endsection