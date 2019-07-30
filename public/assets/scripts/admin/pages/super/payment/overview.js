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
            $('#payment_graph .actions .bootstrap-switch-wrapper').on('click', function(e)
            {
                fn.submitData(paymentGraphStartDate, paymentGraphEndDate);
            });
            $('#payment_graph .actions .bootstrap-switch-handle-off').on('click', function(e)
            {
                fn.submitData(paymentGraphStartDate, paymentGraphEndDate);
            });
            $('#payment_graph .actions .bootstrap-switch-handle-on').on('click', function(e)
            {
                fn.submitData(paymentGraphStartDate, paymentGraphEndDate);
            });            
        },

        render: function() {
            // Payment Graph
            this.renderDaterange($('#payment_graph'), paymentGraphStartDate, paymentGraphEndDate);
            this.renderLineChart('payment_line_chart', paymentLineGraphData);
            this.renderPieChart('payment_pie_chart', paymentPieGraphData);
        },

        renderDaterange: function($container, startDate, endDate) {
            if (!jQuery().daterangepicker) {
                return;
            }

            var self = this;
            $('.chart-daterange span', $container).html(moment(startDate).format('MMMM D, YYYY') + ' - ' + moment(endDate).format('MMMM D, YYYY'));
            
            $('.chart-daterange', $container).daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: startDate,
                    endDate: endDate,
                    minDate: '01/01/2017',
                    maxDate: '12/31/2020',
                    dateLimit: {
                        days: 60
                    },
                    showDropdowns: false,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
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

                    fn.submitData(startDate, endDate);
                }
            );

            $('.chart-daterange', $container).show();
        },

        renderLineChart: function(chartId, graphData) {
            
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
                "categoryField": "date",
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
                    "parseDates": true,
                    "autoGridCount": false,
                    "axisColor": "#555555",
                    "gridAlpha": 0,
                    "gridCount": 50
                }
            });
        },

        renderPieChart: function(chartId, graphData) {
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
        
        submitData: function(startDate, endDate) {
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

                    eval('fn.renderDaterange($container, paymentGraphStartDate, paymentGraphEndDate);');
                    eval('fn.renderLineChart(\'payment_line_chart\', paymentLineGraphData);');
                    eval('fn.renderPieChart(\'payment_pie_chart\', paymentPieGraphData);');
                    Metronic.init();
                    fn.bindEvents();
                }
            });
        }
    };

    return fn;
});