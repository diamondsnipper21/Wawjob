<ul class="nav nav-tabs nav-job-detail">
    <!-- Overview -->
    <li class="{{ strpos($page, 'job.overview') !== FALSE?'active':'' }}">
        @if (!empty($user_id))
            <a href="{{ route('admin.super.user.buyer.job.overview', ['user_id' => $user_id, 'job_id' => $job->id]) }}">Overview</a>
        @else
            <a href="{{ route('admin.super.job.overview', ['id' => $job->id]) }}">Overview</a>
        @endif
    </li>

    <!-- Invitations -->
    <li class="{{ strpos($page, 'job.invitation') !== FALSE?'active':'' }}">
        @if (!empty($user_id))
            <a href="{{ route('admin.super.user.buyer.job.invitation', ['user_id' => $user_id, 'job_id' => $job->id]) }}">Invitations({{ $job->totalInvitationsCount() }})</a>
        @else
            <a href="{{ route('admin.super.job.invitation', ['id' => $job->id]) }}">Invitations({{ $job->totalInvitationsCount() }})</a>
        @endif
    </li>

    <!-- DON'T USE THIS PART -->
    <li class="{{ strpos($page, 'job.proposal') !== FALSE?'active':'' }} hide">
        @if (!empty($user_id))
            <a href="{{ route('admin.super.user.buyer.job.proposal', ['user_id' => $user_id, 'job_id' => $job->id]) }}">Proposals({{ $job->totalProposalsCount() }})</a>
        @else
            <a href="{{ route('admin.super.job.proposal', ['id' => $job->id]) }}">Proposals({{ $job->totalProposalsCount() }})</a>
        @endif
    </li>

    <!-- Interview -->
    <li class="{{ strpos($page, 'job.interview') !== FALSE?'active':'' }}">
        @if (!empty($user_id))
            <a href="{{ route('admin.super.user.buyer.job.interview', ['user_id' => $user_id, 'job_id' => $job->id]) }}">Interviews({{ $job->totalProposalsCount() }})</a>
        @else
            <a href="{{ route('admin.super.job.interview', ['id' => $job->id]) }}">Interviews({{ $job->totalProposalsCount() }})</a>
        @endif
    </li>

    <!-- Hire / Offers -->
    <li class="{{ strpos($page, 'job.hire_offers') !== FALSE?'active':'' }}">
        @if (!empty($user_id))
            <a href="{{ route('admin.super.user.buyer.job.hire_offers', ['user_id' => $user_id, 'job_id' => $job->id]) }}">Hire({{ $job->hiredContractsCount() }}) / Offers({{ $job->offerHiredContractsCount() }})</a>
        @else
            <a href="{{ route('admin.super.job.hire_offers', ['id' => $job->id]) }}">Hire({{ $job->hiredContractsCount() }}) / Offers({{ $job->offerHiredContractsCount() }})</a>
        @endif
    </li>

    <!-- Action History -->
    <li class="{{ strpos($page, 'job.action_history') !== FALSE?'active':'' }}">
        @if (!empty($user_id))
            <a href="{{ route('admin.super.user.buyer.job.action_history', ['user_id' => $user_id, 'job_id' => $job->id]) }}">Action History</a>
        @else
            <a href="{{ route('admin.super.job.action_history', ['id' => $job->id]) }}">Action History</a>
        @endif
    </li>
</ul>