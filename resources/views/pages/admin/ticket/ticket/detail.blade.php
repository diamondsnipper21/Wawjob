<?php
/**
* Ticket Detail Page on Ticket Manager
*
* @author KCG
* @since July 4, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\File;

?>
@extends('layouts/admin/' . $role_id . (!empty($user)?'/user':''))

@section('content')
<script type="text/javascript">
</script>

<div class="portlet light ticket-detail">
    <div class="portlet-title">
        <div>
            <div class="caption pull-left">
                <span class="caption-subject font-green-sharp bold">
                    <h3>Ticket - #{{ $ticket->id }} {{ $ticket->subject }}</h3>
                </span>
            </div>
            <a href="{{ (!empty($user)?route('admin.super.user.ticket.list', ['user_id' => $user->id]):($auth_user->isSuper()?route('admin.super.ticket.list'):route('admin.ticket.ticket.list'))) }}" class="back-list">&lt; Back to list</a>
        </div>
        <div class="notification-msg-panel">
            {{ show_messages() }}
        </div>
    </div><!-- .portlet-title -->
    <div class="portlet-body">
        <div class="portlet light">
            <div class="portlet-body">
                <input type="hidden" id="ticket_id" value="{{ $ticket->id }}" />
                <form id="form_detail" action="{{ route('admin.'.$role_id.(!empty($user)?'.user':'').'.ticket.detail', ['id' => $ticket->id, 'user_id' => !empty($user)?$user->id:null]) }}" class="form-horizontal" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="_action" value="" />
                    <div class="row padding-top-10 padding-bottom-10">
                        <div class="col-md-8 info-panel">
                            <div class="row padding-bottom-10">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 text-right">
                                            Type : 
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="label-color-icon label-{{ str_replace(' ', '-', strtolower(array_search($ticket->type, Ticket::getOptions('type')))) }}">
                                                <i class="fa {{ Ticket::iconByType($ticket->type) }}"></i>
                                            </div>
                                            <div class="label-text">{{ $ticket->toString('type', $ticket->type) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 text-right margin-top-5">
                                            Assign To : 
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="hidden" id="pre_assignee" value="{{ $ticket->admin_id }}" />
                                            <div class="select2-assignee-wrapper">
                                                <select id="assignee" name="assignee" class="form-control form-filter input-sm select2" data-rule-required="true" data-select2-show-users="1" {{ $ticket->status == Ticket::STATUS_SOLVED ? "disabled" : "" }}>
                                                    @if (!$ticket->admin_id)
                                                        <option value="0">Select...</option> 
                                                    @endif
                                                    @foreach ($ticket_managers as $admin)
                                                        <option data-role-css="{{ $admin['user']->role_css_class() }}" data-role-name="{{ $admin['user']->role_name() }}" data-role-short-name="{{ $admin['user']->role_short_name() }}" value="{{ $admin['id'] }}" {{ array_search($admin['id'], explode(',', $ticket->admin_id)) !== FALSE?'selected':'' }}>{{ $admin['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row padding-bottom-10">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 text-right margin-top-5">
                                            Priority : 
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="select2-priority-wrapper">
                                                <select name="priority" class="form-control form-filter input-sm select2" data-with-color="true" {{ $ticket->status == Ticket::STATUS_SOLVED ? "disabled" : "" }}>
                                                    @foreach (Ticket::getOptions('priority') as $name => $priority)
                                                        <option value="{{ $priority }}" {{ $priority == $ticket->priority ? 'selected' : '' }}>{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row padding-bottom-10">
                                        <div class="col-sm-4 text-right">
                                            Assigner : 
                                        </div>
                                        <div class="col-sm-8">
                                            <b>{!! $ticket->assigner ? $ticket->assigner->getUserNameWithIcon() : '-' !!}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row padding-bottom-10">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 text-right">
                                            Status : 
                                        </div>
                                        <div class="col-sm-8 ticket-status">
                                            <b>
                                                {{ Ticket::toString('status', $ticket->status) }}
                                                {!! $ticket->status == Ticket::STATUS_SOLVED?'<span>[' . Ticket::getOptions('result')[$ticket->archive_type] . ']</span>':'' !!}
                                            </b>
                                            @if ($ticket->status == Ticket::STATUS_SOLVED)
                                                <blockquote>{{ $ticket->closer_id == $ticket->user_id?sprintf('This ticket has been closed by the Owner (%s) on %s', $ticket->user->fullname(), format_date('M i, j \a\t H:i', $ticket->ended_at)):$ticket->reason }}</blockquote>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row padding-bottom-10">
                                        <div class="col-sm-4 text-right">
                                            Assigned At : 
                                        </div>
                                        <div class="col-sm-8">
                                            {{ format_date('M j, Y H:i:s', $ticket->assigned_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row padding-bottom-10">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 text-right">
                                            Created At : 
                                        </div>
                                        <div class="col-sm-8">
                                            {{ format_date('M j, Y g:i A', $ticket->created_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row padding-bottom-10">
                                        <div class="col-sm-4 text-right">
                                            Updated At : 
                                        </div>
                                        <div class="col-sm-8">
                                            {{ format_date('M j, Y g:i A', $ticket->updated_at) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>

                            <div class="row">
                                <div class="col-sm-2 text-right">
                                    Description : 
                                </div>
                                <div class="col-sm-10 ticket-description break">
                                    @if ($ticket->content)
                                        @if ($ticket->type == Ticket::TYPE_ID_VERIFICATION)
                                            {!! $ticket->content !!}
                                        @else
                                            {!! render_more_less_desc($ticket->content, 300) !!}
                                        @endif
                                    @else
                                        No Content
                                    @endif

                                    <div class="attachments">
                                        {!! render_files($ticket->files) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 content-panel">
                            <!-- Memo -->
                            <input type="hidden" name="_action" value="SAVE_MEMO" />
                            <div class="row margin-top-5">
                                <div class="col-sm-9">
                                    <textarea name="memo" class="form-control memo-control maxlength-handler" rows="5" maxlength="5000" rows="5" placeholder="Please leave memo here." data-rule-required="true">{{ $ticket->memo }}</textarea>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" class="btn blue pull-right btn-save-memo">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- .portlet-body -->
        </div><!-- .portlet -->
        <div class="row">
            <div class="col-md-3 ticket-info">
                <div class="portlet light">
                    <div class="portlet-body">
                        @if ($ticket->user_id)
                            <?php $user_icons = [];?>
                            <!-- Buyer | User -->
                            <div class="control-label">
                                <div class="assigner">
                                    @if ($ticket->contract_id)
                                        <img src="{{ avatar_url($ticket->contract->buyer) }}" class="img-circle" width="60" />
                                        <span class="assigner-name"><a target="_blank" style="text-decoration: none" href="{{ route('admin.super.user.overview', ['user_id' => $ticket->contract->buyer->id]) }}">{!! $ticket->contract->buyer->getUserNameWithIcon() !!}</a></span>
                                        @if ($ticket->contract->buyer_id == $ticket->user_id)
                                            <span class="initiator-label"> Initiator </span>
                                        @endif
                                    @else
                                        <img src="{{ avatar_url($ticket->user) }}" class="img-circle" width="60" />
                                        <span class="assigner-name"><a target="_blank" style="text-decoration: none" href="{{ route('admin.super.user.overview', ['user_id' => $ticket->user->id]) }}">{!! $ticket->user->getUserNameWithIcon() !!}</a></span>
                                        <span class="initiator-label"> Initiator </span>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <!-- Freelancer -->
                            <div class="control-label">
                                <div class="assigner">
                                    @if($ticket->contract_id)
                                        <img src="{{ avatar_url($ticket->contract->contractor) }}" class="img-circle" width="60" />
                                        <span class="assigner-name"><a target="_blank" style="text-decoration: none" href="{{ route('admin.super.user.overview', ['user_id' => $ticket->contract->contractor->id]) }}">{!! $ticket->contract->contractor->getUserNameWithIcon() !!}</a></span>
                                        @if ($ticket->contract->contractor_id == $ticket->user_id)
                                            <span class="initiator-label"> Initiator </span>
                                        @endif
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                        @if ($ticket->receiver_id)
                            <div class="control-label">
                                <div class="receiver">
                                    <img src="{{ avatar_url($ticket->receiver) }}" class="img-circle" width="60" />
                                    <span class="receiver-name">{!! $ticket->receiver->getUserNameWithIcon() !!}</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        @elseif ($ticket->admin_id)
                            <div class="control-label">
                                <div class="assigner">
                                    <img src="{{ avatar_url($ticket->admin) }}" class="img-circle" width="60" />
                                    <span class="assigner-name">{!! $ticket->admin->getUserNameWithIcon() !!}</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                    </div><!-- .portlet-body -->
                </div><!-- .portlet light -->
                <div class="portlet light">
                    <div class="portlet-body button-panel">
                        @if ($ticket->contract_id && ($auth_user->isSuper() || $ticket->admin_id == $auth_user->id))
                            <div class="text_center padding-bottom-30">
                                <a href="{{ route('admin.'.$role_id.'.contract', ['contract_id' => $ticket->contract_id]) }}" class="view-project-label">View Related Contract</a>
                            </div>
                        @endif

                        @if ($ticket->type != Ticket::TYPE_ID_VERIFICATION)
                        <div class="text_center padding-bottom-30">
                            <a href="{{ route('admin.'.$role_id.(!empty($user)?'.user':'').'.ticket.msg_admin', ['id' => $ticket->id, 'user_id' => !empty($user)?$user->id:null]) }}" class="btn blue btn-block btn-ticket {{ !$ticket->isAssigned()?'disabled':'' }}">
                                @if ($auth_user->isSuper())
                                    View Private Messages
                                @else
                                    Message to Super Admin
                                @endif

                                <?php $count = $ticket->getUnreadAdminMessages(); ?>
                                @if ($count)
                                    <span class="badge badge-unread-message">
                                        {{ $count }}
                                    </span>
                                @endif
                            </a>
                        </div>
                        @endif

                        @if (!$ticket->isClosed() && !($ticket->isDispute() && $auth_user->isTicket()))
                            <div class="text_center">
                                <button id="btn-solve" 
                                        @if ($ticket->type == Ticket::TYPE_DISPUTE)
                                        data-url="{{ route('admin.super.disputes', ['filter' => ['id' => $ticket->id]]) }}" 
                                        @endif
                                        class="btn blue button-solve btn-ticket btn-block button-archive" 
                                        data-toggle="modal" data-target="#modal_archive" 
                                        {{ $auth_user->isSuper() || ($ticket->admin_id == $auth_user->id) ? "" : "disabled" }}>
                                    @if ($ticket->type != Ticket::TYPE_ID_VERIFICATION)
                                        Solve
                                    @else
                                        Determine
                                    @endif
                                </button>
                            </div>
                        @endif
                    </div><!-- .portlet-body -->
                </div><!-- .portlet -->
            </div>
            <div class="col-md-9">
                <div id="ticket_comments" class="portlet light">
                    <div class="portlet-body">
                        <div class="form-group">
                            @include('pages.partials.messages', [
                                            'id' => $ticket->id, 
                                            'messages' => $messages, 
                                            'type' => $ticket->file_type(), 
                                            'class' => 'Ticket', 
                                            'can_send' => !$ticket->isClosed() && $ticket->isAssigned(), 
                                            'totals' => $message_count,
                                            'limit' => $message_limit
                            ])
                        </div><!-- .form-group -->
                    </div><!-- .portlet-body -->
                </div><!-- .portlet -->
            </div>
        </div>
    </div><!-- .portlet-body -->
</div><!-- .portlet -->
@include('pages.admin.ticket.ticket.solve_modal', ['ticket' => $ticket])
@endsection