<!-- BEGIN HEADER MENU -->
<div class="page-header-menu">
	<div class="container-fluid">
		<!-- BEGIN MEGA MENU -->
		<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
		<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
		<div class="hor-menu">
			<ul class="nav navbar-nav">
				<li class="{{ $page == 'super.payment.overview' ? ' active' : '' }}">
					<a href="{{ route('admin.financial.dashboard') }}">Dashboard</a>
				</li>
				<li class="{{ $page == 'super.payment.transactions' ? ' active' : '' }}">
					<a href="{{ route('admin.financial.transactions') }}">Transactions</a>
				</li>
				<li class="{{ $page == 'super.payment.escrows' ? ' active' : '' }}">
					<a href="{{ route('admin.financial.escrows') }}">Escrow</a>
				</li>
				<li class="{{ $page == 'super.payment.deposit' ? ' active' : '' }}">
					<a href="{{ route('admin.financial.deposit') }}">Deposits</a>
				</li>
				<li class="{{ $page == 'super.payment.withdraw' ? ' active' : '' }}">
					<a href="{{ route('admin.financial.withdraw') }}">Withdrawals</a>
				</li>
				<li class="{{ $page == 'super.payment.site_withdraws' ? ' active' : '' }}">
					<a href="{{ route('admin.financial.site_withdraws') }}">iJobDesk Withdrawals</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END HEADER MENU -->