<?php
/**
* Notification Page on Ticket Manager
*
* @author KCG
* @since June 23, 2017
* @version 1.0
*/
use iJobDesk\Models\Notification;
use iJobDesk\Models\UserNotification;

?>
@extends('layouts/admin/'.($auth_user->isTicket()?'ticket':'super'))

@section('content')

<div id="notifications" class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-list font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">Notifications</span>
            <span class="caption-helper"></span>
        </div>
    </div><!-- .portlet-title -->
    <div class="portlet-body">
        <div class="table-container">
            <form id="notifications_list_form" action="{{ route('admin.'.($auth_user->isSuper()?'super':'ticket').'.notifications') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="_action" />

                {{ show_messages() }}

                <div class="row margin-bottom-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($notifications) }}</div>
                    </div>
                </div>

                <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                    </div>
                    <div class="col-md-6">
                        <div class="toolbar toolbar-table pull-right">
                            <span><strong>Action</strong>&nbsp;</span>
                            <select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-status" data-auto-submit="false">
                                <option value="">Select...</option>
                                <option value="READ">Read</option>
                                <option value="UNREAD">Unread</option>
                                <option value="DELETE">Delete</option>
                            </select>
                            <button class="btn btn-sm yellow table-group-action-submit button-change-status" type="button" disabled><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%"><input type="checkbox" class="group-checkable" /></th>
                                <th width="10%" class="sorting{{ $sort == 'priority'?$sort_dir:'' }}"       data-sort="priority">Priority</th>
                                <th                                                                         >Content</th>
                                <th width="15%" class="sorting{{ $sort == 'notified_at'?$sort_dir:'' }}"    data-sort="notified_at">Notified At</th>
                                <th width="10%">Actions</th>
                            </tr>
                            <tr role="row" class="filter hide">
                                <th>&nbsp;</th>
                                <!-- Priority -->
                                <th>
                                    <select name="filter[priority]" class="form-control form-filter input-sm select2">
                                        <option value="">Select...</option>
                                        @foreach (Notification::options('priority') as $label => $key)
                                        <option value="{{ $key }}" {{ "$key" == old('filter.priority')?'selected':'' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <!-- Message -->
                                <th>
                                    <input type="text" class="form-control form-filter input-sm" name="filter[message]" value="{{ old('filter.message') }}" />
                                </th>
                                <!-- Created At -->
                                <th>
                                    <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="filter[notified_at][from]" placeholder="From" value="{{ old('filter.notified_at.from') }}" data-value="{{ old('filter.notified_at.from') }}" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="filter[notified_at][to]" placeholder="To" value="{{ old('filter.notified_at.to') }}" data-value="{{ old('filter.notified_at.to') }}" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </th>
                                <!-- Actions -->
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($notifications as $index => $notification)
                            {{ parse_notification([$notification], App::getLocale()) }}
                            <tr class="odd gradeX {{ $notification->read_at == NULL?'unread':'' }}" data-read-url="{{ route('admin.'.($auth_user->isSuper()?'super':'ticket').'.notification.read', ['id' => $notification->id]) }}" data-nt-id="{{ $notification->id }}">
                                <td>
                                    <input type="checkbox" class="checkboxes" name="id[]" value="{{ $notification->id }}" data-status-DELETE="true" @if ($notification->read_at) data-status-UNREAD="true" @else data-status-READ="true" @endif />
                                </td>
                                <td align="center">
                                    <div class="label label-priority label-{{ strtolower(array_search($notification->ninfo->priority, Notification::options('priority'))) }}" data-toggle="tooltip" title="{{ array_search($notification->ninfo->priority, Notification::options('priority')) }}">
                                        <i class="fa {{ UserNotification::iconByPriority($notification->ninfo->priority) }}"></i>
                                    </div>
                                </td>
                                <td>{!! nl2br($notification->notification) !!}</td>
                                <td align="center">
                                    {{ format_date('Y-m-d H:i', $notification->notified_at) }}
                                </td>
                                <td align="center"><a href="#" class="delete-btn"><i class="fa fa-times"></i></a></td>
                            </tr>
                        @empty
                            <tr class="odd gradeX">
                                <td colspan="5" align="center">No Notifications</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($notifications) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $notifications->render() !!}</div>
                    </div>
                </div>
            </form>

            <div class="clearfix"></div>

        </div><!-- .table-container -->
    </div><!-- .portlet-body -->
</div>
@endsection