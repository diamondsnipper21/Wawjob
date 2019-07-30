/**
 * report/timelogs.js
 */

define(['moment', 'daterangepicker'], function (moment) {
 	var fn = {
        from: '',
        to: '',

		init: function () {
            this.$dateRange = $('#date_range_value');

            fn.from = this.$dateRange.data('from');
            fn.to = this.$dateRange.data('to');

            this.bind();
		},

        bind: function() {
            $('#date_range').daterangepicker({
                opens: 'right',
                format: 'MM/DD/YYYY',
                startDate: moment(fn.from),
                endDate: moment(fn.to),
                singleDatePicker: true, 
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
                wstart = start.startOf('isoWeek');
                wend = end.endOf('isoWeek');
                var s = wstart.format('MMM D, YYYY'); 
                var e = wend.format('MMM D, YYYY'); 
                var str;

                if (s == e) {
                    str = s;
                } else {
                    str = s + ' - ' + e;
                }

                $('#date_range input').val(str);

                location.href = currentURL + '?from=' + wstart.format('YYYY-MM-DD');
            });

            // Previous, Next Range Link
            $('.prev-unit, .next-unit').on('click', function() {
                location.href = currentURL + '?from=' + $(this).data('from');
            });

            // Customize datepicker
            var start_date = $('.available.active.start-date');
            start_date.removeClass('end-date');
            for(i = 0; i < 6; i++) {
                if(start_date.next().length == 0)
                    start_date = start_date.parent().next().children(0).eq(0);
                else
                    start_date = start_date.next();
                if(i == 5)
                    start_date.addClass('active end-date');
                else
                    start_date.addClass('active in-range');
            }
        },
	};

	return fn;
});