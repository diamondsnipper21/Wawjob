
<?php
/**
 * @desc Top Menu
 * @author KCG
 * @since Feb 25, 2018
 */

use iJobDesk\Models\User;

$role = strtolower($current_user->role_name());
$another_role = ($role == 'buyer'?'freelancer':'buyer');

?>
<ul class="nav navbar-nav navbar-right right-menu">
	<!-- BEGIN USER LANGUAGE DROPDOWN -->
	@include ('layouts.section.menu_lang')
	<!-- END USER LANGUAGE DROPDOWN -->
	
	<li id="header_search_bar" class="no-hover-effect">
		<a class="magnifier btn btn-link">
			<i class="icon icon-magnifier"></i>
		</a>
		<div id="top_search_box" class="search-form magnifier-box">
		    @include('layouts.section.search_box')
		</div>
	</li>
	
	<!-- BEGIN Help DROPDOWN -->
	<li class="dropdown dropdown-extended dropdown-inbox no-hover-effect" id="header_help_bar">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
			<i class="icon icon-question"></i>
			@if ($unread_ticket_messages != 0)
			<span class="badge badge-default">{{ $unread_ticket_messages }}</span>
			@endif
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li><a href="{{ route('frontend.how_it_works') }}">{{ trans('footer.how_it_works') }}</a></li>
			<li><a href="{{ route('frontend.help') }}">{{ trans('footer.help') }}</a></li>
			<li class="unread-comment-count">
				<a href="{{route('ticket.list')}}">{{ trans('common.support') }}</a>
				<span class="badge badge-default count ticket-message-count {{ $unread_ticket_messages == 0?'hide':'' }}">{{ $unread_ticket_messages }}</span>
			</li>
		</ul>
	</li>
	<!-- END Help DROPDOWN -->

	<!-- BEGIN Notification DROPDOWN -->
	<li class="dropdown dropdown-extended dropdown-tasks no-hover-effect" id="header_notification_bar">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
			<i class="icon-bell icon"></i>
			<span class="badge badge-default notfication-cnt">{{ $unread_cnt }}</span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			@if ( $unread_cnt )
				@foreach ($unread_notifications as $unread)
				<li class="notification nid{{$unread->id}}">
					<a href="#" notification-id="{{$unread->id}}">{!! nl2br($unread->notification) !!}<i class="fa fa-times"></i></a>
				</li>
				@endforeach
				<li class="notification-all">
					<a href="{{route('notification.list')}}">{{ trans('common.see_all_notifications') }}</a>
				</li>
			@else
			<li class="notification-all-empty">
				<a href="{{route('notification.list')}}">{{ trans('common.see_all_notifications') }}</a>
			</li>
			@endif
		</ul>
	</li>
	<!-- END Notification DROPDOWN -->

	<!-- BEGIN USER LOGIN DROPDOWN -->
	<li class="dropdown dropdown-user no-hover-effect">
		<a href="#" class="dropdown-toggle user-menu-link" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
			<img class="img-circle hide1 pull-left user-avatar" src="{{ avatar_url($current_user) }}" width="32" height="32" />
			<span class="userbox pull-left">
				<span class="username username-hide-on-mobile">
					{{ $current_user? $current_user->fullname() : "" }}
				</span>
				<i class="icon-arrow-down"></i>
				<span class="userrole userrole-hide-on-mobile">{{ trans('common.as_'.$role) }}</span>
			</span>
			<span class="clearfix"></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
		@if ($right_menu)
			@foreach ($right_menu as $root_key => $root)
				@if ($root['route'] != '#')
					@if ($root_key != $another_role . '_workshop' || ($root_key == $another_role . '_workshop' && $current_user->role == User::ROLE_USER_BOTH))
					<li class="{{ empty($root['seperator'])?'':'seperator' }}">
						<a href="{{ $root['route'] ? route($root['route']) : 'javascript:;' }}">
							@if ($root['icon'])
							<i class="{{ $root['icon'] }}"></i>
							@endif
							{{ trans('menu.'.$role.'_right_menu.' . $root_key . '.title') }} 
						</a>
					</li>
					@endif  
				@endif
			@endforeach
		@endif
		</ul>
	</li>
	<!-- END USER LOGIN DROPDOWN -->
</ul>