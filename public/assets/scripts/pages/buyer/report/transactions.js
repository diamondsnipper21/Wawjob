/**
 * report/transactions.js
 */

define(['wjbuyer', 'moment', 'daterangepicker'], function (buyer, moment) {
 	var fn = {
 		filter: {
 			$form: null,

 			initDateRanger: function() {
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
	                startDate: moment(date_from),
	                endDate: moment(date_to),
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
 						str = s + " - " + e;
 					}

 					$('#date_range input').val(str);

 					fn.filter.submit();
 				}); 

		        // Previous, Next Range Link
		        $('.prev-unit, .next-unit').on('click', function() {
		        	$('#date_range input').val($(this).data('range'));
		        	fn.filter.submit();

		        	return false;
		        });
    		},

		    initContractFilter: function() {
		    	$('.contract-filter').on('change', this.submit);
		    }, 

		    initTransactionTypeFilter: function() {
		    	$('#transaction_type').on('change', this.submit)
		    }, 

		    submit: function() {
		    	fn.filter.$form.submit();
		    },

		    init: function() {
		    	this.$form = $('#frm_transactions_filter');

		    	this.initDateRanger();
		    	this.initContractFilter();
		    	this.initTransactionTypeFilter();

		    	Global.renderSelect2();
		    }
		},

		init: function () {
			this.filter.init();
		}
	};

	window.moment = moment;

	return fn;
});