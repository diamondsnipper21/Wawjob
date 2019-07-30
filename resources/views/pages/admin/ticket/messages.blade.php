<?php
/**
* Messages Page
*
* @author KCG
* @since Dec 30, 2017
* @version 1.0
*/
use iJobDesk\Models\AdminMessage;

?>
@extends('layouts/admin/'.($auth_user->isTicket()?'ticket':'super'))

@section('content')

<div id="messages" class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-list font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">Messages</span>
            <span class="caption-helper"></span>
        </div>
        @if ($current_user->isSuper())
        <div class="tools">
            <a href="#modal_create_thread" data-toggle="modal" class="btn green">Send Message <i class="fa fa-plus"></i></a>
        </div>
        @endif
    </div><!-- .portlet-title -->
    <div class="portlet-body">
        <div class="table-container">
            <form id="messages_list_form" action="{{ route('admin.'.($auth_user->isSuper()?'super':'ticket').'.messages') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="_action" value="" />

                {{ show_messages() }}

                <div class="row margin-bottom-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($messages) }}</div>
                    </div>
                </div>

                <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
                    <div class="col-md-6">
                        <div class="toolbar toolbar-table pull-right">
                            <button class="btn btn-sm red table-group-action-submit button-submit button-delete" type="button"><i class="fa fa-trash"></i> Delete</button>
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%"><input type="checkbox" class="group-checkable" /></th>
                                <!-- <th width="10%" class="sorting{{ $sort == 'sender.fullname'?$sort_dir:'' }}"            data-sort="sender.fullname">Sender</th> -->
                                <th                                                                         >Message</th>
                                <!-- <th width="10%" class="sorting{{ $sort == 'admin_messages.message_type'?$sort_dir:'' }}"        data-sort="admin_messages.message_type">Type</th> -->
                                <!-- <th width="10%" class="sorting{{ $sort == 'is_new'?$sort_dir:'' }}"        data-sort="is_new">New</th> -->
                                <th width="15%" class="sorting{{ $sort == 'admin_messages.created_at'?$sort_dir:'' }}"  data-sort="admin_messages.created_at">Arrived At</th>
                                <!-- <th width="5%">Actions</th> -->
                            </tr>
                            <tr role="row" class="filter">
                                <th>&nbsp;</th>
                                <!-- Sender -->
                                <!-- <th>
                                    <input type="text" class="form-control form-filter input-sm" name="filter[sender]" value="{{ old('filter.sender') }}" placeholder="#ID or Name" />
                                </th> -->
                                <!-- Message -->
                                <th>
                                    <input type="text" class="form-control form-filter input-sm" name="filter[message]" value="{{ old('filter.message') }}" />
                                </th>
                                <!-- Type -->
                                <!-- <th>
                                    <select name="filter[type]" class="form-control form-filter input-sm select2">
                                        <option value="">Select...</option>
                                        @foreach (AdminMessage::getOptions('type') as $label => $key)
                                        <option value="{{ $key }}" {{ "$key" == old('filter.type')?'selected':'' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th> -->
                                <!-- Is New -->
                                <!-- <th>
                                    <select name="filter[is_new]" class="form-control form-filter input-sm select2">
                                        <option value="">Select...</option>
                                        <option value="1" {{ "1" == old('filter.is_new')?'selected':'' }}>New</option>
                                        <option value="0" {{ "0" == old('filter.is_new')?'selected':'' }}>Read</option>
                                    </select>
                                </th> -->
                                <!-- Created At -->
                                <th>
                                    <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][from]" placeholder="From" value="{{ old('filter.created_at.from') }}" data-value="{{ old('filter.created_at.from') }}" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][to]" placeholder="To" value="{{ old('filter.created_at.to') }}" data-value="{{ old('filter.created_at.to') }}" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </th>
                                <!-- Actions -->
                                <!-- <th>&nbsp;</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($messages as $index => $message)
                            <tr class="odd gradeX">
                                <td>
                                    <input type="checkbox" class="checkboxes" name="id[]" value="{{ $message->id }}" />
                                </td>
                                <td><a href="{{ $message->link() }}">{{ $message->message }}</a></td>
                                <td align="center">
                                    {{ format_date('Y-m-d H:i', $message->created_at) }}
                                </td>
                            </tr>
                        @empty
                            <tr class="odd gradeX">
                                <td colspan="3" align="center">No messages</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($messages) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $messages->render() !!}</div>
                    </div>
                </div>
            </form>

            <div class="clearfix"></div>

        </div><!-- .table-container -->
    </div><!-- .portlet-body -->
</div>

@include('pages.admin.ticket.messages.modal')

@endsection