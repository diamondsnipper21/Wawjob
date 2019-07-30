<?php
/**
* Dashboard Page on Super Admin
*
* @author KCG
* @since June 24, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\SiteWallet;

?>
@extends('layouts/admin/super')

@section('additional-js')
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/amcharts.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/serial.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/pie.js') }}"></script>
@endsection

@section('content')

<div class="main-content">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue-madison">
        		<div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
    	        <div class="details">
    	        	<div class="row row1">
    	        		<div class="col-md-6">Buyers:</div>
    	        		<div class="col-md-6 value">
                            {{ User::getCountByRoles([User::ROLE_USER_BUYER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED, User::STATUS_SUSPENDED, User::STATUS_FINANCIAL_SUSPENDED]) }}
                        </div>
    	        	</div>
    	        	<div class="row row2">
    	        		<div class="col-md-6">Freelancers:</div>
    	        		<div class="col-md-6 value">{{ User::getCountByRoles([User::ROLE_USER_FREELANCER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED, User::STATUS_SUSPENDED, User::STATUS_FINANCIAL_SUSPENDED]) }}</div>
    	        	</div>
    	        	<div class="row row4">
    	        		<div class="col-md-6">Suspended Users:</div>
    	        		<div class="col-md-6 value">{{ User::getCountByRoles(null, null, [User::STATUS_SUSPENDED, User::STATUS_FINANCIAL_SUSPENDED]) }}</div>
    	        	</div>
                    <div class="row row3">
                        <div class="col-md-6">Deleted Users:</div>
                        <div class="col-md-6 value">{{ User::getCountByRoles([User::ROLE_USER_BUYER], [User::STATUS_NOT_AVAILABLE, User::STATUS_DELETED]) }}</div>
                    </div>
    	        </div>
                <a class="more" href="{{ route('admin.super.users.dashboard') }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat red-intense">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
    	        	<div class="row row1 job-postings">
    	        		<div class="col-md-6">Open Jobs:</div>
    	        		<div class="col-md-6 value">{{ Project::where('status', Project::STATUS_OPEN)->count() }}</div>
    	        	</div>
    	        	<div class="row row2 job-active-contracts">
    	        		<div class="col-md-6">Hourly Contract:</div>
    	        		<div class="col-md-6 value">{{ Contract::getOpengingCount(Contract::TYPE_HOURLY) }}</div>
    	        	</div>
    	        	<div class="row row3 contracts">
    	        		<div class="col-md-6">Fixed Contract:</div>
    	        		<div class="col-md-6 value">{{ Contract::getOpengingCount(Contract::TYPE_FIXED) }}</div>
    	        	</div>
                </div>
                <a class="more" href="{{ route('admin.super.job.jobs') }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat green-haze">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
    	        	<div class="row mt-2">
    	        		<div class="col-md-7 text-right">Withdraw Requests:</div>
    	        		<div class="col-md-5 value">{{ TransactionLocal::getTotalWithdrawRequests() }}</div>
    	        	</div>
                    <div class="row mt-3">
                        <div class="col-md-7 text-right">In Escrow:</div>
                        <div class="col-md-5 value">${{ formatCurrency(TransactionLocal::totalAmountEscrows()) }}</div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-7 text-right">Site Balance:</div>
                        <div class="col-md-5 value">${{ SiteWallet::holding()->amount }}</div>
                    </div>
                </div>
                <a class="more" href="{{ route('admin.super.payment.transactions') }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
    </div>
    <!-- END DASHBOARD STATS --><div class="clearfix"></div>

    <div class="row">
        <!-- BEGIN Notifications for Ticket -->
        <div class="col-md-6 col-sm-6">
            @include('pages.admin.super.dashboard.notifications')
        </div>
        <!-- END Notifications for Ticket -->

        <!-- BEGIN My Tickets -->
        <div class="col-md-6 col-sm-6">
        	@include('pages.admin.super.dashboard.todos')
        <!-- END My Tickets -->
        </div>
    </div>

    <!-- BEGIN User Growth -->
    <div id="growth" class="portlet light stats-widget">
    	<script type="text/javascript">
    	    // js variables for growth graph
    	    var growthStartDate = '{{ $growth['start_date'] }}';
    	    var growthEndDate 	= '{{ $growth['end_date'] }}';
    	</script>
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">User Growth</span>
            </div>
            <div class="actions">
                <div class="chart-daterange pull-right tooltips btn btn-fit-height grey-salt" data-placement="left" data-original-title="Change dashboard date range" data-id="growth">
                    <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.super.dashboard.user_growth_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.super.dashboard.user_growth_pie_chart')
                </div>
            </div>
        </div>
    </div>
    <!-- END User Growth -->

    <!-- BEGIN Job Postings Growth -->
    <div id="jobposting" class="portlet light stats-widget">
        <script type="text/javascript">
            // js variables for growth graph
            var jobpostingStartDate = '{{ $jobposting['start_date'] }}';
            var jobpostingEndDate   = '{{ $jobposting['end_date'] }}';
        </script>
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">Job Postings Growth</span>
            </div>
            <div class="actions">
                <div class="chart-daterange pull-right tooltips btn btn-fit-height grey-salt" data-placement="left" data-original-title="Change dashboard date range" data-id="jobposting">
                    <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.super.dashboard.jobposting_growth_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.super.dashboard.jobposting_growth_pie_chart')
                </div>
            </div>
        </div>
    </div>
    <!-- END Job Postings Growth -->

    <!-- BEGIN Transactions -->
    <div id="transaction" class="portlet light stats-widget">
        <script type="text/javascript">
            // js variables for growth graph
            var transactionStartDate = '{{ $transaction['start_date'] }}';
            var transactionEndDate   = '{{ $transaction['end_date'] }}';
        </script>
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">Transactions</span>
            </div>
            <div class="actions">
                <div class="chart-daterange pull-right tooltips btn btn-fit-height grey-salt" data-placement="left" data-original-title="Change dashboard date range" data-id="transaction">
                    <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.super.dashboard.transaction_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.super.dashboard.transaction_pie_chart')
                </div>
            </div>
        </div>
    </div>
    <!-- END Transactions -->
</div><!-- .main-content -->

@endsection