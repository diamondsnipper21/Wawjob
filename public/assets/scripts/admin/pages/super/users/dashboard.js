/**
 * @author KCG
 * @since June 10, 2017
 * User Overview
 */

define(['daterangepicker', 'moment', 'vmap-world', 'select2'], function (daterangepicker, moment) {

	var fn = {
		init: function() {
            this.$container = $('.super-users-dashboard');

			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
            var self = this;

            Metronic.addResizeHandler(function () {
                $('.vmaps').each(function () {
                    var map = jQuery(this);
                    map.width(map.parent().width());
                });
            });

            this.$container.on('change', '#stat_region_users .select2', function() {
                $.ajax(currentURL, {
                    'type': 'post',
                    'data': {
                        'user_role': $(this).val()
                    },
                    'success': function(html) {
                        var $html = $(html);
                        var $contents = $('#stat_region_users', $html);

                        $('#stat_region_users').html($contents.html());

                        self.renderJQVMAP();

                        $('input[type="checkbox"]').uniform();
                    }
                });
            });

            this.$container.on('change', '#lifetime', function() {
                if (!$(this).is(':checked')) {
                    $('#stats-range').removeClass('disabled');
                    return false;
                }

                var $container = $('#overview_stats');

                $.ajax(currentURL, {
                    'type': 'post',
                    'data': {
                        'lifetime': true
                    },
                    'success': function(html) {
                        var $html = $(html);
                        var $contents = $('#overview_stats', $html);

                        $('#overview_stats').html($contents.html());

                        self.renderDaterange();
                        self.renderLineChart();
                        self.renderPieChart();

                        $('input[type="checkbox"]').uniform();
                        
                    }
                });
            });
		},

		render: function() {
			// Stats Section
			this.renderDaterange();
			this.renderLineChart();
			this.renderPieChart();

            // vmap
            this.renderJQVMAP();
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
                    minDate: '01/01/2015',
                    maxDate: '12/31/2050',
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

                    var $container = $('#overview_stats');

                    $.ajax(currentURL, {
                    	'type': 'post',
                    	'data': {
                    		'start_date': startDate,
                    		'end_date': endDate,
                            'lifetime': $('#lifetime').is(':checked')?1:0
                    	},
                    	'success': function(html) {
                            var $html = $(html);
                            var $contents = $('#overview_stats', $html);

                            $('#overview_stats').html($contents.html());

                            self.renderDaterange();
                            self.renderLineChart();
                            self.renderPieChart();
                            
                            $('input[type="checkbox"]').uniform();
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
	            // "dataDateFormat": "YYYY-MM-DD",
                "valueAxis": [{
                    integersOnly: true,
                    precision: 0,
                    step: 1,
                    autoGridCount: true
                }],
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
		},

        renderJQVMAP: function () {
            if (!jQuery().vectorMap) {
                return;
            }

            // $('#stat_region_users .select2').select2('detach');
            $('#stat_region_users .select2').select2({
                allowClear: false,
                width: 150,
                minimumResultsForSearch: -1
            });

            var setMap = function (name) {
                var data = {
                    map: 'world_en',
                    backgroundColor: null,
                    borderColor: '#333333',
                    borderOpacity: 0.5,
                    borderWidth: 1,
                    color: '#c6c6c6',
                    enableZoom: true,
                    hoverColor: '#c9dfaf',
                    hoverOpacity: null,
                    values: statRegionUsers,
                    normalizeFunction: 'linear',
                    scaleColors: ['#b6da93', '#909cae'],
                    selectedColor: '#c9dfaf',
                    selectedRegion: null,
                    showTooltip: true,
                    onLabelShow: function (event, label, code) {
                        if (code in statRegionUsers)
                            label.html(label.text() + ': <strong>' + statRegionUsers[code] + '</strong>');
                    },
                    onRegionOver: function (event, code) {
                        // if (code == 'ca') {
                        //     event.preventDefault();
                        // }
                    },
                    onRegionClick: function (element, code, region) {
                    }
                };

                data.map = name + '_en';
                var map = jQuery('#vmap_' + name);
                if (!map) {
                    return;
                }
                map.width(map.parent().parent().width());
                map.show();
                map.vectorMap(data);
                // map.hide();
            }

            setMap("world");
        }
	};

	return fn;
});