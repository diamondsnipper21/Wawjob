<?php

use iJobDesk\Models\UserProfile;
use iJobDesk\Models\Category;
use iJobDesk\Models\Language;
use iJobDesk\Models\Skill;
use iJobDesk\Models\File;

?>

@extends('layouts/default/index')

@section('content')

<div id="profile_setup_page" class="page-content-section no-padding">

    @include('pages.freelancer.step.header')
    
    <div class="view-section job-content-section">
        <div class="row">
        	<div id="about_me" class="col-md-9">
        		<form action="{{ route('profile.step', ['step' => 2]) }}" method="post" class="form-horizontal page-content">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="_action" value="SAVE" />

					<div class="job-top-section mb-4">
                        <div class="title-section">
                            <span class="title">2. {{ trans('profile.about_me') }}</span>
                        </div>
                    </div>

	        		<div class="profile-page freelancer-step-about-me freelancer-step-content edit-mode {{ $current_user->isSuspended()?'disable-edit-mode':'' }}">
                        {{ show_messages() }}
	        			@include('pages.freelancer.user.myprofile.about_me')
	        		</div>

	        		<div class="form-group row pt-4">
	                    <div class="col-sm-9 col-sm-offset-3">
	                        <button type="submit" class="btn btn-primary" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('common.save') }} & {{ trans('common.next') }}</button>
	                        <a href="{{ Request::url() }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
	                    </div>
	                </div>
	            </form>
        	</div>

            <div class="col-md-3 page-content">
            	@include('pages.freelancer.step.right_block')
            </div>
        </div><!-- .roww -->
    </div><!-- .view-section -->
</div><!-- .page-content-section -->

@endsection