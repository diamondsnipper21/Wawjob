<?php

/**
* User Sidebar Menu
*
* @author KCG
* @since Mar 3, 2018
* @version 1.0
* @param  Request $request
* @return Response
*/

use iJobDesk\Models\User;
?>

<div class="page-content">
@if ($page != 'freelancer.user.my_profile' && $page != 'freelancer.user.profile_settings')
    <div class="user-sidebar-menu {{ $role_id }}-sidebar-menu">
        <div class="title hidden-mobile">{{ trans('user.user_settings') }}</div>
        <ul>
            @foreach ($user_settings_menu as $key=>$root)
            <li class="menu-item{{ $root['active'] ? ' active' : ''}}{{ strpos($key, 'SEPERATOR') !== FALSE?' menu-seperator hidden-mobile':'' }}{{ isset($root['class']) ? ' ' . $root['class'] : '' }}">
                @if (strpos($key, 'SEPERATOR') === FALSE)
                    @if ($root['route'])
                    <a href="{{ route($root['route']) }}">
                        {{ trans('menu.' . $role_id . '_user_settings_menu.' . $key . '.title') }} 
                    </a>
                    @else
                    {{ trans('menu.' . $role_id . '_user_settings_menu.' . $key . '.title') }} 
                    @endif
                @endif
            </li>
            @endforeach
        </ul>

        @if ( $use_account_both && !$current_user->isSuspended() )
            @if ( $current_user->role != User::ROLE_USER_BOTH )
            <div class="become-{{ $role_id }}-section">
                <p class="become-{{ $role_id }}-description">{{ trans('user.become_freelancer.description') }}</p>
                <form class="form-horizontal" action="{{ route('user.switch') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <button type="submit" class="btn btn-primary">{{ trans('user.become_freelancer.button') }}</button>
                </form>
            </div>
            @endif
        @endif
    </div>
@else
    <div class="profile-sidebar user-sidebar-menu {{ $role_id }}-sidebar-menu">
        <div class="title hidden-mobile">{{ trans('page.freelancer.user.my_profile.title') }}</div>
        <ul>
            <li class="menu-item{{ $page == 'freelancer.user.my_profile' ? ' active' : '' }}">
                <a href="{{ $page == 'freelancer.user.profile_settings' ? _route('user.my_profile') : '' }}#about_me">{{ trans('profile.about_me') }}</a>
            </li>
            <li class="menu-item">
                <a href="{{ $page == 'freelancer.user.profile_settings' ? _route('user.my_profile') : '' }}#portfolios">{{ trans('common.portfolio') }}</a>
            </li>
            <li class="menu-item">
                <a href="{{ $page == 'freelancer.user.profile_settings' ? _route('user.my_profile') : '' }}#certifications">{{ trans('profile.certification') }}</a>
            </li>
            <li class="menu-item">
                <a href="{{ $page == 'freelancer.user.profile_settings' ? _route('user.my_profile') : '' }}#employments">{{ trans('profile.employment') }}</a>
            </li>
            <li class="menu-item">
                <a href="{{ $page == 'freelancer.user.profile_settings' ? _route('user.my_profile') : '' }}#educations">{{ trans('profile.education') }}</a>
            </li>
            <li class="menu-item">
                <a href="{{ $page == 'freelancer.user.profile_settings' ? _route('user.my_profile') : '' }}#experiences">{{ trans('profile.experience') }}</a>
            </li>
            <li class="menu-item menu-seperator hidden-mobile"></li>
            <li class="menu-item{{ $page == 'freelancer.user.profile_settings' ? ' active' : '' }}">
                <a href="{{ _route('user.profile_settings') }}">{{ trans('profile.profile_settings') }}</a>
            </li>
            <li class="menu-item menu-seperator hidden-mobile"></li>
            <li class="menu-item">
                <a href="{{ _route('user.profile', ['uid' => $user->id]) }}" target="_blank">{{ trans('profile.see_as_others_see') }}</a>
            </li>
        </ul>
    </div>
@endif
</div>