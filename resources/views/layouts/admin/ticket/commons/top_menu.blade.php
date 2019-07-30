<!-- BEGIN HEADER MENU -->
<div class="page-header-menu">
	<div class="container-fluid">
		<!-- BEGIN MEGA MENU -->
		<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
		<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
		<div class="hor-menu">
			<ul class="nav navbar-nav">
				<li class="{{ $page == 'ticket.dashboard' ? ' active' : '' }}">
					<a href="{{ route('admin.ticket.dashboard') }}">Dashboard</a>
				</li>
				<li class="{{ strpos($page, 'ticket.ticket') !== FALSE ? ' active' : '' }}">
					<a href="{{ route('admin.ticket.ticket.list') }}">
						Tickets
						@if ($count_of_ticket_with_new_msg > 0)
							&nbsp;&nbsp;&nbsp;
							<span class="badge badge-default">
								{{ $count_of_ticket_with_new_msg }}
							</span>
						@endif
					</a>
				</li>
				<li class="{{ strpos($page, 'ticket.todo') !== FALSE ? ' active' : '' }}">
					<a href="{{ route('admin.ticket.todo.list') }}">ToDos</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END HEADER MENU -->