<?php
/**
* Job Postings Page on Super Admin
*
* @author PYH
* @since July 10, 2017
* @version 1.0
*/
use iJobDesk\Models\User;
use iJobDesk\Models\UserContact;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;

?>
@extends('layouts/admin/super'.(!empty($user)?'/user':''))

@section('content')

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-tasks font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">Job Postings</span>
        </div>
    </div>
    <div id="jobs_portlet_body" class="portlet-body">
        <form id="jobs_list" action="{{ empty($user)?route('admin.super.job.jobs'):route('admin.super.user.buyer.jobs', ['user_id' => $user->id]) }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="_action" value="" />

            {{ show_messages() }}

            <div class="row margin-bottom-10">
                <div class="col-md-12 margin-top-10">
                    <div role="status" aria-live="polite">{{ render_admin_paginator_desc($jobs) }}</div>
                </div>
            </div>
            <div class="row margin-bottom-10">
                <div class="col-md-6 margin-top-10">
                    <a href="#" class="clear-filter">Clear filters</a>
                </div>
                <div class="col-md-6">
                    <div class="toolbar toolbar-table pull-right">
                        <span><strong>Action</strong>&nbsp;</span>
                        <select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-status" data-auto-submit="false">
                            <option value="">Select...</option>
                            <option value="{{ Project::STATUS_OPEN }}" {{ old('status') == Project::STATUS_OPEN ? 'selected' : '' }}>Activate</option>
                            <option value="{{ Project::STATUS_SUSPENDED }}" {{ old('status') == Project::STATUS_SUSPENDED ? 'selected' : '' }}>Suspend</option>
                            <option value="{{ Project::STATUS_DELETED }}" {{ old('status') == Project::STATUS_DELETED ? 'selected' : '' }}>Delete</option>
                        </select>
                        <button class="btn btn-sm yellow table-group-action-submit button-change-status" disabled type="button"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr role="row" class="heading">
                            <th width="2%"><input type="checkbox" class="group-checkable" /></th>
                            <th width="5%"  class="sorting{{ $sort == 'id'?$sort_dir:'' }}"                         data-sort="id">ID #</th>
                            <th             class="sorting{{ $sort == 'subject'?$sort_dir:'' }}"                    data-sort="subject">Title</th>
                            <th width="8%"  class="sorting{{ $sort == 'type'?$sort_dir:'' }}"                       data-sort="type">Type</th>
                            @if (empty($user))
                            <th width="15%" class="sorting{{ $sort == 'fullname'?$sort_dir:'' }}"                   data-sort="fullname">Owner</th>
                            @endif
                            <th width="8%"  class="sorting{{ $sort == 'total_proposals'?$sort_dir:'' }}"            data-sort="total_proposals">Applicants</th>
                            <th width="8%"  class="sorting{{ $sort == 'total_interviews'?$sort_dir:'' }}"           data-sort="total_interviews">Interviews</th>
                            <th width="8%"  class="sorting{{ $sort == 'is_public'?$sort_dir:'' }}"                  data-sort="is_public">Visibility</th>
                            <th width="12%" class="sorting{{ $sort == 'projects.created_at'?$sort_dir:'' }}"        data-sort="projects.created_at">Date Posted</th>
                            @if (!empty($user))
                            <th width="12%" class="sorting{{ $sort == 'projects.updated_at'?$sort_dir:'' }}"        data-sort="projects.updated_at">Updated At</th>
                            @endif
                            <th width="12%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}"                     data-sort="status">Status</th>
                        </tr>
                        <tr role="row" class="filter">
                            <th>&nbsp;</th>
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="ID #" />
                            </th>
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[title]" value="{{ old('filter.title') }}" />
                            </th>
                            <th>
                                <select name="filter[type]" class="form-control form-filter input-sm select2">
                                    <option value="">Select...</option>
                                    <option value="{{ Project::TYPE_FIXED }}"  {{ old('filter.type') != '' && Project::TYPE_FIXED == old('filter.type')?'selected':'' }}>Fixed</option>
                                    <option value="{{ Project::TYPE_HOURLY }}" {{ Project::TYPE_HOURLY == old('filter.type')?'selected':'' }}>Hourly</option>
                                </select>
                            </th>
                            @if (empty($user))
                            <th>
                                <input type="text" class="form-control form-filter input-sm" name="filter[owner]" value="{{ old('filter.owner') }}" placeholder="#Owner ID or Name" />
                            </th>
                            @endif
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>
                                <select name="filter[visibility]" class="form-control form-filter input-sm select2">
                                    <option value="">Select...</option>
                                    <option value="{{ ucfirst(Project::STATUS_PRIVATE) }}" {{ old('filter.visibility') != '' && Project::STATUS_PRIVATE == old('filter.visibility')?'selected':'' }}>Private</option>
                                    <option value="{{ ucfirst(Project::STATUS_PROTECTED) }}"  {{ Project::STATUS_PROTECTED == old('filter.visibility')?'selected':'' }}>Protected</option>
                                    <option value="{{ ucfirst(Project::STATUS_PUBLIC) }}"  {{ Project::STATUS_PUBLIC == old('filter.visibility')?'selected':'' }}>Public</option>
                                </select>
                            </th>
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
                            @if (!empty($user))
                            <th>
                                <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="filter[updated_at][from]" placeholder="From" value="{{ old('filter.updated_at.from') }}" data-value="{{ old('filter.updated_at.from') }}" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                                <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="filter[updated_at][to]" placeholder="To" value="{{ old('filter.updated_at.to') }}" data-value="{{ old('filter.updated_at.to') }}" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </th>
                            @endif
                            <th>
                                <select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
                                    <option value="">Select...</option>
                                    <option value="{{ ucfirst(Project::STATUS_DRAFT) }}"     {{ Project::STATUS_DRAFT == old('filter.status')?'selected':'' }}>Draft</option>
                                    <option value="{{ ucfirst(Project::STATUS_OPEN) }}"      {{ Project::STATUS_OPEN == old('filter.status')?'selected':'' }}>Open</option>
                                    <option value="{{ ucfirst(Project::STATUS_SUSPENDED) }}" {{ Project::STATUS_SUSPENDED == old('filter.status')?'selected':'' }}>Suspended</option>
                                    <option value="{{ ucfirst(Project::STATUS_CLOSED) }}"    {{ old('filter.status') != '' && Project::STATUS_CLOSED == old('filter.status')?'selected':'' }}>Closed</option>
                                    <option value="{{ ucfirst(Project::STATUS_CANCELLED) }}" {{ Project::STATUS_CANCELLED == old('filter.status')?'selected':'' }}>Cancelled</option>
                                    <option value="{{ ucfirst(Project::STATUS_DELETED) }}"   {{ Project::STATUS_DELETED == old('filter.status')?'selected':'' }}>Deleted</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($jobs as $job)
                        <tr class="odd gradeX">
                            <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $job->id }}" {{ Project::enableStatusChanged($job) }} {{ $job->status == Project::STATUS_CANCELLED || $job->status == Project::STATUS_DELETED ? 'disabled' : '' }}/></td>
                            <td align="center">{{ $job->id }}</td>
                            <td class="text-align-left">
                                @if (!empty($user))
                                <a href="{{ route('admin.super.user.buyer.job.overview', ['user_id' => $user->id, 'job_id' => $job->id]) }}">{{ $job->subject }}</a>
                                @else
                                <a href="{{ route('admin.super.job.overview', ['id' => $job->id]) }}">{{ $job->subject }}</a>
                                @endif
                            </td>
                            <td align="center">{{ str_replace(' Price', '', $job->type_string()) }}</td>
                            @if (empty($user))
                            <td><a href="{{ route('admin.super.user.overview', ['user_id' => $job->client->id]) }}">{!! $job->user->fullname(true) !!}</a></td>
                            @endif
                            <td align="center">{{ $job->total_proposals }}</td>
                            <td align="center">{{ $job->messagedApplicationsCount() }}</td>
                            <td align="center">{{ ucfirst($job->visibility_string()) }}</td>
                            <td align="center">{{ format_date('Y-m-d H:i:s', $job->created_at) }}</td>
                            @if (!empty($user))
                            <td align="center">{{ format_date('Y-m-d H:i:s', $job->updated_at) }}</td>
                            @endif
                            <td align="center"><span class="label label-{{ strtolower($job->status_admin_string()) }}">{{ ucfirst($job->status_admin_string()) }}</span></td>
                        </tr>
                    @empty
                        <tr class="odd gradeX">
                            <td colspan="10" align="center">No Jobs</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($jobs) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $jobs->render() !!}</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection