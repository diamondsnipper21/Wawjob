<?php
/**
* Payment Overview Page on Super Admin
*
* @author LSN
* @since July 23, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;

?>
@extends('layouts/admin/super')

@section('additional-js')
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/amcharts.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/serial.js') }}"></script>
<script src="{{ url('assets/plugins/metronic/global/plugins/amcharts/amcharts/pie.js') }}"></script>
@endsection

@section('content')

<div class="main-content">
    <div class="row affiliates-overview">
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
            <div class="dashboard-stat blue-madison">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="row1">
                        <b></b>
                    </div>
                    <div class="number">
                    @if ( $baseData['pending_payment']['amount'] >= 0 )
                        $ {{ $baseData['pending_payment']['amount'] }}
                    @else
                        ($ {{ abs($baseData['pending_payment']['amount']) }})
                    @endif
                    </div>
                    <div class="desc">
                        Pending Payment
                    </div>
                </div>  
                <a class="more" href="javascript:;">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
            <div class="dashboard-stat red-intense">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                    @if ( $baseData['last_payment']['amount'] >= 0 )
                        $ {{ $baseData['last_payment']['amount'] }}
                    @else
                        ($ {{ abs($baseData['last_payment']['amount']) }})
                    @endif
                    </div>
                    <div class="desc">
                        {{ $baseData['last_payment']['date'] }}
                    </div>
                    <div class="desc">
                        Last Payment
                    </div>
                </div>
                <a class="more" href="javascript:;">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
            <div class="dashboard-stat green-haze">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="number">
                    @if ( $baseData['lifetime_payment']['amount'] >= 0 )
                        $ {{ $baseData['lifetime_payment']['amount'] }}
                    @else
                        ($ {{ abs($baseData['lifetime_payment']['amount']) }})
                    @endif
                    </div>
                    <div class="desc">
                        LifeTime Payment
                    </div>
                </div>
                <a class="more" href="javascript:;">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
            <div class="dashboard-stat purple-plum">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number">
                        {{ $baseData['invited_users_count'] }}  
                        ( 
                        {{ $baseData['invited_buyers_count'] }} / 
                        {{ $baseData['invited_freelancers_count'] }} 
                        )
                    </div>
                    <div class="desc">
                        Lifetime Invited Users (Buyers / Freelancers)
                    </div>
                </div>
                <a class="more" href="javascript:;">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
            <div class="dashboard-stat green-haze">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number">
                        {{ $baseData['affiliate_users_count'] }}
                    </div>
                    <div class="desc">
                        Lifetime Affiliate Users
                    </div>
                </div>
                <a class="more" href="javascript:;">View more <i class="m-icon-swapright m-icon-white"></i></a>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div id="affiliate_graph" class="portlet light stats-widget">
        <script type="text/javascript">
            // js variables for payment graph
            var affiliateStartDate = '{{ $period['startDate'] }}';
            var affiliateEndDate   = '{{ $period['endDate'] }}';
        </script>
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">Affiliate Users</span>
            </div>
            <div class="actions row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3">Lifetime</label>
                        <div class="col-md-9">
                            <input type="checkbox" id="lifetime" name="lifetime" class="make-switch" data-on-text="On" data-off-text="Off" {{ $lifetime == "true" ? "checked" : ""}}>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-daterange pull-right tooltips btn btn-fit-height grey-salt" data-placement="left" data-original-title="Change Overview date range" data-id="payment_graph">
                        <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.super.affiliates.overview.affiliate_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.super.affiliates.overview.affiliate_pie_chart')
                </div>
            </div>
        </div>
    </div>

    <div id="payment_graph" class="portlet light stats-widget">
        <script type="text/javascript">
            // js variables for payment graph
            var paymentStartDate = '{{ $period['startDate'] }}';
            var paymentEndDate   = '{{ $period['endDate'] }}';
        </script>
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-share font-blue-steel hide"></i>
                <span class="caption-subject font-red-sunglo bold">Payment Stat</span>
            </div>
            <div class="actions row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3">Lifetime</label>
                        <div class="col-md-9">
                            <input type="checkbox" id="lifetime" name="lifetime" class="make-switch" data-on-text="On" data-off-text="Off" {{ $lifetime == "true" ? "checked" : ""}}>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-daterange pull-right tooltips btn btn-fit-height grey-salt" data-placement="left" data-original-title="Change Overview date range" data-id="payment_graph">
                        <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.super.affiliates.overview.payment_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.super.affiliates.overview.payment_pie_chart')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection