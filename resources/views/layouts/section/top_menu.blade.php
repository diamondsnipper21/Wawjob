<?php
/**
 * @desc Top Menu
 * @author KCG
 * @since Feb 25, 2018
 */

if ( $current_user ) {
	$role = strtolower($current_user->role_name());
}
?>

<nav class="navbar">
    <div class=container-fluid>
        
        @include('layouts.section.logo')

		<div class="mobile-menu-wrap">
        	@if ( $current_user )
                @if ( $current_user->isFreelancer() )
                    <a href="{{ route('user.my_profile') }}">
                @endif
                <img class="img-circle hide1 pull-left user-avatar" src="{{ avatar_url($current_user) }}" width="32" height="32" />
                @if ( $current_user->isFreelancer() )
                    </a>
                @endif
        	@endif
            <a class="btn-mobile-link pull-left" data-toggle="dropdown" data-close-others="true" href="#"><i class="fa fa-list"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
            @if ($current_user && $main_menu)
            	<li>
            		<ul class="sub-menus">
		            @foreach ($main_menu as $key => $root)
		                <li class="{{ $key }}">
		                    <a href="{{ !$root['children'] ? route($root['route']) : '#' }}" class="{{ $root['children'] ? 'sub-title' : '' }}">{{ trans('menu.'.$role.'_main_menu.' . $key . '.title') }}</a>

		                    @if ( $root['children'] )
		                    <ul class="sub-menu">
		                        @foreach ($root['children'] as $sub_key => $sub_root)
		                        <li class="{{ empty($sub_root['seperator'])?'':'seperator' }}"><a href="{{ $sub_root['route'] ? route($sub_root['route']) : '#' }}">{{ trans('menu.'.$role.'_main_menu.' . $key . '.' . $sub_key . '.title') }}</a></li>
		                        @endforeach
		                    </ul>
		                    @else
		                    @endif
		                </li>
		            @endforeach
		        	</ul>
		        </li>
		        <li>
                	@include('layouts.user.section.sidebar_menu')
                </li>
            @else
                <li><a href="{{ route('job.create') }}">{{ trans('common.post_job') }}</a></li>
                <li><a href="{{ route('user.login') }}">{{ trans('page.auth.login.title_with_space') }}</a></li>
                <li><a href="{{ route('user.signup') }}">{{ trans('page.auth.signup.title_with_space') }}</a></li>
            @endif
            </ul>
        </div><!-- .mobile-menu-wrap -->

        <div class="collapse navbar-collapse" id="bs-navbar-collapse">
        @if ($current_user && $main_menu)
            <ul class="nav navbar-nav main-menu">
            @foreach ($main_menu as $key => $root)
                <?php
                    $main_sub_menu = $root['children'];
                    $selected = false;

                    foreach ($root['patterns'] as $pattern) {
                        if (str_is($pattern, $page))
                            $selected = true;
                    }
                ?>
                <li class="{{ $main_sub_menu?'dropdown no-hover-effect':'' }} {{ $key }} {{ $selected?' active' : '' }}">
                    @if ($key == 'messages')
                        <span class="badge badge-default msg-notification {{ $unread_msg_count > 0?'':'hide' }}">{{ $unread_msg_count }}</span>
                    @endif

                    <a href="{{ !$main_sub_menu && $root['route'] ? route($root['route']) : '#' }}" {!! $main_sub_menu?' class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"':'' !!}>{{ trans('menu.'.$role.'_main_menu.' . $key . '.title') }} @if ($main_sub_menu)&nbsp;&nbsp;<i class="icon-arrow-down caret-o"></i>@endif</a>

                    <!-- Sub Menus -->
                    @if ($main_sub_menu)
                    <ul class="dropdown-menu">
                        @foreach ($main_sub_menu as $sub_key => $sub_root)
                        <li class="{{ empty($sub_root['seperator'])?'':'seperator' }}"><a href="{{ $sub_root['route'] ? route($sub_root['route']) : '#' }}">{{ trans('menu.'.$role.'_main_menu.' . $key . '.' . $sub_key . '.title') }}</a></li>
                        @endforeach
                    </ul>
                    @endif
                </li>
            @endforeach
            </ul>
            @include('layouts.section.top_right_menu')
        @elseif (!$current_user)
            <div id="top_search_box" class="search-form navbar-form navbar-left">
                @include('layouts.section.search_box')
            </div>

            <ul class="nav navbar-nav navbar-right guest-right-menu">
                <li class="post-job"><button data-href="{{ route('job.create') }}" class="btn btn-primary">{{ trans('common.post_job') }}</button></li>
                <li><a href="{{ route('user.login') }}" class="text-uppercase"><i class="icon-login"></i>{{ trans('page.auth.login.title_with_space') }}</a></li>
                <li><a href="{{ route('user.signup') }}" class="text-uppercase"><i class="icon-people"></i>{{ trans('page.auth.signup.title_with_space') }}</a></li>
            </ul>
        @endif
        </div>
    </div>
</nav>