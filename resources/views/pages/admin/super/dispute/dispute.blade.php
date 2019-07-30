<?php
/**
* Dispute Page on Super Admin
*
* @author KCG
* @since Jan 09, 2018
* @version 2.0
*/
use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Ticket;

$statusList = Ticket::getOptions('status');
?>
@extends('layouts/admin/super')

@section('additional-js')
<!-- <script src="{{ url('assets/scripts/admin/pages/super/job/jobs.js') }}"></script> -->
@endsection

@section('content')

<div id="disputes" class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <div class="pull-left">
                <i class="fa fa-weixin font-green-sharp"></i>
                <span class="caption-subject font-green-sharp bold">Disputes</span>
            </div>
            <div class="pull-left">
                <span class="number">
                    <strong>{{ $opened_disputes }}</strong> Disputes in Queue now<br />
                    @if ($unassigned_disputes != 0)
                    <span><strong>{{ $unassigned_disputes }}</strong> Unassigned Disputes</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <form id="dispute" action="{{ route('admin.super.disputes') }}" method="post" class="form-datatable">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="ticket_id" class="ticket-id" value="" />
            <input type="hidden" name="action" class="action" value="" />
            <input type="hidden" name="comment" class="comment" value="" />

            {{ show_messages() }}

            <div class="row margin-bottom-10 render">
                <div class="col-md-12 margin-top-10">
                    <div role="status" aria-live="polite">{{ render_admin_paginator_desc($disputes) }}</div>
                </div>
            </div>
            <div class="row margin-bottom-10">
                <div class="col-md-6 margin-top-10">
                    <a href="#" class="clear-filter">Clear filters</a>
                </div>
                <div class="col-md-6">
                    <!-- <div class="toolbar pull-right">
                        <div class="view">View :</div>
                        <select id="result" name="filter[result]" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-result">
                            <option value="">Select...</option>
                            <option value="active" {{ old('filter.result') == 'active' ?'selected':'' }}>Active</option>
                            <option value="archived" {{ old('filter.result') == 'archived' ?'selected':'' }}>Archived</option>
                        </select>
                    </div>
                    <div class="clear"></div> -->
                </div>
            </div>

            <div class="table-container">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr role="row" class="heading">
                            <th width="8%"  class="sorting{{ $sort == 'tickets.id'?$sort_dir:'' }}"           data-sort="tickets.id">Ticket #</th>
                            <th             class="sorting{{ $sort == 'title'?$sort_dir:'' }}"                data-sort="title">Contract</th>
                            <th width="10%" class="sorting{{ $sort == 'buyer'?$sort_dir:'' }}"                data-sort="buyer">Buyer</th>
                            <th width="10%" class="sorting{{ $sort == 'contractor'?$sort_dir:'' }}"           data-sort="contractor">Freelancer</th>
                            <th width="10%" class="sorting{{ $sort == 'creator'?$sort_dir:'' }}"              data-sort="creator">Initiator</th>
                            <th width="12%" class="sorting{{ $sort == 'tickets.created_at'?$sort_dir:'' }}"   data-sort="tickets.created_at">Date Posted</th>
                            <th width="11%"  class="sorting{{ $sort == 'tickets.status'?$sort_dir:'' }}"      data-sort="tickets.status">Status</th>
                            <th width="13%">Actions</th>
                        </tr>
                        <tr role="row" class="filter">
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" />
                            </th>
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[title]" value="{{ old('filter.title') }}" placeholder="#ID or Title" />
                            </th>
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[buyer]" value="{{ old('filter.buyer') }}" placeholder="#ID or Name" />
                            </th>
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[freelancer]" value="{{ old('filter.freelancer') }}" placeholder="#ID or Name" />
                            </th>
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[creator]" value="{{ old('filter.creator') }}" placeholder="#ID or Name" />
                            </th>
                            <th>
                                <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="filter[date_posted][from]" placeholder="From" value="{{ old('filter.date_posted.from') }}" data-value="{{ old('filter.date_posted.from') }}" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                                <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="filter[date_posted][to]" placeholder="To" value="{{ old('filter.date_posted.to') }}" data-value="{{ old('filter.date_posted.to') }}" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </th>
                            <th>
                                <select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
                                    <option value="">Select...</option>
                                    @foreach ($statusList as $label => $status)
                                    <option value="{{ $status }}" {{ "$status" == old('filter.status')?'selected':'' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($disputes as $key => $dispute)
                        <tr class="odd gradeX" data-id="{{ $dispute->id }}">
                            <td align="center"><a href="{{ route('admin.super.ticket.detail', ['id' => $dispute->id]) }}">{{ $dispute->id }}</a><!-- managed by {{ $dispute->assigner_id }}--></td>
                            <td data-contractid="{{$dispute->contract ? $dispute->contract->id : ''}}"><a href="{{ route('admin.super.contract', ['id' => ($dispute->contract ? $dispute->contract->id : '')]) }}">   {{ $dispute->title }}</a></td>
                            <td><a href="{{ route('admin.super.user.overview', ['user_id' => ($dispute->contract ? $dispute->contract->buyer_id : '')]) }}">{!! $dispute->contract->buyer->fullname(true) !!}</a></td>
                            <td><a href="{{ route('admin.super.user.overview', ['user_id' => ($dispute->contract ? $dispute->contract->contractor_id : '')]) }}">{!! $dispute->contract->contractor->fullname(true) !!}</a></td>
                            <td><a href="{{ route('admin.super.user.overview', ['user_id' => $dispute->user->id]) }}">{!! $dispute->user->fullname(true) !!}</a></td>
                            <td align="center">{{ format_date('Y-m-d H:i:s', $dispute->created_at) }}</td>
                            <td align="center"><span class="label label-{{ strtolower(array_search($dispute->status, $statusList)) }}">{{ array_search($dispute->status, $statusList) }}</span></td>
                            <td align="center">
                                @if (!($dispute->status == Ticket::STATUS_SOLVED || $dispute->status == Ticket::STATUS_CLOSED))
                                <a class="btn blue btn-link action-link button-determine" data-url="{{ route('admin.super.dispute.determine', ['id' => $dispute->id]) }}">Determine</a><br />
                                @endif

                                @if ($dispute->status == Ticket::STATUS_SOLVED)
                                <a href="#dispute_result_{{ $dispute->id }}" class="action-link view-link" data-toggle="modal">View</a>
                                @include('pages.admin.super.dispute.view')
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="odd gradeX">
                            <td colspan="8" align="center">No Disputes</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="row render">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($disputes) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $disputes->render() !!}</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Modal -->
    <div id="modal_determine_container"></div>
</div>
@endsection