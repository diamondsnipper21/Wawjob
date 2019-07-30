/**
* report/timesheet.js
*/

define(['moment', 'daterangepicker', 'cookie'], function (moment) {
    var fn = {
        $date_range: null,
        from: '',
        to: '',
        mode: '',

        init: function () {
            this.$dateRange = $('#date_range_value');

            fn.from = this.$dateRange.data('from');
            fn.to = this.$dateRange.data('to');
            fn.mode = this.$dateRange.data('mode');

            this.bind();
            this.render();

            Global.renderSelect2();
        },

        bind: function() {
            $('.report-mode-section .mode-item').on('click', function() {
                fn.mode = $(this).data('mode');

                fn.generateUrl();
            });

            $('.contract-filter').on('change', fn.generateUrl);
        },

        render: function() {
            $('#date_range').daterangepicker({
                opens: 'right',
                format: 'MM/DD/YYYY',
                ranges: {
                    'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
                    'This Week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                applyClass: 'btn-primary',
                separator: ' to ',
                startDate: moment(fn.from),
                endDate: moment(fn.to),
                'locale': {
                    'applyLabel': trans.apply,
                    'cancelLabel': trans.cancel,
                    'fromLabel': trans.from,
                    'toLabel': trans.to,
                    'customRangeLabel': trans.custom,
                    'daysOfWeek': [
                        trans.sun,
                        trans.mon,
                        trans.tue,
                        trans.wed,
                        trans.thu,
                        trans.fri,
                        trans.sat,
                    ],
                    'monthNames': [
                        trans.jan,
                        trans.feb,
                        trans.mar,
                        trans.apr,
                        trans.may,
                        trans.jun,
                        trans.jul,
                        trans.aug,
                        trans.sep,
                        trans.oct,
                        trans.nov,
                        trans.dec,
                    ],
                }
            }, function (start, end) {
                var s = start.format('MMM D, YYYY'); 
                var e = end.format('MMM D, YYYY'); 
                var str;

                if (s == e) {
                    str = s;
                } else {
                    str = s + ' - ' + e;
                }

                $('#date_range input').val(str);

                fn.from = start.format('YYYY-MM-DD');
                fn.to = end.format('YYYY-MM-DD');

                fn.generateUrl();
            });
        },

        generateUrl: function() {
			var url = currentURL + '?from=' + fn.from + '&to=' + fn.to + '&mode=' + fn.mode;

            if ( $('.contract-filter').val() != '0' ) {
            	url += '&contract_id=' + $('.contract-filter').val();
            }

            location.href = url;
        }
    };

    return fn;
});