/**
 * @author KCG
 * @since July 13, 2017
 */
define(['/assets/scripts/pages/contract/contract_detail.js', 'page_user_common', 'common', 'ajax_datatable', 'bs-datepicker'], function (detailPage, page_user_common, common) {
	var fn = {
		init: function() {
            detailPage.init();
			page_user_common.init();

			this.initElements();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
        },

		bindEvents: function() {
		},

		render: function() {
            this.renderDataTable();
            this.renderSelect2();
            this.renderDateTimePicker();
		},

        renderDataTable: function() {
            var self = this;
            $('#action_histories').ajaxDatatable({
                success: function(html) {
                    self.initElements();
                    self.render();
                }
            });
        },

        renderDateTimePicker: function() {
            $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true,
                changeDate: function() {
                    // var $next = $(this).next();
                    // var $prev = $(this).prev();
                }
            });
        },

        renderSelect2: function() {
            common.renderSelect2();
        }
	};

	return fn;
});