<?php

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
        <input type="hidden" name="_reason" value="" />

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-green-sharp hide"></i>
                    <span class="caption-helper"><span class="caption-subject font-green-sharp bold"><i class="icon-user"></i>&nbsp;&nbsp;Send email to users</span></span>
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
                        <div class="toolbar pull-right">
                            <span><strong>Action</strong>&nbsp;</span>
                            <select id="select_action" class="table-group-action-input form-control input-inline select2 select-change-status" data-auto-submit="false" data-width="170">
                                <option value="">Select...</option>
                                <option value="selected">To Selected</option>
                                <option value="freelancers">To Freelancers</option>
                                <option value="buyers">To Buyers</option>
                                <option value="all">To All</option>
                            </select>
                            <button id="button_action" class="btn btn-sm yellow table-group-action-submit button-change-status" type="submit" disabled><i class="fa fa-check"></i> Send Email</button>
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    @include('pages.admin.super.users.list.both')
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