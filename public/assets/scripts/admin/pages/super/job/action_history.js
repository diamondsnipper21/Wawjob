/**
 * @author KCG
 * @since July 13, 2017
 */

define(['page_user_common', 'common', 'ajax_datatable', 'bs-datepicker'], function (page_user_common, common) {
	var fn = {
		init: function() {
			page_user_common.init();

			this.initElements();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$form      = $('form.form-datatable');
            this.$container = $('#action_histories');
        },

		bindEvents: function() {
            var self = this;
		},

		render: function() {
            this.renderDataTable();
            this.renderSelect2();
            this.renderDateTimePicker();

            common.handleUniform();
		},

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
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