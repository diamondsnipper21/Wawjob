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
    <div class="super-payments-overview">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="dashboard-stat blue-madison">
                    <div class="visual">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div class="details">
                        <div class="row1">
                            <b>${{ formatCurrency($payment_data['escrow']) }}</b>
                        </div>
                        <div class="row2">
                            In Escrow
                        </div>
                    </div>  
                    <a class="more" href="{{ route($current_user->isFinancial() ? 'admin.financial.transactions' : 'admin.super.payment.transactions', ['view' => 'escrow']) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="dashboard-stat green-haze">
                    <div class="visual">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="details">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="row1">
                                    <b>${{ formatCurrency($payment_data['withdrawal']) }}</b>
                                </div>
                                <div class="row2">
                                    In Withdrawal Request
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="row1">
                                    <b>${{ formatCurrency($payment_data['total_withdrawals']) }}</b>
                                </div>
                                <div class="row2">
                                    Total Withdrawals
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="more" href="{{ route($current_user->isFinancial() ? 'admin.financial.transactions' : 'admin.super.payment.transactions', ['view' => 'withdraw']) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="dashboard-stat purple-plum">
                    <div class="visual">
                        <i class="fa fa-globe"></i>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="details">
                                <div class="row1">
                                    <b>${{ formatCurrency($payment_data['deposit']) }}</b>
                                </div>
                                <div class="row2">
                                    Total Deposits
                                </div>
                            </div>  
                            <a class="more" href="{{ route($current_user->isFinancial() ? 'admin.financial.deposit' : 'admin.super.payment.deposit') }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="details">
                                <div class="row1">
                                    <b>${{ formatCurrency($payment_data['holding']) }}  (${{ formatCurrency($payment_data['earning']) }})</b>
                                </div>
                                <div class="row2">
                                    Holding  (Earning)
                                </div>
                            </div>
                            <a class="more" href="{{ route($current_user->isFinancial() ? 'admin.financial.transactions' : 'admin.super.payment.transactions', ['view' => 'earning']) }}">View more <i class="m-icon-swapright m-icon-white"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <!-- END OVERVIEW STATS -->
    </div>
    <!-- BEGIN PAYMENT GRAPH -->
    <div id="payment_graph" class="portlet light stats-widget">
        <script type="text/javascript">
            // js variables for payment graph
            var paymentGraphStartDate = '{{ $payment_graph['start_date'] }}';
            var paymentGraphEndDate   = '{{ $payment_graph['end_date'] }}';
        </script>
        <div class="portlet-title">
            <div class="col-md-6">
                <div class="caption">
                    <i class="icon-share font-blue-steel hide"></i>
                    <span class="caption-subject font-red-sunglo bold">Contract</span>
                </div>
            </div>
            <div class="col-md-6">
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
                            <i class="icon-calendar"></i>&nbsp; <span class="visible-lg-inline-block">&nbsp;</span>&nbsp; <i class="fa fa-angle-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    @include('pages.admin.super.payment.overview.payment_line_chart')
                </div>
                <div class="col-md-6">
                    @include('pages.admin.super.payment.overview.payment_pie_chart')
                </div>
            </div>
        </div>
    </div>
    <!-- END PAYMENT GRAPH -->
</div>
@endsection