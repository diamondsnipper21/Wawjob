<?php
/**
* Todo Detail Page on Ticket Manager
*
* @author KCG
* @since July 4, 2017
* @version 1.0
*/

use iJobDesk\Models\Todo;
use iJobDesk\Models\User;
use iJobDesk\Models\File;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\AdminMessage;

?>
@extends('layouts/admin/' . $role_id)

@section('content')

<div class="portlet light todo-detail">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-green-sharp bold">#{{ $todo->id }} - {{ $todo->subject }}{!! $todo->isOverdue()?'&nbsp;&nbsp;<span class="label label-danger">Overdue</span>':'' !!}</span>
        </div>
        <div class="actions">
            <a href="{{ $auth_user->isSuper()?route('admin.super.todo.list'):route('admin.ticket.todo.list') }}" class="back-list">&lt; Back to list</a>
        </div>
    </div>
    <div class="portlet-body messages">

        {{ show_messages() }}
        <div class="row">
            <div class="col-md-8">
                <blockquote class="break">
                    {!! render_more_less_desc($todo->description, 250) !!}
                </blockquote>
                
                <div class="todo-attachments">
                    {!! render_files($todo->files) !!}
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn blue pull-right" data-toggle="modal" data-target="#modal_todo"><i class="fa fa-pencil"></i> Edit</button>
            </div>
        </div>
        <div class="row margin-top-30">
            <div class="col-md-3 todo-info">
                <h3 class="form-section" style="margin-top:0px">TODO Details</h3>
                <hr />
                <div class="control-label">Type: <strong>{{ array_search($todo->type, Todo::options('type')) }}</strong></div>
                <div class="control-label">Priority: <strong class="label label-{{ strtolower(array_search($todo->priority, Todo::options('priority'))) }}">{{ array_search($todo->priority, Todo::options('priority')) }}</strong></div>
                @if (!empty($todo->related_ticket_id))
                <div class="control-label">Related Ticket: <a href="{{ route('admin.ticket.ticket.detail', ['id' => $todo->related_ticket_id ]) }}"><strong>#{{ $todo->related_ticket_id }}</strong></a></div>
                @endif
                <div class="control-label">Due Date: <strong class="{{ strtotime($todo->due_date) < time()?'overdue':'' }}">{{ format_date(null, $todo->due_date) }}</strong></div>
                <strong>Assigned to</strong>
                <div class="control-label">
                    @forelse ($todo->assigners as $user)
                    <div class="assigner">
                        <img src="{{ avatar_url($user) }}" class="img-circle" width="60" />
                        <span class="assigner-role">{!! $user->getUserNameWithIcon() !!}</span>
                    </div>
                    @empty
                    <div class="assigner no-body">
                        No Body
                    </div>
                    @endforelse
                    <div class="clearfix"></div>
                </div>
                <strong>Creator</strong>
                <div class="control-label">
                    <div class="assigner">
                        <img src="{{ avatar_url($todo->creator) }}" class="img-circle" width="60" />
                        <span class="assigner-role">{!! $todo->creator->getUserNameWithIcon() !!}</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="control-label">Created Date: <strong>{{ format_date('Y-m-d H:i', $todo->created_at) }}</strong></div>
                <div class="control-label">Status: <strong>{{ $todo->getStatus() }}</strong></div>
            </div>
            <div id="todo_messages" class="col-md-9">
                <h3 class="form-section" style="margin-top:0px">Messages</h3><hr />
                @include('pages.partials.messages', [
                                'id' => $todo->id, 
                                'messages' => $messages, 
                                'type' => File::TYPE_ADMIN_MESSAGE, 
                                'class' => 'Todo', 
                                'can_send' => !$todo->isClosed(), 
                                'totals' => $message_count,
                                'limit' => $message_limit
                ])
            </div>
        </div>
        @include('pages.admin.ticket.todo.edit_modal')
    </div>
</div>

@endsection