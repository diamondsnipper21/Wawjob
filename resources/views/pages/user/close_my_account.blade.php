<?php
/**
* Close My Account (user/close-my-account)
*
* @author  - Ro Un Nam
*/
?>
@extends('layouts/user/index')

@section('content')
<div class="title-section">
    <span class="title">
        <i class="icon-power title-icon"></i>
        {{ trans('page.' . $page . '.title') }}
    </span>
</div>
<div class="page-content-section user-close-account-page">
    <div class="form-section">
        <form id="close_my_account_form" class="form-horizontal" method="post" action="{{ route('user.close_my_account')}}" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_action" value="confirm">

            {{ show_messages() }}

            @if ( $action == 'confirm' )

                <input type="hidden" name="reason" value="{{ $reason }}">
                <input type="hidden" name="_reason" value="{{ $comment }}">

                <div class="sub-title">
                    <p>{{ trans('user.close_my_account.confirm_close_account') }}</p>

                    <p>{{ trans('user.close_my_account.permanently_deleted_description') }}</p>

                    <p>{{ trans('user.close_my_account.sorry_for_inconvenience') }}</p>
                </div>

                <fieldset>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <button type="submit" id="btn_confirm_close" class="btn btn-primary">{{ trans('common.confirm') }}</button>
                            <a href="{{ route('user.contact_info') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                        </div>
                    </div>
                </fieldset>        

            @else

                <div class="sub-title">
                    {{ trans('user.close_my_account.note_description') }}
                </div>

                <fieldset>
                    <div class="form-group row">
                        <div class="col-sm-3 col-xs-4 control-label">
                            <div class="pre-summary">{{ trans('common.reason') }}<span class="form-required"> *</span></div>
                        </div>
                        <div class="col-sm-9 col-xs-8">
                            <div class="form-line-wrapper w-40">
                                <select class="form-control select2" name="reason" id="reason" data-rule-required="true">
                                    <option value="">{{ trans('common.please_select') }}</option>
                                    <option value="1">{{ trans('user.close_my_account.reason_poor_service') }}</option>
                                    <option value="2">{{ trans('user.close_my_account.reason_irresponsive') }}</option>
                                    <option value="3">{{ trans('user.close_my_account.reason_complicated') }}</option>
                                    <option value="4">{{ trans('user.close_my_account.reason_poor_freelancers') }}</option>
                                    <option value="5">{{ trans('common.other') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3 col-xs-4 control-label">
                            <div class="pre-summary">{{ trans('common.comment') }}<span class="form-required"> *</span></div>
                        </div>
                        <div class="col-sm-9 col-xs-8">
                            <div class="form-line-wrapper2 w-80">
                                <textarea class="form-control" name="comment" id="comment" data-rule-required="true"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                    <div class="col-sm-offset-3 col-sm-9 col-xs-offset-4 col-xs-8">
                            <button type="submit" class="btn btn-primary">{{ trans('common.go') }}</button>
                            <a href="{{ route('user.contact_info') }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
                        </div>
                    </div>
                </fieldset>

            @endif
        </form>
    </div>
</div>
@endsection