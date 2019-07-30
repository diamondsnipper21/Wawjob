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
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/amcharts.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/serial.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/pie.js') }}"></script>
@endsection

@section('content')

<div class="main-content">
    <!-- BEGIN OVERVIEW STATS -->
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue-madison">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="number">
                        {{ User::getCountByRoles([User::ROLE_USER_BUYER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED]) }}
                        ({{ User::getCountByRoles([User::ROLE_USER_BUYER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED, User::STATUS_SUSPENDED, User::STATUS_FINANCIAL_SUSPENDED]) }})</div>
                    <div class="desc">Buyers(Active)</div>
                </div>
                <a class="more" href="{{ route('admin.super.users.list', ['role' => User::ROLE_USER_BUYER]) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat red-intense">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                         {{ User::getCountByRoles([User::ROLE_USER_FREELANCER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED]) }}
                        ({{ User::getCountByRoles([User::ROLE_USER_FREELANCER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED, User::STATUS_SUSPENDED, User::STATUS_FINANCIAL_SUSPENDED]) }})
                    </div>
                    <div class="desc">Freelancers(Active)</div>
                </div>
                <a class="more" href="{{ route('admin.super.users.list', ['role' => User::ROLE_USER_FREELANCER]) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat green-haze">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="number">
                        {{ User::getCountByRoles(null, null, [User::STATUS_SUSPENDED, User::STATUS_FINANCIAL_SUSPENDED]) }}
                    </div>
                    <div class="desc">Suspended Users</div>
                </div>
                <a class="more" href="{{ route('admin.super.users.list', ['filter' => ['status' => User::STATUS_SUSPENDED]]) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat purple-plum">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number">{{ User::onlyTrashed()->count() }}</div>
                    <div class="desc">Deleted Users</div>
                </div>
                <a class="more" href="javascript:;" style="visibility: hidden">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
    </div>
    <!-- END OVERVIEW STATS -->

    <!-- BEGIN User Stats -->
    <div id="overview_stats" class="portlet light stats-widget">
        <script type="text/javascript">
            var statStartDate = '{{ $start_date }}';
            var statEndDate = '{{ $end_date }}';
        </script>
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">Users</span>
            </div>
            <div class="actions row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3 lifetime-label">Lifetime</label>
                        <div class="col-md-9">
                            <input type="checkbox" id="lifetime" name="lifetime" {{ $lifetime?'checked':'' }} />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="stats-range" class="chart-daterange pull-right tooltips btn btn-fit-height grey-salt {{ $lifetime?'disabled':'' }}" data-placement="left" data-original-title="Change dashboard date range">
                        <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-8">
                    <script type="text/javascript">
                        var lineGraphData = @json($stats['line']);
                    </script>
                    <div id="line_chart" class="chart" style="height: 400px;"></div>
                </div>
                <div class="col-md-4">
                    <script type="text/javascript">
                        var pieGraphData = @json($stats['pie']);
                    </script>
                    <div id="pie_chart" class="chart" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- END User Stats -->

    <!-- Begin Locations -->
    <div class="row">
        <div class="col-md-12">
            <div id="stat_region_users" class="portlet light">
                <script type="text/javascript">
                    var statRegionUsers = @json($stat_region_users);
                </script>
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-share font-red-sunglo"></i>
                        <span class="caption-subject font-red-sunglo bold">Regional Stats</span>
                    </div>
                    <div class="actions">
                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="region_statistics_content">
                        <div class="btn-toolbar margin-bottom-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <select id="user_role" class="select2">
                                            <option value="">All Users</option>
                                            <option value="{{ User::ROLE_USER_BUYER }}" {{ old('user_role') == User::ROLE_USER_BUYER?'selected':'' }}>Buyers</option>
                                            <option value="{{ User::ROLE_USER_FREELANCER }}" {{ old('user_role') == User::ROLE_USER_FREELANCER?'selected':'' }}>Freelancers</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="vmap_world" class="vmaps"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Locations -->
</div>

@endsection