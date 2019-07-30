<?php
/**
* Dashboard Page on Ticket Manager
*
* @author KCG
* @since June 10, 2017
* @version 1.0
*/
use iJobDesk\Models\Ticket;

?>
@extends('layouts/admin/ticket')

@section('additional-js')
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/amcharts.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/serial.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/pie.js') }}"></script>
@endsection

@section('content')
<script type="text/javascript">
    // js variables for stats
    var statStartDate = '{{ $start_date }}';
    var statEndDate = '{{ $end_date }}';
</script>

<div class="main-content">

    <!-- BEGIN DASHBOARD STATS -->
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue-madison">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="number">{{ Ticket::openingCounts() }}</div>
                    <div class="desc">Opening Tickets</div>
                </div>
                <a class="more" href="{{ route('admin.ticket.ticket.list', ['tab' => 'opening']) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat red-intense">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">{{ Ticket::highCounts() }}</div>
                    <div class="desc">Critical Tickets</div>
                </div>
                <a class="more" href="{{ route('admin.ticket.ticket.list', ['tab' => 'opening', 'filter' => ['priority' => Ticket::PRIORITY_HIGH]]) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat green-haze">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="number">{{ Ticket::ownCounts() }}</div>
                    <div class="desc">My Tickets</div>
                </div>
                <a class="more" href="{{ route('admin.ticket.ticket.list') }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat purple-plum">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number">{{ Ticket::archivedCounts() }}</div>
                    <div class="desc">Archived</div>
                </div>
                <a class="more" href="{{ route('admin.ticket.ticket.list', ['tab' => 'archived']) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
    </div>
    <!-- END DASHBOARD STATS -->
    <div class="clearfix"></div>

    <div class="row">
        <!-- BEGIN Notifications for Ticket -->
        <div class="col-md-6 col-sm-6">
            @include('pages.admin.ticket.dashboard.notifications')
        </div>
        <!-- END Notifications for Ticket -->

        <!-- BEGIN My Tickets -->
        <div class="col-md-6 col-sm-6">
        @include('pages.admin.ticket.dashboard.my_tickets')
        <!-- END My Tickets -->
        </div>
    </div>

    <!-- BEGIN Stats -->
    <div class="portlet light stats-widget">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">Stats</span>
            </div>
            <div class="actions">
                <div id="stats-range" class="pull-right tooltips btn btn-fit-height grey-salt" data-placement="left" data-original-title="Change dashboard date range">
                    <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.ticket.dashboard.stats_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.ticket.dashboard.stats_pie_chart')
                </div>
            </div>
        </div>
    </div>
    <!-- END Stats -->
</div>
@endsection