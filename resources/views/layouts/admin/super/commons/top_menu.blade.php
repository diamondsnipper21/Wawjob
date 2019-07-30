<?php
use iJobDesk\Models\User;
?>
<!-- BEGIN HEADER MENU -->
<div class="page-header-menu">
	<div class="container-fluid">
		<div class="hor-menu">
			<ul class="nav navbar-nav">
				<li class="{{ strpos($page, 'super.dashboard') !== FALSE?'active':'' }}">
					<a href="{{ route('admin.super.dashboard') }}">Dashboard</a>
				</li>
				<li class="menu-dropdown mega-menu-dropdown {{ strpos($page, 'super.user') !== FALSE?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Users&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, 'super.users.dashboard') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.users.dashboard') }}">Overview</a></li>
						<li class="{{ strpos($page, 'super.users.list') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.users.list') }}">Users</a></li>
					</ul>
				</li>
				<li class="{{ strpos($page, 'super.proposal') !== FALSE || strpos($page, 'super.job') !== FALSE?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Job Postings&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, 'super.job') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.job.jobs') }}">Job Postings</a></li>
						<li class="{{ strpos($page, 'super.proposal') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.proposals') }}">Proposals</a></li>
					</ul>
				</li>

				<li class="{{ strpos($page, 'super.contract') !== FALSE || strpos($page, 'super.dispute') !== FALSE?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Contracts&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, 'super.contract') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.contracts') }}">Contracts</a></li>
						<li class="{{ strpos($page, 'super.dispute') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.disputes') }}">Dispute</a></li>
					</ul>
				</li>

				<li class="{{ strpos($page, '.payment.') !== FALSE?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Payments&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, '.payment.overview') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.payment.overview') }}">Overview</a></li>
						<li class="{{ strpos($page, '.payment.escrow') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.payment.escrows') }}">Escrow</a></li>
						<li class="{{ strpos($page, '.payment.deposit') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.payment.deposit') }}">Deposits</a></li>
						<li class="{{ strpos($page, '.payment.withdraw') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.payment.withdraw') }}">Withdrawals</a></li>
						<li class="{{ strpos($page, '.payment.transaction') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.payment.transactions') }}">Transactions</a></li>
						<li class="li-site-withdraws {{ strpos($page, '.payment.site_withdraw') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.payment.site_withdraws') }}">iJobDesk Withdrawals</a></li>
					</ul>
				</li>

				<li class="{{ strpos($page, '.affiliates.') !== FALSE?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Affiliates&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, 'super.affiliates.overview') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.affiliates.overview') }}">Overview</a></li>
						<li class="{{ strpos($page, 'super.affiliates.affiliates') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.affiliates.users') }}">Commission History</a></li>
					</ul>
				</li>

				<li class="{{ strpos($page, 'ticket.todo') !== FALSE?'active':'' }}">
					<a href="{{ route('admin.super.todo.list') }}">TODOs</a>
				</li>

				<li class="{{ strpos($page, 'ticket.ticket') !== FALSE?'active':'' }}">
					<a href="{{ route('admin.super.ticket.list') }}">
						Tickets
						@if ($count_of_ticket_with_new_msg > 0)
							&nbsp;
							<span class="badge badge-default">
								{{ $count_of_ticket_with_new_msg }}
							</span>
						@endif
					</a>
				</li>

				<li class="{{ strpos($page, 'super.stats') !== FALSE || strpos($page, 'super.admin_users') !== FALSE || strpos($page, 'super.cronjobs') !== FALSE || strpos($page, '.settings.help_page') !== FALSE || strpos($page, '.settings.static_page') !== FALSE ?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Manage&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, 'super.admin_users') !== FALSE?'active':'' }}">
							<a href="{{ route('admin.super.admin_users.list') }}">Administrators</a>
						</li>
						<li class="{{ strpos($page, '.settings.send_email') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.send_email') }}">Send Email</a></li>
						<li class="{{ strpos($page, '.settings.static_page') !== FALSE?'active':'' }}" ><a href="{{ route('admin.super.settings.static_pages') }}">Static Pages</a></li>
						<li class="{{ strpos($page, '.settings.help_page') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.help_pages') }}">Help Pages</a></li>
						<li class="{{ strpos($page, 'super.cronjobs') !== FALSE?'active':'' }} li-site-withdraws"><a href="{{ route('admin.super.cronjobs') }}">Cronjobs</a></li>
					</ul>
				</li>

				<li class="{{ strpos($page, '.settings.') !== FALSE && strpos($page, '.settings.help_page') === FALSE && strpos($page, '.settings.static_page') === FALSE ?'active':'' }}">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle">Settings&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, '.settings.payment_method') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.payment_method') }}">Payment Methods</a></li>
						<li class="{{ strpos($page, '.settings.fee') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.fees') }}">Fees and Charges</a></li>
						<li class="{{ strpos($page, '.settings.user_points') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.user_points') }}">Freelancer Rankings</a></li>
						
						<li class="{{ strpos($page, '.settings.email_template') !== FALSE?'active':'' }} li-site-withdraws"><a href="{{ route('admin.super.settings.email_templates') }}">Email Templates</a></li>
						<li class="{{ strpos($page, '.settings.notifications') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.notifications') }}">Notification Templates</a></li>
						<li class="{{ strpos($page, '.settings.job_cat') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.job_categories') }}">Job Categories</a></li>
						<li class="{{ strpos($page, '.settings.skill') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.skills') }}">Skills</a></li>
						<li class="{{ strpos($page, '.settings.countries') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.settings.countries') }}">Countries</a></li>
						<!-- <li class=""><a href="{{ route('admin.super.settings.faqs') }}">FAQs</a></li> -->						
					</ul>
				</li>

				<li class="menu-dropdown mega-menu-dropdown {{ strpos($page, 'super.tools') !== FALSE?'active':'' }} pull-right">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"><i class="icon-wrench"></i>&nbsp;Tools&nbsp;<i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu pull-left">
						<li class="{{ strpos($page, 'super.tools.log_viewer') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.tools.log_viewer') }}">Log Viewer</a></li>
						<li class="{{ strpos($page, 'super.tools.backup') !== FALSE?'active':'' }}"><a href="{{ route('admin.super.tools.backup') }}">Backup</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END HEADER MENU