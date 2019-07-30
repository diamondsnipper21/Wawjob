<?php
/**
* Change Security Question Page (user/change-security-question)
*
* @author  - Ro Un Nam
*/
?>
@extends('layouts/user/index')

@section('content')
<div class="title-section">
    <span class="title">
        <i class="icon-shield title-icon"></i>
        @if ( $has_security_question )
            {{ trans('page.' . $page . '.title') }}
        @else
            {{ trans('page.user.security_question.title') }}
        @endif
    </span>
</div>
<div class="page-content-section user-change-security-question-page">
    <div class="form-section">
        <form id="change_security_question_form" class="form-horizontal" method="post" action="{{ route('user.change_security_question')}}" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_action" value="{{ $has_security_question ? 'update' : 'create' }}">

            {{ show_messages() }}

            <div class="mb-4 lh-2">
                @if ( $has_security_question )
                {{ trans('user.change_security_question.text_answer') }}
                @endif
                {!! trans('user.change_security_question.text_security_question') !!}
                <a href="{{ route('frontend.help.detail', ['slug' => 'change-security-question']) }}" target="_blank">{{ trans('common.learn_more') }}</a>
            </div>

            @if ( $has_security_question )
            <div class="form-group row">
                <div class="col-sm-3 col-xs-6 control-label">
                    <div class="pre-summary">{{ trans('user.change_security_question.existing_question') }}</div>
                </div>
                <div class="col-sm-9 col-xs-6">
                    <div class="w-50">
                        <label class="control-label">{{ parse_json_multilang($user_security_question->question) }}</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-3 col-xs-6 control-label">
                    <div class="pre-summary">{{ trans('user.change_security_question.answer') }}</div>
                </div>
                <div class="col-sm-9 col-xs-6">
                    <div class="input-group w-50">
                        <input type="password" class="form-control border-right-0" id="old_answer" name="old_answer" autocomplete="off" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                        <span class="input-group-addon bg-transparent"><i class="icon-lock"></i></span>
                    </div>
                </div>
            </div>
            <hr>
            @endif

            @include('pages.partials.security_question')

            <div class="form-group row pt-4">
                <div class="col-sm-9 col-sm-offset-3">
                    <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.save') }}</button>
                    <a href="{{ route('user.contact_info') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection