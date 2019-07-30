<?php

use iJobDesk\Models\Notification;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\AdminMessage;

?>
<!-- BEGIN TOP NAVIGATION MENU -->
<div id="header_bar" class="top-menu">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	<ul class="nav navbar-nav pull-right">
		<!-- BEGIN NOTIFICATION DROPDOWN -->
		<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
		<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
			<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				<i class="icon-bell"></i>
				<span class="badge badge-default unread-count">{{ count($unread_notifications) }}</span>
			</a>
			<ul class="dropdown-menu">
				<li class="external">
					<h3><span class="bold"><span class="unread-count">{{ count($unread_notifications) }}</span> pending</span> notifications</h3>
					<a href="{{ route('admin.'.($auth_user->isSuper()?'super':($auth_user->isFinancial() ? 'financial' : 'ticket')).'.notifications') }}">View All</a>
				</li>
				<li>
					<ul id="unread_notifications" class="dropdown-menu-list scroller" style="{{ $unread_notifications->isEmpty()?'':'height: 250px;'  }}" data-handle-color="#637283">
					@forelse ($unread_notifications as $n)
						<li>
							<a href="{{ route('admin.'.($auth_user->isSuper()?'super':($auth_user->isFinancial() ? 'financial' : 'ticket')).'.notification.read', ['id' => $n->id]) }}" data-nt-id="{{ $n->id }}">
								<span class="time">{{ ago($n->notified_at) }}</span>
								<span class="details">
									<span class="label label-priority label-{{ strtolower(array_search($n->ninfo ? $n->ninfo->priority : Notification::PRIORITY_NORMAL, Notification::options('priority'))) }}">
	                                    <i class="fa {{ UserNotification::iconByPriority($n->ninfo ? $n->ninfo->priority : Notification::PRIORITY_NORMAL) }}"></i>
	                                </span>&nbsp;
	                                <span class="nt-desc">{!! nl2br($n->notification) !!}</span>
								</span>
							</a>
							<i class="fa fa-times" data-url="{{ route('admin.'.($auth_user->isSuper()?'super':'ticket').'.notification.delete', ['id' => $n->id]) }}"></i>
						</li>
					@empty
						<li class="no-unread-data {{ $unread_notifications?'hide':'' }}">No Notifications</li>
					@endforelse
					</ul>
				</li>
			</ul>
		</li>
		<!-- END NOTIFICATION DROPDOWN -->
		<!-- BEGIN INBOX DROPDOWN -->
		<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
		<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
			<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				<i class="icon-envelope-open"></i>
				<span class="badge badge-default unread-count">{{ count($unread_messages) }}</span>
			</a>
			<ul class="dropdown-menu">
				<li class="external">
					<h3>You have <span class="bold"><span class="unread-count">{{ count($unread_messages) }}</span> New</span> Messages</h3>
					<a href="{{ route('admin.'.($auth_user->isSuper()?'super':($auth_user->isFinancial() ? 'financial' : 'ticket')).'.messages') }}">View All</a>
				</li>
				<li>
					<ul id="unread_messages" class="dropdown-menu-list scroller" style="{{ $unread_messages->isEmpty()?'':'height: 250px;'  }}" data-handle-color="#637283">
					@forelse ($unread_messages as $m)
						<li>
							<a href="{{ $m->link() }}">
								<span class="photo">
									<img src="{{ avatar_url($m->sender) }}" class="img-circle" alt="">
								</span>
								<span class="subject">
									<span class="from">
									@if ($m->sender && $m->message_type != AdminMessage::MESSAGE_TYPE_CONTACT)
										{!! $m->sender->getUserNameWithIcon() !!}
									@else
										{{ $m->contact_us->fullname }}
									@endif
									</span>
									<span class="time">{{ ago($m->created_at) }}</span>
								</span>
								<span class="message">{!! render_more_less_desc($m->message, 50, true, true) !!}</span>
							</a>
							<i class="fa fa-times" data-url="{{ route('admin.'.($auth_user->isSuper()?'super':($auth_user->isFinancial() ? 'financial' : 'ticket')).'.message.delete', ['id' => $m->id]) }}"></i>
						</li>
					@empty
						<li class="no-unread-data {{ $unread_messages?'hide':'' }}">No Messages</li>
					@endforelse
					</ul>
				</li>
			</ul>
		</li>
		<!-- END INBOX DROPDOWN -->
		<!-- BEGIN USER LOGIN DROPDOWN -->
		<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
		<li class="dropdown dropdown-user">
			<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
			<img alt="" class="img-circle" src="{{ avatar_url($auth_user) }}"/>
			<span class="username username-hide-on-mobile">{{ $auth_user->fullname() }}</span>
			<i class="fa fa-angle-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-default">
				<li>
					<a href="{{ route('admin.'.($auth_user->isSuper()?'super':($auth_user->isFinancial() ? 'financial' : 'ticket')).'.account') }}">
					<i class="icon-user"></i> My Account </a>
				</li>
				<li>
					<a href="{{ route('admin.user.logout') }}">
					<i class="fa fa-sign-out"></i> Log Out </a>
				</li>
			</ul>
		</li>
		<!-- END USER LOGIN DROPDOWN -->
	</ul>
</div>
<!-- END TOP NAVIGATION MENU -->
