/**
 * @author KCG
 * @since June 10, 2017
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
			// Stats Section
			this.renderDaterange();
			this.renderLineChart();
			this.renderPieChart();

            $('[data-toggle="tooltip"]').tooltip();
		},

		renderDaterange: function() {
            if (!jQuery().daterangepicker) {
                return;
            }

            var self = this;

            $('#stats-range').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: statStartDate,
                    endDate: statEndDate,
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
                    $('#stats-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                    var startDate = start.format('YYYY-MM-DD');
                    var endDate   = end.format('YYYY-MM-DD');

                    $.ajax(currentURL, {
                    	'type': 'post',
                    	'data': {
                    		'start_date': startDate,
                    		'end_date': endDate
                    	},
                    	'dataType': 'json',
                    	'success': function(data) {
                    		lineGraphData = data.line;
                    		pieGraphData = data.pie;

                    		self.renderLineChart();
                    		self.renderPieChart();
                    	}
                    });
                }
            );

            $('#stats-range span').html(moment(statStartDate).format('MMMM D, YYYY') + ' - ' + moment(statEndDate).format('MMMM D, YYYY'));
            $('#stats-range').show();
        },

        renderLineChart: function() {
        	var chart = AmCharts.makeChart("line_chart", {
	            "type": "serial",
	            "theme": "light",

	            "fontFamily": 'Open Sans',            
	            "color":    '#888888',

	            "dataProvider": lineGraphData['data'],
	            "balloon": {
	                "cornerRadius": 6
	            },
	            "legend": {
	                "useGraphSettings": true,
	                "markerSize": 12,
	                "valueWidth": 0,
	                "verticalGap": 0
	            },
	            "graphs": lineGraphData['options'],
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
	                // "parseDates": true,
	                "autoGridCount": false,
	                "axisColor": "#555555",
	                "gridAlpha": 0,
	                "gridCount": 50
	            }
	        });
        },

        renderPieChart: function() {
        	var chart = AmCharts.makeChart("pie_chart", {
	            "type": "pie",
	            "theme": "light",
	            "fontFamily" : 'Open Sans',	            
	            "color":    '#888',
	            "dataProvider": pieGraphData,
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