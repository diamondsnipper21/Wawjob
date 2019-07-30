<?php
/**
* Hire User Page (job/hire/user_id)
*
* @author  - nada
*/

use iJobDesk\Models\Project;
?>
@extends('layouts/default/index')

@section('content')
<div class="page-content-section no-padding">
    <div class="form-section">
        <form id="formHireUser" method="post" action="{{ _route('job.hire_user', ['uid' => $contractor->id])}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="mb-4">
                <div class="title-section clearfix border-0">
                    <div class="user-avatar pull-left margin-right-20">
                        <img src="{{ avatar_url($contractor) }}" alt="" class="img-circle" width="64" height="64">
                    </div>
                    <div class="title pull-left mt-3">{!! trans('common.hire_sb', ['sb' => $contractor->fullname()]) !!}</div>
                </div>
                {{ show_messages() }}
            </div>

            <div class="job-content-section no-padding row">
                <div class="col-md-9 col-sm-8">
                    <div class="border rounded mb-4 p-4">
                        <div class="form-group">
                            <div class="control-label">
                                {{ trans('common.related_job') }}
                            </div>
                        </div>

                        <div class="form-group">
                        	<div class="w-50">
                                <select class="form-control select2" id="job" name="job" placeholder="Job" data-rule-required="true">
                                    <option value="">{{ trans('common.please_select') }}</option>
                                    @foreach($jobs as $job) 
                                        <option value="{{ $job->id }}">{{ $job->subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="job-info mt-4"></div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-hire-user" disabled>{{ trans('common.hire') }}</button>
                            <a class="btn btn-link" href="{{ _route('user.profile', [$contractor->id]) }}">{{ trans('common.cancel') }}</a>
                        </div>
                    </div><!-- END OF .box-section -->
                </div>
                <div class="col-md-3 col-sm-4">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection