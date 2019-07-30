/**
 * affiliate.js
 */

define(['common', 'moment', 'chartjs', 'daterangepicker', 'jquery-form'], function (common, moment, Chart) {

  	var fn = {
  		$wrapper: null,
  		$chart: null,
  		$form: null,
  		$listform: null,
  		$listAffiliates: null,
  		$emails: null,
  		$daterange: null,
  		$usertype: null,

   		init: function () {
   			this.$wrapper = $('.user-affiliate-page');
   			this.$chart = $('#chart_invitation', this.$wrapper);
   			this.$form = $('#formAffiliate', this.$wrapper);
   			this.$emails = $('#emails', this.$form);

   			this.$listform = $('#formAffiliateHistory', this.$wrapper);
   			this.$listAffiliates = $('.list-affiliates', this.$wrapper);
   			this.$daterange = $('#date_range', this.$wrapper);
   			this.$usertype = $('#user_type', this.$wrapper);

			$('.field-url').on('click', function() { 
			    $(this).focus();
			    $(this).select();
			});

			this.$emails.on('change', function() { 
			    fn.$emails.val(fn.$emails.val().replace(/\s+/g, ''));
			});

			this.$emails.on('keypress keyup', function() { 
			    $('.help-block-error', fn.$emails.closest('.row')).remove();
			    fn.$emails.closest('.row').removeClass('has-error');
			});

			this.$form.on('submit', this.validate);
			this.$listform.on('submit', this.filter);

			this.drawChart();
			this.initFilter();
			this.initTab();
			
			Global.renderSelect2();
    	},

    	initTab: function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                common.initFooter();
            });
 		},

    	validateEmails: function(emails) {
			var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if ( !emails ) {
				return false;
			}

			var result = emails.split(',');
			for (var i = 0; i < result.length; i++) {
				if ( result[i] != '' ) {
					if ( !regex.test(result[i]) ) {
						return false;
					}
				}
			}

			return true;    		
    	},

		validate: function() {
			if ( !fn.validateEmails(fn.$emails.val()) ) {
				fn.$emails.closest('.row').removeClass('has-success').addClass('has-error');
				fn.$emails.focus();
				if ( !$('.help-block-error', fn.$emails.closest('.row')).length ) {
					fn.$emails.parent().after('<div class="help-block help-block-error">' + trans.message_failed_invalid_emails + '</div>');
				}

				return false;
			} else {
				fn.$emails.closest('.row').removeClass('has-error');
				$('.help-block-error', fn.$emails.closest('.row')).remove();
				return true;
			}
		},

		decimalAdjust: function(type, value, exp) {
			// If the exp is undefined or zero...
			if (typeof exp === 'undefined' || +exp === 0) {
				return Math[type](value);
			}

			value = +value;
			exp = +exp;
			// If the value is not a number or the exp is not an integer...
			if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
				return NaN;
			}

			// Shift
			value = value.toString().split('e');
			value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
			// Shift back
			value = value.toString().split('e');

			return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
		},		

		drawChart: function() {
            var config = {
            	type: 'doughnut',
		        data: {
		            datasets: [{
		            	data: [
			            	this.$chart.data('buyer'),
			            	this.$chart.data('freelancer')
		            	],
		                backgroundColor: [
		                    'rgb(255, 99, 132)',
		                    'rgb(54, 162, 235)'
		                ],
		            }],
		            labels: [
		            	this.$chart.data('buyer-label'),
		            	this.$chart.data('freelancer-label')
		            ]
		        },
		        options: {
		            responsive: true,
		            legend: {
						position: 'top',
					},
					animation: {
						animateScale: true,
						animateRotate: true
					}
		        }
            };
            var ctx = document.getElementById("chart_invitation").getContext("2d");
        	window.pieChart = new Chart(ctx, config);
	    },

		initFilter: function() {
	        this.$daterange.daterangepicker({
	            opens: 'right',
	            format: 'MM/DD/YYYY',
	            ranges: {
	              	'This Week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
	              	'This Month': [moment().startOf('month'), moment().endOf('month')],
	              	'This Year': [moment().startOf('year'), moment().endOf('year')],
	            },
	            separator: ' to ',
	            startDate: moment(this.$daterange.data('from')),
	            endDate: moment(this.$daterange.data('to')),
	        }, function(start, end) {
	            var s = start.format('MMM D, YYYY'); 
	            var e = end.format('MMM D, YYYY'); 
	            var str;

	            if (s == e) {
	              	str = s;
	            } else {
	              	str = s + ' - ' + e;
	            }

	            $('#date_range input').val(str);

	            fn.filter();
	        });

	        this.$usertype.on('change', function() {
	        	fn.filter();
	        });
		},

 		filter: function() {
			fn.$listform.ajaxSubmit({
				success: function(data) {
					$('tbody', fn.$listAffiliates).html(data);
				},
				error: function(xhr) {
					console.log(xhr);
				},

				dataType: 'html',
			});

			return false;
 		}
  	};

  	return fn;
});