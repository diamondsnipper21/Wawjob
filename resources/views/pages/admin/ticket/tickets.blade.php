<?php
/**
* Ticket Listing Page on Ticket Manager
*
* @author KCG
* @since June 30, 2017
* @version 1.0
*/

use iJobDesk\Models\Ticket;

?>
@extends('layouts/admin/' . $role_id . (!empty($user)?'/user':''))

@section('content')
<script type="text/javascript">
    var admins = @json($admins);
    var tab = '{{ $tab }}';
</script>
<div id="tickets">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <div class="pull-left">
                    <i class="fa fa-ticket font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold">Tickets</span>
                </div>
                @if (empty($user))
                <div class="pull-left">
                    <span class="number">
                        <strong>{{ $new_tickets }}</strong> New ticket(s)<br />
                        @if ($unassigned_tickets != 0)
                        <span><strong>{{ $unassigned_tickets }}</strong> Unassigned</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>
            
            <div class="tools">
            @if ($tab != 'archived')
                <button class="btn green button-assign-to" data-toggle="modal" data-target="#modal_assign" disabled>Assign To <i class="fa fa-user"></i></button>
            @endif

            @if ($user)
                <button class="btn blue button-create-ticket" data-toggle="modal" data-target="#modal_create">Create New Ticket <i class="fa fa-plus"></i></button>
            @endif
            </div>            
        </div>
        <div class="portlet-body">
            <div class="tabbable-custom">
                <ul class="nav nav-tabs">
                    <li class="{{ $tab == 'opening'?'active':'' }}">
                        @if(!empty($user))
                            <a href="{{ route('admin.' . $role_id. '.user.ticket.list', ['tab' => 'opening', 'user_id' => $user->id]) }}" aria-expanded="true">Opening({{ $opens_count }})</a>
                        @else
                            <a href="{{ route('admin.' . $role_id. '.ticket.list', ['tab' => 'opening']) }}" aria-expanded="true">Opening({{ $opens_count }})</a>
                        @endif
                    </li>
                    @if(empty($user))
                        <li class="{{ $tab == 'mine'?'active':'' }}">
                            <a href="{{ route('admin.'.$role_id.'.ticket.list', ['tab' => 'mine']) }}" aria-expanded="true">My Tickets({{ $mys_count }})</a>
                        </li>
                    @endif
                    <li class="{{ $tab == 'archived'?'active':'' }}">
                        @if(!empty($user))
                            <a href="{{ route('admin.' . $role_id. '.user.ticket.list', ['tab' => 'archived', 'user_id' => $user->id]) }}" aria-expanded="true">Archived({{ $archived_count }})</a>
                        @else
                            <a href="{{ route('admin.' . $role_id. '.ticket.list', ['tab' => 'archived']) }}" aria-expanded="true">Archived({{ $archived_count }})</a>
                        @endif
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active">
                        @include('pages.admin.ticket.ticket.listing')
                    </div>
                </div>
            </div>
        </div><!-- .portlet-body -->
    </div><!-- .portlet -->
</div><!-- #tickets -->
@endsection