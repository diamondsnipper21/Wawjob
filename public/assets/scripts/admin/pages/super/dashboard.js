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
            $('body').on('click', '.notification-action a', function() {
                var $self = $(this);

                $.ajax($(this).attr('href'), {
                    dataType: 'json',
                    type: 'post',
                    success: function(json) {
                        $self.closest('li').slideUp();
                    }
                });
                return false;
            });
		},

		render: function() {
			// User Growth
			this.renderDaterange($('#growth'), growthStartDate, growthEndDate);
			this.renderLineChart('growth_line_chart', growthLineGraphData);
			this.renderPieChart('growth_pie_chart', growthPieGraphData);

			// Job Postings Growth
			this.renderDaterange($('#jobposting'), jobpostingStartDate, jobpostingEndDate);
			this.renderLineChart('jobposting_line_chart', jobpostingLineGraphData);
			this.renderPieChart('jobposting_pie_chart', jobpostingPieGraphData);

			// Transactions
			this.renderDaterange($('#transaction'), transactionStartDate, transactionEndDate);
			this.renderLineChart('transaction_line_chart', transactionLineGraphData);
			this.renderPieChart('transaction_pie_chart', transactionPieGraphData);
		},

		renderDaterange: function($container, startDate, endDate) {
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
                    // dateLimit: {
                    //     days: 60
                    // },
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
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')]
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
                	var id = this.element.data('id');
                    $('.chart-daterange span', '#' + id).html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                    var startDate = start.format('YYYY-MM-DD');
                    var endDate   = end.format('YYYY-MM-DD');

                    $.ajax(currentURL, {
                    	'type': 'post',
                    	'data': {
                    		'graph_id' : id,
                    		'start_date': startDate,
                    		'end_date': endDate
                    	},
                    	'success': function(html) {
                    		var $html = $(html);
		                    var $contents = $('#' + id, $html);

		                    $('#' + id).html($contents.html());

                    		eval('self.renderDaterange($container, ' + id + 'StartDate, ' + id + 'EndDate);');
							eval('self.renderLineChart(\'' + id + '_line_chart\', ' + id + 'LineGraphData);');
							eval('self.renderPieChart(\'' + id + '_pie_chart\', ' + id + 'PieGraphData);');
                    	}
                    });
                }
            );

            $('.chart-daterange span', $container).html(moment(startDate).format('MMMM D, YYYY') + ' - ' + moment(endDate).format('MMMM D, YYYY'));
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
		}
	};

	return fn;
});