/**
 * @author KCG
 * @since July 23, 2017
 */

define(['common', 'moment', 'ajax_datatable', 'daterangepicker', 'jquery-form'], function (common, moment) {
    var fn = {
    	$form: null,

        init: function() {
        	this.$form = $('#frm_transactions_filter');

            this.render();
        },

        render: function() {
            this.renderGeneral();
            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();
            this.renderTypes();

            common.handleUniform();
        },

        renderGeneral: function() {
            $('#all').on('change', this.submit);
        },

        renderDataTable: function() {
            var self = this;

			$('.page-content').ajaxDatatable({
				success: function(html) {
					self.init();
				}
			});
        },

        renderDateTimePicker: function() {
			$('#date_range').daterangepicker({
				opens: 'right',
				format: 'MM/DD/YYYY',
				ranges: {
					'This Week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
				},
				separator: ' to ',
				startDate: moment(date_from),
				endDate: moment(date_to),
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

				fn.submit();
			}); 

	        // Previous, Next Range Link
	        $('.prev-unit, .next-unit').on('click', function() {
	        	$('#date_range input').val($(this).data('range'));
	        	fn.submit();

	        	return false;
	        });
        },

        renderTypes: function() {
	    	$('#transaction_type').on('change', this.submit);
	    },

		renderSelect2: function() {
            common.renderSelect2();

            // Users
            $('#user_id').select2({
                placeholder: 'Filter by users',
                minimumInputLength: 1,
                ajax: {
                    url: $('#user_id').data('ajax-url'),
                    dataType: 'json',
                    type: 'POST',
                    blockUI: false,
                    data: function (params) {
                        return {
                            term: params.term, // search term
                            page_limit: 10,
                            action: 'search_user'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.users
                        };
                    }
                },
                templateResult: function(user) {
                    if (!user.id || !user.fullname)
                        return user.text;

                    return user.fullname + ' - ' + user.username;
                },
                templateSelection: function(user) {
                    if (!user.id || !user.fullname)
                        return user.text;

                    return user.fullname;
                }
            });

            $('#user_id').on('change', this.submit);

            $('i.fa-times', $('.box-users')).on('click', function() {
                $('#user_id').val('');
                fn.submit();
            });

            $('#view_by').on('change', this.submit);
		},

		submit: function() {
            $('input[name="view"]').val($('#all').is(':checked') ? 'all' : '');
	    	fn.$form.submit();
	    },
    };

    return fn;
});