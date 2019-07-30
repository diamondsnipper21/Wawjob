define(['stars','/assets/plugins/metronics/js/metronic.js', '/assets/plugins/metronics/js/jquery.easypiechart.min.js', '/assets/plugins/metronics/js/jquery.flot.min.js', '/assets/plugins/metronics/js/jquery.flot.resize.min.js', '/assets/plugins/metronics/js/jquery.flot.categories.min.js' ], function (stars) {
    var fn = {

        init: function () {
                    this.initMiniCharts();
                    this.initCharts();
                    stars.init($('.feedback-score .value .stars'));
                },

                initMiniCharts: function () {

                    $('.easy-pie-chart .number.transactions').easyPieChart({
                        animate: 1000,
                        size: 200,
                        lineWidth: 15,
                        barColor: Metronic.getBrandColor('green')
                    });

                },

                initCharts: function () {

                    if (!jQuery.plot) {
                        return;
                    }

                    if ($('#profile_views').size() != 0) {
                        
                        var data1 = [
                            ['DEC', 300],
                            ['JAN', 600],
                            ['FEB', 1100],
                            ['MAR', 2300],
                            ['APR', 860],
                            ['MAY', 1200],
                            ['JUN', 1450],
                            ['JUL', 1800],
                            ['AUG', 1200],
                            ['SEP', 600]
                        ];

                        var plot_statistics = $.plot($("#profile_views"),

                            [{
                                data: data1,
                                lines: {
                                    fill: 0.3,
                                    lineWidth: 0.9,
                                },
                                color: ['#BAD9F5']
                            }, {
                                data: data1,
                                points: {
                                    show: true,
                                    fill: true,
                                    radius: 4,
                                    fillColor: "#9ACAE6",
                                    lineWidth: 2
                                },
                                color: '#9ACAE6',
                                shadowSize: 1
                            }, {
                                data: data1,
                                lines: {
                                    show: true,
                                    fill: false,
                                    lineWidth: 3
                                },
                                color: '#9ACAE6',
                                shadowSize: 0
                            }],

                            {

                                xaxis: {
                                    tickLength: 0,
                                    tickDecimals: 0,
                                    mode: "categories",
                                    min: 0,
                                    font: {
                                        lineHeight: 18,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                yaxis: {
                                    ticks: 5,
                                    tickDecimals: 0,
                                    tickColor: "#eee",
                                    font: {
                                        lineHeight: 14,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                grid: {
                                    hoverable: true,
                                    clickable: true,
                                    tickColor: "#eee",
                                    borderColor: "#eee",
                                    borderWidth: 1
                                }
                            });              
                       
                    }
                },
    };

    return fn;
});