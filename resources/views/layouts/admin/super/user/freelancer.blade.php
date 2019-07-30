<ul class="nav nav-tabs">
    <li class="{{ $page == 'super.user.commons.overview'?'active':'' }}">
        <a href="{{ route('admin.super.user.overview', ['user_id' => $user->id]) }}">Overview</a>
    </li>
    <li class="{{ strpos($page, 'freelancer.proposal') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.freelancer.proposals', ['user_id' => $user->id]) }}">Proposals</a>
    </li>
    <li class="{{ strpos($page, 'commons.contract') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.contracts', ['user_id' => $user->id]) }}">Contracts</a>
    </li>
    <li class="{{ strpos($page, 'super.user.commons.message') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.messages', ['user_id' => $user->id]) }}">Messages</a>
    </li>
    <li class="{{ in_array('user', explode('.', $page)) && strpos($page, 'transaction') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.transactions', ['user_id' => $user->id]) }}">Transactions</a>
    </li>
    <li class="{{ in_array('user', explode('.', $page)) && strpos($page, 'timesheet') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.timesheet', ['user_id' => $user->id]) }}">Timesheet</a>
    </li>
    <li class="{{ in_array('user', explode('.', $page)) && strpos($page, 'workdiary') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.workdiary', ['user_id' => $user->id]) }}">Workdiary</a>
    </li>
    <li class="{{ strpos($page, 'super.user.freelancer.profile') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.freelancer.profile', ['user_id' => $user->id]) }}">Profile</a>
    </li>
    <!-- <li class="{{ $page == 'super.user.connection'?'active':'' }}">
        <a href="{{ route('admin.super.user.overview', ['user_id' => $user->id]) }}">Connection<br />History</a>
    </li> -->
    <li class="{{ strpos($page, 'super.user.commons.notification_settings') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.notification_settings', ['user_id' => $user->id]) }}">Notification<br />Settings</a>
    </li>
    <li class="{{ strpos($page, 'super.user.affiliate') !==FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.affiliate', ['user_id' => $user->id]) }}">Affiliate</a>
    </li>
    <li class="{{ strpos($page, '.ticket') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.ticket.list', ['user_id' => $user->id, 'tab' => 'opening']) }}">Tickets</a>
    </li>
    <li class="{{ strpos($page, 'super.user.commons.action_history') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.action_history', ['user_id' => $user->id]) }}">Action<br />History</a>
    </li>
     <li class="{{ strpos($page, 'super.user.commons.access_history') !== FALSE?'active':'' }}">
        <a href="{{ route('admin.super.user.access_history', ['user_id' => $user->id]) }}">Access<br />History</a>
    </li>
</ul>