@extends('layouts/auth/signup')

@section('content')
  <div id="main_body" class="container-fluid shadow-box">

    <div class="mt-5 text-center">
        <h4>{{ trans('page.auth.signup.get_started') }}</h4>
    </div>
    <div class="text-center content_1">{{ trans('page.auth.signup.what_you_are_looking_for') }}</div>
    
    <div id="main_panel">
        <div class="left choose-type">
            <div class="block">
                <div class="icon"><img src="/assets/images/pages/auth/buyer.png"></div>
                <div class="text-center content_2">{{ trans('page.auth.signup.hire_a_freelancer') }}</div>
                <div class="text-center content_3">{!! trans('page.auth.signup.find_collaborate') !!}</div>
                <div class="col-sm-6 col-sm-offset-3">
                    <a class="button btn btn-primary" href="{{ $buyer_url }}">{{ trans('page.auth.signup.hire') }}</a>
                </div>
            </div>
        </div>
        <div class="div-line">OR</div>
        <div class="right choose-type">
            <div class="block">
                <div class="icon"><img src="/assets/images/pages/auth/freelancer.png"></div>
                <div class="text-center content_2">{{ trans('page.auth.signup.looking_for_online_work') }}</div>
                <div class="text-center content_3">{!! trans('page.auth.signup.find_freelance_projects') !!}</div>
                <div class="col-sm-6 col-sm-offset-3">
                    <a class="button btn btn-primary" href="{{ $freelancer_url }}">{{ trans('page.auth.signup.work') }}</a>
                </div>
            </div>
        </div>
        <div class="clear-div"></div>
    </div>
</div>
@endsection