<?php
/**
* Todo Listing Page on Ticket Manager
*
* @author KCG
* @since June 30, 2017
* @version 1.0
*/

use iJobDesk\Models\Todo;

?>
@extends('layouts/admin/' . $role_id)

@section('content')

<script type="text/javascript">
    var admins = @json($admins);
</script>

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-cogs font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">TODOs</span>
        </div>
        <div class="tools">
            <button class="btn green" data-toggle="modal" data-target="#modal_todo">Add New <i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="portlet-body">
        <div class="tabbable-custom">
            <ul class="nav nav-tabs">
                <li class="{{ $tab == 'opening'?'active':'' }}">
                    <a href="{{ route('admin.' . $role_id . '.todo.list') }}" aria-expanded="true">Opening</a>
                </li>
                <li class="{{ $tab == 'archived'?'active':'' }}">
                    <a href="{{ route('admin.' . $role_id . '.todo.list', ['tab' => 'archived']) }}" aria-expanded="false">Archived</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active">
                    @include('pages.admin.ticket.todo.listing')
                </div>
            </div>
        </div>
    </div>
</div>

@endsection