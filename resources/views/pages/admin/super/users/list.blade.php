<?php
/**
* User Overview Page on Super Admin
*
* @author KCG
* @since July 7, 2017
* @version 1.0
*/

use iJobDesk\Models\User;

?>
@extends('layouts/admin/super')

@section('additional-js')
@endsection

@section('content')

<div id="user_list">
    <form method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="_action" value="" />
        <textarea name="_reason" class="hide"></textarea>

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-green-sharp hide"></i>
                    <span class="caption-helper"><span class="caption-subject font-green-sharp bold"><i class="icon-user"></i>&nbsp;&nbsp;{{ $login_blocked ? 'Login Blocked' : 'Users' }}</span></span>
                </div>
                <div class="actions">
                    <strong>View</strong>&nbsp;
                    <select id="select_user_role" name="role" class="select2" data-width="200" data-url="{{ route('admin.super.users.list') }}">
                        <option value="">All Users</option>
                        <option value="buyers" {{ $role == User::ROLE_USER_BUYER?'selected':'' }}>Buyers</option>
                        <option value="freelancers" {{ $role == User::ROLE_USER_FREELANCER?'selected':'' }}>Freelancers</option>
                    </select>
                </div>
            </div>
            <div class="portlet-body">
                {{ show_messages() }}
                <div class="row margin-bottom-10">
                    <div class="col-md-12 margin-top-10">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($users) }}</div>
                    </div>
                </div>
                <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
                    <div class="col-md-6">
                        <div class="toolbar toolbar-table pull-right">
                            <span><strong>Action</strong>&nbsp;</span>
                            <select id="select_action" class="table-group-action-input form-control input-inline select2 select-change-status" data-auto-submit="false" data-width="170">
                                <option value="">Select...</option>
                                <option value="{{ User::STATUS_REQUIRE_ID_VERIFIED }}">Require ID Verification</option>
                                <option value="{{ User::STATUS_SUSPENDED }}">Suspend Account</option>
                                <option value="{{ User::STATUS_FINANCIAL_SUSPENDED }}">Suspend Financial</option>
                                <option value="{{ User::STATUS_AVAILABLE }}">Activate</option>
                                <option value="{{ User::STATUS_LOGIN_ENABLED }}">Enable Login</option>
                                <option value="{{ User::STATUS_DELETED }}">Delete</option>
                            </select>
                            <button id="button_action" class="btn btn-sm yellow table-group-action-submit button-change-status" type="submit" disabled><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </div>
                <div class="table-container">
                @if ($role == User::ROLE_USER_BUYER)
                    @include('pages.admin.super.users.list.buyers')
                @elseif ($role == User::ROLE_USER_FREELANCER)
                    @include('pages.admin.super.users.list.freelancers')
                @else
                    @include('pages.admin.super.users.list.both')
                @endif
                </div>

                <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($users) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $users->render() !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection