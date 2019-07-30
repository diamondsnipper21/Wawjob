/**
 * @author KCG
 * @since June 24, 2017
 */

define(['daterangepicker', 'moment'], function (daterangepicker, moment) {

    var fn = {
        init: function() {

            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
            //
            $('#affiliate_graph .actions .bootstrap-switch-wrapper').on('click', function(e)
            {
                fn.submitAffiliateData(affiliateStartDate, affiliateEndDate);
            });
            $('#affiliate_graph .actions .bootstrap-switch-handle-off').on('click', function(e)
            {
                fn.submitAffiliateData(affiliateStartDate, affiliateEndDate);
            });
            $('#affiliate_graph .actions .bootstrap-switch-handle-on').on('click', function(e)
            {
                fn.submitAffiliateData(affiliateStartDate, affiliateEndDate);
            });

            //
            $('#payment_graph .actions .bootstrap-switch-wrapper').on('click', function(e)
            {
                fn.submitPaymentData(paymentStartDate, paymentEndDate);
            });
            $('#payment_graph .actions .bootstrap-switch-handle-off').on('click', function(e)
            {
                fn.submitPaymentData(paymentStartDate, paymentEndDate);
            });
            $('#payment_graph .actions .bootstrap-switch-handle-on').on('click', function(e)
            {
                fn.submitPaymentData(paymentStartDate, paymentEndDate);
            });
            
        },

        render: function() {
            // Graph
            this.renderAffiliateDaterange($('#affiliate_graph'), affiliateStartDate, affiliateEndDate);
            this.renderAffiliateLineChart('affiliate_line_chart', affiliateLineGraphData);
            this.renderAffiliatePieChart('affiliate_pie_chart', affiliatePieGraphData);

            this.renderPaymentDaterange($('#payment_graph'), paymentStartDate, paymentEndDate);
            this.renderPaymentLineChart('payment_line_chart', paymentLineGraphData);
            this.renderPaymentPieChart('payment_pie_chart', paymentPieGraphData);
        },

        renderAffiliateDaterange: function($container, startDate, endDate) {
            if (!jQuery().daterangepicker) {
                return;
            }

            var self = this;

            $('.chart-daterange', $container).daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: startDate,
                    endDate: endDate,
                    minDate: '01/01/2017',
                    maxDate: '12/31/2020',
                    dateLimit: {
                        days: 185
                    },
                    showDropdowns: false,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        //'Today': [moment(), moment()],
                        //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 6 Months': [moment().subtract(6, 'month'), moment()],
                        'Last 3 Months': [moment().subtract(3, 'month'), moment()],
                        //'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    buttonClasses: ['btn btn-sm'],
                    applyClass: ' blue',
                    cancelClass: 'default',
                    format: 'MM/DD/YYYY',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Apply',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom Range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                },
                function (start, end) {
                    $('.chart-daterange span', '#affiliate_graph').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    var startDate = start.format('YYYY-MM-DD');
                    var endDate   = end.format('YYYY-MM-DD');
                    fn.submitAffiliateData(startDate, endDate);
                }
            );

            $('.chart-daterange span', $container).html(moment(startDate).format('MMMM D, YYYY') + ' - ' + moment(endDate).format('MMMM D, YYYY'));
            $('.chart-daterange', $container).show();
        },

        renderPaymentDaterange: function($container, startDate, endDate) {
            if (!jQuery().daterangepicker) {
                return;
            }

            var self = this;

            $('.chart-daterange', $container).daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: startDate,
                    endDate: endDate,
                    minDate: '01/01/2017',
                    maxDate: '12/31/2020',
                    dateLimit: {
                        days: 185
                    },
                    showDropdowns: false,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        //'Today': [moment(), moment()],
                        //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 6 Months': [moment().subtract(6, 'month'), moment()],
                        'Last 3 Months': [moment().subtract(3, 'month'), moment()],
                        //'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    buttonClasses: ['btn btn-sm'],
                    applyClass: ' blue',
                    cancelClass: 'default',
                    format: 'MM/DD/YYYY',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Apply',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom Range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                },
                function (start, end) {
                    $('.chart-daterange span', '#payment_graph').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    var startDate = start.format('YYYY-MM-DD');
                    var endDate   = end.format('YYYY-MM-DD');
                    fn.submitPaymentData(startDate, endDate);
                }
            );

            $('.chart-daterange span', $container).html(moment(startDate).format('MMMM D, YYYY') + ' - ' + moment(endDate).format('MMMM D, YYYY'));
            $('.chart-daterange', $container).show();
        },

        renderAffiliateLineChart: function(chartId, graphData) {            
            var chart = AmCharts.makeChart(chartId, {
                "type": "serial",
                "theme": "light",

                "fontFamily": 'Open Sans',            
                "color":    '#888888',

                "dataProvider": graphData['data'],
                "balloon": {
                    "cornerRadius": 6
                },
                "legend": {
                    "useGraphSettings": true,
                    "markerSize": 6,
                    "valueWidth": 0,
                    "verticalGap": 0
                },
                "graphs": graphData['options'],
                "dataDateFormat": "YYYY-MM-DD",
                "categoryField": "month",
                "categoryAxis": {
                    "dateFormats": [{
                        "period": "DD",
                        "format": "DD"
                    }, {
                        "period": "WW",
                        "format": "MMM DD"
                    }, {
                        "period": "MM",
                        "format": "MMM"
                    }, {
                        "period": "YYYY",
                        "format": "YYYY"
                    }],
                    "parseDates": false,
                    "autoGridCount": false,
                    "axisColor": "#555555",
                    "gridAlpha": 0,
                    "gridCount": 50
                }
            });
        },

        renderAffiliatePieChart: function(chartId, graphData) {
            var chart = AmCharts.makeChart(chartId, {
                "type": "pie",
                "theme": "light",
                "fontFamily" : 'Open Sans',             
                "color":    '#888',
                "dataProvider": graphData,
                "valueField": "value",
                "titleField": "type",
                "outlineAlpha": 0.4,
                "depth3D": 15,
                "balloonText": "[[type]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                "angle": 30
            });
        },

        renderPaymentLineChart: function(chartId, graphData) {            
            var chart = AmCharts.makeChart(chartId, {
                "type": "serial",
                "theme": "light",

                "fontFamily": 'Open Sans',            
                "color":    '#888888',

                "dataProvider": graphData['data'],
                "balloon": {
                    "cornerRadius": 6
                },
                "legend": {
                    "useGraphSettings": true,
                    "markerSize": 12,
                    "valueWidth": 0,
                    "verticalGap": 0
                },
                "graphs": graphData['options'],
                "dataDateFormat": "YYYY-MM-DD",
                "categoryField": "month",
                "categoryAxis": {
                    "dateFormats": [{
                        "period": "DD",
                        "format": "DD"
                    }, {
                        "period": "WW",
                        "format": "MMM DD"
                    }, {
                        "period": "MM",
                        "format": "MMM"
                    }, {
                        "period": "YYYY",
                        "format": "YYYY"
                    }],
                    "parseDates": false,
                    "autoGridCount": false,
                    "axisColor": "#555555",
                    "gridAlpha": 0,
                    "gridCount": 50
                }
            });
        },

        renderPaymentPieChart: function(chartId, graphData) {
            var chart = AmCharts.makeChart(chartId, {
                "type": "pie",
                "theme": "light",
                "fontFamily" : 'Open Sans',             
                "color":    '#888',
                "dataProvider": graphData,
                "valueField": "value",
                "titleField": "type",
                "outlineAlpha": 0.4,
                "depth3D": 15,
                "balloonText": "[[type]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                "angle": 30
            });
        },

        submitAffiliateData: function(startDate, endDate) {
            $container = $('#affiliate_graph');
            $.ajax(currentURL, {
                'type': 'post',
                'data': {
                    'start_date': startDate,
                    'end_date': endDate,
                    'lifetime': $('#affiliate_graph .actions #lifetime').is(':checked')
                },
                'success': function(html) {
                    var $html = $(html);
                    var $contents = $('#affiliate_graph', $html);

                    $('#affiliate_graph').html($contents.html());

                    eval('fn.renderAffiliateDaterange($container, affiliateStartDate, affiliateEndDate);');
                    eval('fn.renderAffiliateLineChart(\'affiliate_line_chart\', affiliateLineGraphData);');
                    eval('fn.renderAffiliatePieChart(\'affiliate_pie_chart\', affiliatePieGraphData);');
                    Metronic.init();
                    fn.bindEvents();
                }
            });
        },

        submitPaymentData: function(startDate, endDate) {
            $container = $('#payment_graph');
            $.ajax(currentURL, {
                'type': 'post',
                'data': {
                    'start_date': startDate,
                    'end_date': endDate,
                    'lifetime': $('#payment_graph .actions #lifetime').is(':checked')
                },
                'success': function(html) {
                    var $html = $(html);
                    var $contents = $('#payment_graph', $html);

                    $('#payment_graph').html($contents.html());

                    eval('fn.renderPaymentDaterange($container, paymentStartDate, paymentEndDate);');
                    eval('fn.renderPaymentLineChart(\'payment_line_chart\', paymentLineGraphData);');
                    eval('fn.renderPaymentPieChart(\'payment_pie_chart\', paymentPieGraphData);');
                    Metronic.init();
                    fn.bindEvents();
                }
            });
        }
    };

    return fn;
});